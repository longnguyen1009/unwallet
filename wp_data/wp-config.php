<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'unwallet' );

/** Database username */
define( 'DB_USER', 'unwallet_user' );

/** Database password */
define( 'DB_PASSWORD', 'unwallet_password' );

/** Database hostname */
define( 'DB_HOST', 'db:3306' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'v$>D.>F|,BE(2dNfkddQJk2gyHu/tcByrjC;hnafb1$UGIHK]Lz6}s[K.5Pj|n{p' );
define( 'SECURE_AUTH_KEY',  'sBj}F]w%M$A*G%#_rC,u5u(L@!>M<uyW(IlHq0iChh}!l_mt|qvD.Oqn*M+Z&d|j' );
define( 'LOGGED_IN_KEY',    '?]e#iuq?itew7tP2{Gff~NRT[;,?m1}&>52&3Vtu[u^gEi~]2-X{0Ot=>zmnHB^B' );
define( 'NONCE_KEY',        'pwzJd0(;x`bm=Ak)l!W P$7iycW!Hb>J6[au[m$6hG>f8}H27N )ZL`:A+.%>J>%' );
define( 'AUTH_SALT',        '>GT#V# vaOE ZEps_da;H@gdNB[Y)9^_&G+0R1C5|)x4X3=PIi|K.X]b{G+R?N[O' );
define( 'SECURE_AUTH_SALT', 'Q]8``;>Q!6EPH5P}rboL##)(aXzABMU.erJ-sOZuVQ`xrybxtwR-1tLmJXp/:K<[' );
define( 'LOGGED_IN_SALT',   'zU5on{lDi,e.QRx0!DS:8.T 2*Qcn~Y/0i~A~rp.nuE+e5);2&a><[^F/#GB}[9]' );
define( 'NONCE_SALT',       'kIw4sZf}R21zHMw48e&ZBz,&;b{+la}.6O[>~!U2Nu1%IHbTKx2:AKFI(,$$3j}5' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
