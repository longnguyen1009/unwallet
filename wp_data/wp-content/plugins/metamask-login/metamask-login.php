<?php
/*
 * Plugin name: MetaMask login
 * Version: 1.0
 * Author:
 */

require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . '/unwallet-login.php';

use Elliptic\EC;
use kornrunner\Keccak;

// AJAX request for Metamask login
add_action( 'wp_ajax_metamasklogin', 'metamask_ajax_login' );
add_action( 'wp_ajax_nopriv_metamasklogin', 'metamask_ajax_login' );

add_action( 'wp_footer', 'add_logout_script' );
add_action( 'admin_enqueue_scripts', 'add_logout_script' );

function add_logout_script() {
    wp_register_script( 'mm-logout', plugin_dir_url(__FILE__) . 'assets/logout.js', array(), time(), true );
    wp_localize_script(
        'mm-logout',
        'metamask_logout',
        array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        )
    );
    wp_enqueue_script('jquery');
    wp_enqueue_script( 'mm-logout' );
}

// Handle login by Metamask
function metamask_ajax_login() {

    $address = isset( $_POST[ 'address' ] ) ? $_POST[ 'address' ] : '';
    $message = isset( $_POST[ 'message' ] ) ? $_POST[ 'message' ] : '';
    $signature = isset( $_POST[ 'signature' ] ) ? $_POST[ 'signature' ] : '';
    if( verifySignature( $message, $signature, $address ) ) {
        $users = get_users([
                'meta_key' => 'eth_address',
                'meta_value' => $address,
                'number' => 1,
                'role' => 'subscriber',
            ]
        );
        if( ! is_wp_error( $users ) && ( $user = reset( $users ) ) ) {
            wp_clear_auth_cookie();
            wp_set_current_user( $user->ID );
            wp_set_auth_cookie( $user->ID );

            wp_send_json_success([
                'message' => 'OK',
                'user' => $user,
                'user_meta' => get_user_meta($user->ID),
            ]);
        } else {
            wp_send_json_error( new WP_Error( 'login_err', 'ユーザーが存在しません' ) );
        }
    } else {
        wp_send_json_error( new WP_Error( 'login_err', 'エラーになります' ) );
    }
}

// elliptic curves mathematics, authenticate address
function convertPubKeyToAddress( $pubkey ) {
    return "0x" . substr( Keccak::hash( substr( hex2bin( $pubkey->encode( "hex" ) ), 1 ), 256 ), 24 );
}
function verifySignature( $message, $signature, $address ) {
    try {
        $len = strlen( $message );

        // the message is prepended with \x19Ethereum Signed Message:\n<length of message>
        $hash = Keccak::hash("\x19Ethereum Signed Message:\n{$len}{$message}", 256);
        $sign = array(
            "r" => substr( $signature, 2, 64 ),
            "s" => substr( $signature, 66, 64 )
        );
        $recid = ord( hex2bin( substr( $signature, 130, 2 ) ) ) - 27;
        if( $recid != ( $recid & 1 ) ) {
            return false;
        }
        $ec = new EC( 'secp256k1' );
        $pubkey = $ec->recoverPubKey( $hash, $sign, $recid );
        return strtolower( $address ) == strtolower( convertPubKeyToAddress( $pubkey ) );
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}

// Extra function for edit user info

add_action('show_user_profile', 'my_user_profile_edit_action');
add_action('edit_user_profile', 'my_user_profile_edit_action');

add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

function my_user_profile_edit_action($user) {
    ?>
        <h2>NFT</h2>
        <table class="form-table" role="presentation">
            <tbody>
                <tr class="user-description-wrap">
                    <th><label>Etherium address</label></th>
                    <td>
                        <input type="text" name="eth_address" id="eth_address"
                               value="<?php echo esc_attr(get_the_author_meta( 'eth_address', $user->ID ) ); ?>"
                               class="regular-text" />
                    </td>
                </tr>
                <tr class="user-description-wrap">
                    <th><label>unWallet address</label></th>
                    <td>
                        <input type="text" name="unwallet_url" id="unwallet_url"
                               value="<?php echo esc_attr(get_the_author_meta( 'unwallet_url', $user->ID ) ); ?>"
                               class="regular-text" />
                    </td>
                </tr>
            </tbody>
        </table>
        <h2>Redirect URL</h2>
        <table class="form-table" role="presentation">
            <tbody>
            <tr class="user-description-wrap">
                <th><label>URL</label></th>
                <td>
                    <input type="text" name="redirect_url" id="redirect_url"
                           value="<?php echo esc_attr(get_the_author_meta( 'redirect_url', $user->ID ) ); ?>"
                           class="regular-text" />
                </td>
            </tr>
            </tbody>
        </table>
    <?php
}

function save_extra_user_profile_fields( $user_id ) {
    if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
        return;
    }

    if ( !current_user_can( 'edit_user', $user_id ) ) {
        return false;
    }
    update_user_meta( $user_id, 'eth_address', $_POST['eth_address'] );
    update_user_meta( $user_id, 'redirect_url', $_POST['redirect_url'] );
    update_user_meta( $user_id, 'unwallet_url', $_POST['unwallet_url'] );
}

// Logout action
add_action( 'wp_ajax_logout_metamask', 'logout_metamask' );
add_action( 'wp_ajax_nopriv_logout_metamask', 'logout_metamask' );

function logout_metamask() {
    $user = wp_get_current_user();

    wp_logout();

    // if ( ! empty( $_REQUEST['redirect_to'] ) ) {
    //     $redirect_to           = $_REQUEST['redirect_to'];
    //     $requested_redirect_to = $redirect_to;
    // } else {
    //     $redirect_to = add_query_arg(
    //         array(
    //             'loggedout' => 'true',
    //             'wp_lang'   => get_user_locale( $user ),
    //         ),
    //         wp_login_url()
    //     );
    //
    //     $requested_redirect_to = '';
    // }
    //
    // $redirect_to = apply_filters( 'logout_redirect', $redirect_to, $requested_redirect_to, $user );

    wp_send_json_success([
        'message' => 'OK',
        // 'user' => $user,
        // 'user_meta' => get_user_meta($user->ID),
        'redirec_to' => '/login',
    ]);
}

// // add style, script for login by unWallet
// add_action( 'login_enqueue_scripts', function() {
//     // wp_register_script( 'uw-login', plugin_dir_url( '' ) . 'metamask-login/assets/test-dist/unwallet.js', [], time(), true );
//     wp_register_script( 'uw-login', plugin_dir_url( '' ) . 'metamask-login/assets/unwallet.js', [], time(), true );
//     wp_enqueue_script('uw-login');
// } );

// function set_scripts_type_attribute( $tag, $handle, $src ) {
//     if ( 'uw-login' === $handle ) {
//         $tag = '<script type="module" src="'. $src .'"></script>';
//     }
//     return $tag;
// }
// add_filter( 'script_loader_tag', 'set_scripts_type_attribute', 10, 3 );
