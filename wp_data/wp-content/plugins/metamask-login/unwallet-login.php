<?php

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
