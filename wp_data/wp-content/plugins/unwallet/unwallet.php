<?php
/**
 * Plugin Name:       unWallet Login
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            The WordPress Contributors
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       unwallet
 *
 * @package           create-block
 */

function create_block_unwallet_block_init() {

	register_block_type(
		plugin_dir_path( __FILE__ ) . 'build',
		array(
			'render_callback' => 'create_block_unwallet_render_callback',
		)
	);
}
add_action( 'init', 'create_block_unwallet_block_init' );


function create_block_unwallet_render_callback( $atts, $content, $block) {

	wp_enqueue_script('create-block-unwallet-view-script');
	ob_start();
	require plugin_dir_path( __FILE__ ) . 'build/template.php';
	return ob_get_clean();
}



// AJAX request for unWallet login
add_action( 'wp_ajax_unwalletlogin', 'unwallet_ajax_login' );
add_action( 'wp_ajax_nopriv_unwalletlogin', 'unwallet_ajax_login' );

function unwallet_ajax_login () {
	$address = isset( $_POST[ 'address' ] ) ? $_POST[ 'address' ] : '';

	$users = get_users([
			'meta_key' => 'unwallet_url',
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
}

// Extra function for edit user info
add_action('show_user_profile', 'my_user_profile_edit_unWallet_action');
add_action('edit_user_profile', 'my_user_profile_edit_unWallet_action');

add_action( 'personal_options_update', 'save_extra_user_profile_unwallet_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_unwallet_fields' );

function my_user_profile_edit_unWallet_action($user) {
	?>
	<h2>NFT</h2>
	<table class="form-table" role="presentation">
		<tbody>
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
	<?php
}

function save_extra_user_profile_unwallet_fields( $user_id ) {
	if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
		return;
	}

	if ( !current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}
	update_user_meta( $user_id, 'unwallet_url', $_POST['unwallet_url'] );
}
