<?php

/** Make sure that the WordPress bootstrap has run before continuing. */
// require __DIR__ . '/wp-load.php';

// add button login by MetaMask
add_action( 'login_form', function() {
    ?>
        <div id="mm-login">
            <a href="" class="button">Metamaskでログイン</a>
        </div>
    <?php
} );

add_action( 'login_form', function() {
    ?>
    <div id="uw-login">
        <a href="" class="button">unWalletでログイン</a>
    </div>
<?php
} );

// add style, script for login by Metamask
add_action( 'login_enqueue_scripts', function() {
    wp_enqueue_style( 'mm-login', plugin_dir_url( '' ) . 'metamask-login/assets/style.css' );
    wp_register_script( 'mm-login', plugin_dir_url( '' ) . 'metamask-login/assets/script.js', array(), time(), true );
    wp_localize_script(
        'mm-login',
        'metamask_login',
        array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            // 'message' => 'I want to login to Wordpress at ' . date( 'Y-m-d H:i:s' ) . '.',
        )
    );
    wp_enqueue_script( 'mm-login' );
} );

/**
 * Output the login page header.
 *
 * @since 2.1.0
 *
 * @global string      $error         Login error message set by deprecated pluggable wp_login() function
 *                                    or plugins replacing it.
 * @global bool|string $interim_login Whether interim login modal is being displayed. String 'success'
 *                                    upon successful login.
 * @global string      $action        The action that brought the visitor to the login page.
 *
 * @param string   $title    Optional. WordPress login Page title to display in the `<title>` element.
 *                           Default 'Log In'.
 * @param string   $message  Optional. Message to display in header. Default empty.
 * @param WP_Error $wp_error Optional. The error to pass. Default is a WP_Error instance.
 */
function login_header( $title = 'Log In', $message = '', $wp_error = null ) {
global $error, $interim_login, $action;

// Don't index any of these forms.
add_filter( 'wp_robots', 'wp_robots_sensitive_page' );
add_action( 'login_head', 'wp_strict_cross_origin_referrer' );

add_action( 'login_head', 'wp_login_viewport_meta' );

if ( ! is_wp_error( $wp_error ) ) {
    $wp_error = new WP_Error();
}

// Shake it!
$shake_error_codes = array( 'empty_password', 'empty_email', 'invalid_email', 'invalidcombo', 'empty_username', 'invalid_username', 'incorrect_password', 'retrieve_password_email_failure' );
/**
 * Filters the error codes array for shaking the login form.
 *
 * @since 3.0.0
 *
 * @param string[] $shake_error_codes Error codes that shake the login form.
 */
$shake_error_codes = apply_filters( 'shake_error_codes', $shake_error_codes );

if ( $shake_error_codes && $wp_error->has_errors() && in_array( $wp_error->get_error_code(), $shake_error_codes, true ) ) {
    add_action( 'login_footer', 'wp_shake_js', 12 );
}

$login_title = get_bloginfo( 'name', 'display' );

/* translators: Login screen title. 1: Login screen name, 2: Network or site name. */
$login_title = sprintf( __( '%1$s &lsaquo; %2$s &#8212; WordPress' ), $title, $login_title );

if ( wp_is_recovery_mode() ) {
    /* translators: %s: Login screen title. */
    $login_title = sprintf( __( 'Recovery Mode &#8212; %s' ), $login_title );
}

/**
 * Filters the title tag content for login page.
 *
 * @since 4.9.0
 *
 * @param string $login_title The page title, with extra context added.
 * @param string $title       The original page title.
 */
$login_title = apply_filters( 'login_title', $login_title, $title );

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
    <title><?php echo $login_title; ?></title>
    <?php

    wp_enqueue_style( 'login' );

    /*
     * Remove all stored post data on logging out.
     * This could be added by add_action('login_head'...) like wp_shake_js(),
     * but maybe better if it's not removable by plugins.
     */
    if ( 'loggedout' === $wp_error->get_error_code() ) {
        ?>
        <script>if("sessionStorage" in window){try{for(var key in sessionStorage){if(key.indexOf("wp-autosave-")!=-1){sessionStorage.removeItem(key)}}}catch(e){}};</script>
        <?php
    }

    /**
     * Enqueue scripts and styles for the login page.
     *
     * @since 3.1.0
     */
    do_action( 'login_enqueue_scripts' );

    /**
     * Fires in the login page header after scripts are enqueued.
     *
     * @since 2.1.0
     */
    do_action( 'login_head' );

    $login_header_url = __( 'https://wordpress.org/' );

    /**
     * Filters link URL of the header logo above login form.
     *
     * @since 2.1.0
     *
     * @param string $login_header_url Login header logo URL.
     */
    $login_header_url = apply_filters( 'login_headerurl', $login_header_url );

    $login_header_title = '';

    /**
     * Filters the title attribute of the header logo above login form.
     *
     * @since 2.1.0
     * @deprecated 5.2.0 Use {@see 'login_headertext'} instead.
     *
     * @param string $login_header_title Login header logo title attribute.
     */
    $login_header_title = apply_filters_deprecated(
        'login_headertitle',
        array( $login_header_title ),
        '5.2.0',
        'login_headertext',
        __( 'Usage of the title attribute on the login logo is not recommended for accessibility reasons. Use the link text instead.' )
    );

    $login_header_text = empty( $login_header_title ) ? __( 'Powered by WordPress' ) : $login_header_title;

    /**
     * Filters the link text of the header logo above the login form.
     *
     * @since 5.2.0
     *
     * @param string $login_header_text The login header logo link text.
     */
    $login_header_text = apply_filters( 'login_headertext', $login_header_text );

    $classes = array( 'login-action-' . $action, 'wp-core-ui' );

    if ( is_rtl() ) {
        $classes[] = 'rtl';
    }

    if ( $interim_login ) {
        $classes[] = 'interim-login';

        ?>
        <style type="text/css">html{background-color: transparent;}</style>
        <?php

        if ( 'success' === $interim_login ) {
            $classes[] = 'interim-login-success';
        }
    }

    $classes[] = ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_locale() ) ) );

    /**
     * Filters the login page body classes.
     *
     * @since 3.5.0
     *
     * @param string[] $classes An array of body classes.
     * @param string   $action  The action that brought the visitor to the login page.
     */
    $classes = apply_filters( 'login_body_class', $classes, $action );

    ?>
</head>
<body class="login no-js <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
<script type="text/javascript">
    document.body.className = document.body.className.replace('no-js','js');
</script>
<?php
/**
 * Fires in the login page header after the body tag is opened.
 *
 * @since 4.6.0
 */
do_action( 'login_header' );

?>
<div id="login">
    <h1>ユーザーログイン</h1>
    <?php
    /**
     * Filters the message to display above the login form.
     *
     * @since 2.1.0
     *
     * @param string $message Login message text.
     */
    $message = apply_filters( 'login_message', $message );

    if ( ! empty( $message ) ) {
        echo $message . "\n";
    }

    // In case a plugin uses $error rather than the $wp_errors object.
    if ( ! empty( $error ) ) {
        $wp_error->add( 'error', $error );
        unset( $error );
    }

    if ( $wp_error->has_errors() ) {
        $errors   = '';
        $messages = '';

        foreach ( $wp_error->get_error_codes() as $code ) {
            $severity = $wp_error->get_error_data( $code );
            foreach ( $wp_error->get_error_messages( $code ) as $error_message ) {
                if ( 'message' === $severity ) {
                    $messages .= '	' . $error_message . "<br />\n";
                } else {
                    $errors .= '	' . $error_message . "<br />\n";
                }
            }
        }

        if ( ! empty( $errors ) ) {
            /**
             * Filters the error messages displayed above the login form.
             *
             * @since 2.1.0
             *
             * @param string $errors Login error message.
             */
            echo '<div id="login_error">' . apply_filters( 'login_errors', $errors ) . "</div>\n";
        }

        if ( ! empty( $messages ) ) {
            /**
             * Filters instructional messages displayed above the login form.
             *
             * @since 2.5.0
             *
             * @param string $messages Login messages.
             */
            echo '<p class="message" id="login-message">' . apply_filters( 'login_messages', $messages ) . "</p>\n";
        }
    }
    } // End of login_header().

    /**
     * Outputs the footer for the login page.
     *
     * @since 3.1.0
     *
     * @global bool|string $interim_login Whether interim login modal is being displayed. String 'success'
     *                                    upon successful login.
     *
     * @param string $input_id Which input to auto-focus.
     */
    function login_footer( $input_id = '' ) {
    global $interim_login;

    // Don't allow interim logins to navigate away from the page.

    ?>
</div><?php // End of <div id="login">. ?>

<?php

if ( ! empty( $input_id ) ) {
    ?>
    <script type="text/javascript">
        try{document.getElementById('<?php echo $input_id; ?>').focus();}catch(e){}
        if(typeof wpOnload==='function')wpOnload();
    </script>
    <?php
}

/**
 * Fires in the login page footer.
 *
 * @since 3.1.0
 */
do_action( 'login_footer' );

?>
<div class="clear"></div>
</body>
</html>
<?php
}

/**
 * Outputs the JavaScript to handle the form shaking on the login page.
 *
 * @since 3.0.0
 */
function wp_shake_js() {
    ?>
    <script type="text/javascript">
        document.querySelector('form').classList.add('shake');
    </script>
    <?php
}

/**
 * Outputs the viewport meta tag for the login page.
 *
 * @since 3.7.0
 */
function wp_login_viewport_meta() {
    ?>
    <meta name="viewport" content="width=device-width" />
    <?php
}

/*
 * Main part.
 *
 * Check the request and redirect or display a form based on the current action.
 */

$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'login';
$errors = new WP_Error();

if ( isset( $_GET['key'] ) ) {
    $action = 'resetpass';
}

if ( isset( $_GET['checkemail'] ) ) {
    $action = 'checkemail';
}

$default_actions = array(
    'login',
    WP_Recovery_Mode_Link_Service::LOGIN_ACTION_ENTERED,
);

// Validate action so as to default to the login screen.
if ( ! in_array( $action, $default_actions, true ) && false === has_filter( 'login_form_' . $action ) ) {
    $action = 'login';
}

nocache_headers();

header( 'Content-Type: ' . get_bloginfo( 'html_type' ) . '; charset=' . get_bloginfo( 'charset' ) );

if ( defined( 'RELOCATE' ) && RELOCATE ) { // Move flag is set.
    if ( isset( $_SERVER['PATH_INFO'] ) && ( $_SERVER['PATH_INFO'] !== $_SERVER['PHP_SELF'] ) ) {
        $_SERVER['PHP_SELF'] = str_replace( $_SERVER['PATH_INFO'], '', $_SERVER['PHP_SELF'] );
    }

    $url = dirname( set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] ) );

    if ( get_option( 'siteurl' ) !== $url ) {
        update_option( 'siteurl', $url );
    }
}

// Set a cookie now to see if they are supported by the browser.
$secure = ( 'https' === parse_url( wp_login_url(), PHP_URL_SCHEME ) );
setcookie( TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN, $secure );

if ( SITECOOKIEPATH !== COOKIEPATH ) {
    setcookie( TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN, $secure );
}

if ( isset( $_GET['wp_lang'] ) ) {
    setcookie( 'wp_lang', sanitize_text_field( $_GET['wp_lang'] ), 0, COOKIEPATH, COOKIE_DOMAIN, $secure );
}

/**
 * Fires when the login form is initialized.
 *
 * @since 3.2.0
 */
do_action( 'login_init' );

/**
 * Fires before a specified login form action.
 *
 * The dynamic portion of the hook name, `$action`, refers to the action
 * that brought the visitor to the login form.
 *
 * Possible hook names include:
 *
 *  - `login_form_checkemail`
 *  - `login_form_confirm_admin_email`
 *  - `login_form_confirmaction`
 *  - `login_form_entered_recovery_mode`
 *  - `login_form_login`
 *  - `login_form_logout`
 *  - `login_form_lostpassword`
 *  - `login_form_postpass`
 *  - `login_form_register`
 *  - `login_form_resetpass`
 *  - `login_form_retrievepassword`
 *  - `login_form_rp`
 *
 * @since 2.8.0
 */
do_action( "login_form_{$action}" );

$http_post     = ( 'POST' === $_SERVER['REQUEST_METHOD'] );
$interim_login = isset( $_REQUEST['interim-login'] );

/**
 * Filters the separator used between login form navigation links.
 *
 * @since 4.9.0
 *
 * @param string $login_link_separator The separator used between login form navigation links.
 */
$login_link_separator = apply_filters( 'login_link_separator', ' | ' );

// Handle login
$secure_cookie   = '';
$customize_login = isset( $_REQUEST['customize-login'] );
$is_subscriber = true;

if ( $customize_login ) {
    wp_enqueue_script( 'customize-base' );
}
// If the user wants SSL but the session is not SSL, force a secure cookie.
if ( ! empty( $_POST['log'] ) && ! force_ssl_admin() ) {
    $user_name = sanitize_user( wp_unslash( $_POST['log'] ) );
    $user      = get_user_by( 'login', $user_name );

    if ( ! $user && strpos( $user_name, '@' ) ) {
        $user = get_user_by( 'email', $user_name );
    }

    if ( $user ) {
        if ( get_user_option( 'use_ssl', $user->ID ) ) {
            $secure_cookie = true;
            force_ssl_admin( true );
        }

        if (isset($user->roles) && count($user->roles) && !in_array('subscriber', $user->roles)) {
            $is_subscriber = false;
            $user = new WP_Error( 'login_err', 'そのようなユーザーはいません。確認してください' );
        }
    }
}

if ( isset( $_REQUEST['redirect_to'] ) ) {
    $redirect_to = $_REQUEST['redirect_to'];
    // Redirect to HTTPS if user wants SSL.
    if ( $secure_cookie && false !== strpos( $redirect_to, 'wp-admin' ) ) {
        $redirect_to = preg_replace( '|^http://|', 'https://', $redirect_to );
    }
} else {
    $redirect_to = get_home_url(); // Default page
}

$reauth = empty( $_REQUEST['reauth'] ) ? false : true;

if ($is_subscriber) {
    $user = wp_signon( array(), $secure_cookie );

    if (isset($user->ID)) {
        $user_meta = get_user_meta($user->ID);
        // If subscribers set redirect_url
        $redirect_to = !empty($user_meta['redirect_url'][0])
            ? preg_replace( '|^http://|', 'https://', $user_meta['redirect_url'][0])
            : $redirect_to;
    }
}


if ( empty( $_COOKIE[ LOGGED_IN_COOKIE ] ) && $is_subscriber ) {
    if ( headers_sent() ) {
        $user = new WP_Error(
            'test_cookie',
            sprintf(
            /* translators: 1: Browser cookie documentation URL, 2: Support forums URL. */
                __( '<strong>Error:</strong> Cookies are blocked due to unexpected output. For help, please see <a href="%1$s">this documentation</a> or try the <a href="%2$s">support forums</a>.' ),
                __( 'https://wordpress.org/documentation/article/cookies/' ),
                __( 'https://wordpress.org/support/forums/' )
            )
        );
    } elseif ( isset( $_POST['testcookie'] ) && empty( $_COOKIE[ TEST_COOKIE ] ) ) {
        // If cookies are disabled, the user can't log in even with a valid username and password.
        $user = new WP_Error(
            'test_cookie',
            sprintf(
            /* translators: %s: Browser cookie documentation URL. */
                __( '<strong>Error:</strong> Cookies are blocked or not supported by your browser. You must <a href="%s">enable cookies</a> to use WordPress.' ),
                __( 'https://wordpress.org/documentation/article/cookies/#enable-cookies-in-your-browser' )
            )
        );
    }
}

$requested_redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '';
/**
 * Filters the login redirect URL.
 *
 * @since 3.0.0
 *
 * @param string           $redirect_to           The redirect destination URL.
 * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
 * @param WP_User|WP_Error $user                  WP_User object if login was successful, WP_Error object otherwise.
 */
$redirect_to = apply_filters( 'login_redirect', $redirect_to, $requested_redirect_to, $user );

if ( ! is_wp_error( $user ) && ! $reauth ) {
    if ( $interim_login ) {
        $message       = '<p class="message">' . __( 'ログインに成功しました。' ) . '</p>';
        $interim_login = 'success';
        login_header( '', $message );

        ?>
        </div>
        <?php

        /** This action is documented in wp-login.php */
        do_action( 'login_footer' );

        if ( $customize_login ) {
            ?>
            <script type="text/javascript">setTimeout( function(){ new wp.customize.Messenger({ url: '<?php echo wp_customize_url(); ?>', channel: 'login' }).send('login') }, 1000 );</script>
            <?php
        }

        ?>
        </body></html>
        <?php

        exit;
    }

    // Check if it is time to add a redirect to the admin email confirmation screen.
    if ( is_a( $user, 'WP_User' ) && $user->exists() && $user->has_cap( 'manage_options' ) ) {
        $admin_email_lifespan = (int) get_option( 'admin_email_lifespan' );

        /*
         * If `0` (or anything "falsey" as it is cast to int) is returned, the user will not be redirected
         * to the admin email confirmation screen.
         */
        /** This filter is documented in wp-login.php */
        $admin_email_check_interval = (int) apply_filters( 'admin_email_check_interval', 6 * MONTH_IN_SECONDS );

        if ( $admin_email_check_interval > 0 && time() > $admin_email_lifespan ) {
            $redirect_to = add_query_arg(
                array(
                    'action'  => 'confirm_admin_email',
                    'wp_lang' => get_user_locale( $user ),
                ),
                wp_login_url( $redirect_to )
            );
        }
    }

    if ( ( empty( $redirect_to ) || 'wp-admin/' === $redirect_to || admin_url() === $redirect_to ) ) {
        // If the user doesn't belong to a blog, send them to user admin. If the user can't edit posts, send them to their profile.
        if ( is_multisite() && ! get_active_blog_for_user( $user->ID ) && ! is_super_admin( $user->ID ) ) {
            $redirect_to = user_admin_url();
        } elseif ( is_multisite() && ! $user->has_cap( 'read' ) ) {
            $redirect_to = get_dashboard_url( $user->ID );
        } elseif ( ! $user->has_cap( 'edit_posts' ) ) {
            $redirect_to = $user->has_cap( 'read' ) ? admin_url( 'profile.php' ) : home_url();
        }

        wp_redirect( $redirect_to );
        exit;
    }

    wp_safe_redirect( $redirect_to );
    exit;
}

$errors = $user;
// Clear errors if loggedout is set.
if ( ! empty( $_GET['loggedout'] ) || $reauth ) {
    $errors = new WP_Error();
}

if ( empty( $_POST ) && $errors->get_error_codes() === array( 'empty_username', 'empty_password' ) ) {
    $errors = new WP_Error( '', '' );
}

if ( $interim_login ) {
    if ( ! $errors->has_errors() ) {
        $errors->add( 'expired', __( 'Your session has expired. Please log in to continue where you left off.' ), 'message' );
    }
} else {
    // Some parts of this script use the main login form to display a message.
    if ( isset( $_GET['loggedout'] ) && $_GET['loggedout'] ) {
        $errors->add( 'loggedout', __( 'You are now logged out.' ), 'message' );
    } elseif ( isset( $_GET['registration'] ) && 'disabled' === $_GET['registration'] ) {
        $errors->add( 'registerdisabled', __( '<strong>Error:</strong> User registration is currently not allowed.' ) );
    } elseif ( strpos( $redirect_to, 'about.php?updated' ) ) {
        $errors->add( 'updated', __( '<strong>You have successfully updated WordPress!</strong> Please log back in to see what&#8217;s new.' ), 'message' );
    } elseif ( WP_Recovery_Mode_Link_Service::LOGIN_ACTION_ENTERED === $action ) {
        $errors->add( 'enter_recovery_mode', __( 'Recovery Mode Initialized. Please log in to continue.' ), 'message' );
    } elseif ( isset( $_GET['redirect_to'] ) && false !== strpos( $_GET['redirect_to'], 'wp-admin/authorize-application.php' ) ) {
        $query_component = wp_parse_url( $_GET['redirect_to'], PHP_URL_QUERY );
        $query           = array();
        if ( $query_component ) {
            parse_str( $query_component, $query );
        }

        if ( ! empty( $query['app_name'] ) ) {
            /* translators: 1: Website name, 2: Application name. */
            $message = sprintf( 'Please log in to %1$s to authorize %2$s to connect to your account.', get_bloginfo( 'name', 'display' ), '<strong>' . esc_html( $query['app_name'] ) . '</strong>' );
        } else {
            /* translators: %s: Website name. */
            $message = sprintf( 'Please log in to %s to proceed with authorization.', get_bloginfo( 'name', 'display' ) );
        }

        $errors->add( 'authorize_application', $message, 'message' );
    }
}

/**
 * Filters the login page errors.
 *
 * @since 3.6.0
 *
 * @param WP_Error $errors      WP Error object.
 * @param string   $redirect_to Redirect destination URL.
 */
$errors = apply_filters( 'wp_login_errors', $errors, $redirect_to );

// Clear any stale cookies.
if ( $reauth ) {
    wp_clear_auth_cookie();
}

login_header( __( 'Log In' ), '', $errors );

if ( isset( $_POST['log'] ) ) {
    $user_login = ( 'incorrect_password' === $errors->get_error_code() || 'empty_password' === $errors->get_error_code() ) ? esc_attr( wp_unslash( $_POST['log'] ) ) : '';
}

$rememberme = ! empty( $_POST['rememberme'] );

$aria_describedby = '';
$has_errors       = $errors->has_errors();

if ( $has_errors ) {
    $aria_describedby = ' aria-describedby="login_error"';
}

if ( $has_errors && 'message' === $errors->get_error_data() ) {
    $aria_describedby = ' aria-describedby="login-message"';
}

wp_enqueue_script( 'user-profile' );
?>

<form name="loginform" id="loginform" action="<?php echo esc_url( '/login/', 'login_post' ); ?>" method="post">
    <p>
        <label for="user_login"><?php _e( 'ユーザー名またはメールアドレス' ); ?></label>
        <input type="text" name="log" id="user_login"<?php echo $aria_describedby; ?> class="input" value="<?php echo esc_attr( $user_login ); ?>" size="20" autocapitalize="off" autocomplete="username" />
    </p>

    <div class="user-pass-wrap">
        <label for="user_pass"><?php _e( 'パスワード' ); ?></label>
        <div class="wp-pwd">
            <input type="password" name="pwd" id="user_pass"<?php echo $aria_describedby; ?> class="input password-input" value="" size="20" autocomplete="current-password" spellcheck="false" />
            <button type="button" class="button button-secondary wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Show password' ); ?>">
                <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
            </button>
        </div>
    </div>
    <?php

    /**
     * Fires following the 'Password' field in the login form.
     *
     * @since 2.1.0
     */
    do_action( 'login_form' );

    ?>
    <p class="submit">
        <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e( 'ログイン' ); ?>" />
        <?php

        if ( $interim_login ) {
            ?>
            <input type="hidden" name="interim-login" value="1" />
            <?php
        } else {
            ?>
            <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
            <?php
        }

        if ( $customize_login ) {
            ?>
            <input type="hidden" name="customize-login" value="1" />
            <?php
        }

        ?>
        <input type="hidden" name="testcookie" value="1" />
    </p>
</form>

<?php

$login_script  = 'function wp_attempt_focus() {';
$login_script .= 'setTimeout( function() {';
$login_script .= 'try {';

if ( $user_login ) {
    $login_script .= 'd = document.getElementById( "user_pass" ); d.value = "";';
} else {
    $login_script .= 'd = document.getElementById( "user_login" );';

    if ( $errors->get_error_code() === 'invalid_username' ) {
        $login_script .= 'd.value = "";';
    }
}

$login_script .= 'd.focus(); d.select();';
$login_script .= '} catch( er ) {}';
$login_script .= '}, 200);';
$login_script .= "}\n"; // End of wp_attempt_focus().

/**
 * Filters whether to print the call to `wp_attempt_focus()` on the login screen.
 *
 * @since 4.8.0
 *
 * @param bool $print Whether to print the function call. Default true.
 */
if ( apply_filters( 'enable_login_autofocus', true ) && ! $error ) {
    $login_script .= "wp_attempt_focus();\n";
}

// Run `wpOnload()` if defined.
$login_script .= "if ( typeof wpOnload === 'function' ) { wpOnload() }";

?>
<script type="text/javascript">
    <?php echo $login_script; ?>
</script>
<?php

if ( $interim_login ) {
    ?>
    <script type="text/javascript">
        ( function() {
            try {
                var i, links = document.getElementsByTagName( 'a' );
                for ( i in links ) {
                    if ( links[i].href ) {
                        links[i].target = '_blank';
                        links[i].rel = 'noopener';
                    }
                }
            } catch( er ) {}
        }());
    </script>
    <?php
}

login_footer();
