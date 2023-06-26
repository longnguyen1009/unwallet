<?php
$idToken = isset( $_POST[ 'id_token' ] ) ? $_POST[ 'id_token' ] : '';
if (!$idToken) {
    var_dump('error');
    die;
}
/*
    eyJraWQiOiIxIiwiYWxnIjoiRVMyNTYifQ.eyJhbXI
    iOltdLCJlbWFpbCI6bnVsbCwiaXNzIjoiaHR0cHM6Ly9pZ
    C51bndhbGxldC5kZXYiLCJzdWIiOiIweDQwMWQwQzZEMWU0OUQ3RD
    g0Njk1YTY2MzAyQTM5MzBBOTc0QTVjMjQiLCJhdWQiOlsiMTMwMzA0
    NzQ3MzE2OTg3Il0sImV4cCI6MTY4Njg5ODQzOCwiaWF0IjoxNjg2OD
    EyMDM4LCJub25jZSI6IjM0ZjY1MDg5NmIxNDE2ZGUwY2NkZDUzY2UzN
    zVhOGU0In0.qmJIHToEyzJ8gE0X_gAkhmwv_fvmwhnWztwNPW_djpwx
    L884FbTpxvLmpRwpKZbZgIfdq_lD6GfnNtH_WgCOjw
*/

$idTokenParts = explode('.', $idToken);
$address = json_decode(base64_decode($idTokenParts[1]), true)['sub'];

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

    $user_meta = get_user_meta($user->ID);
    $redirect_to = !empty($user_meta['redirect_url'][0])
        ? preg_replace( '|^http://|', 'https://', $user_meta['redirect_url'][0])
        : get_home_url();

    // wp_send_json_success([
    //     'message' => 'OK',
    //     'user' => $user,
    //     'user_meta' => get_user_meta($user->ID),
    // ]);
} else {
    // wp_send_json_error( new WP_Error( 'login_err', 'ユーザーが存在しません' ) );
    $error_message = 'ユーザーが存在しません';
    $redirect_to = '/login' . '?login_error=' . urlencode($error_message);
    // new WP_Error( 'login_err', 'ユーザーが存在しません' )
}

wp_safe_redirect( $redirect_to );

