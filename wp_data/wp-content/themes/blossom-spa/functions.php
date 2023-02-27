<?php
/**
 * Blossom Spa functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Blossom_Spa
 */

//define theme version
$blossom_spa_theme_data = wp_get_theme();
if( ! defined( 'BLOSSOM_SPA_THEME_VERSION' ) ) define( 'BLOSSOM_SPA_THEME_VERSION', $blossom_spa_theme_data->get( 'Version' ) );
if( ! defined( 'BLOSSOM_SPA_THEME_NAME' ) ) define( 'BLOSSOM_SPA_THEME_NAME', $blossom_spa_theme_data->get( 'Name' ) );
if( ! defined( 'BLOSSOM_SPA_THEME_TEXTDOMAIN' ) ) define( 'BLOSSOM_SPA_THEME_TEXTDOMAIN', $blossom_spa_theme_data->get( 'TextDomain' ) ); 

/**
 * Custom Functions.
 */
require get_template_directory() . '/inc/custom-functions.php';

/**
 * Standalone Functions.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Template Functions.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Custom functions for selective refresh.
 */
require get_template_directory() . '/inc/partials.php';

/**
 * Fontawesome
 */
require get_template_directory() . '/inc/fontawesome.php';

/**
 * Custom Controls
 */
require get_template_directory() . '/inc/custom-controls/custom-control.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer/customizer.php';

/**
 * Widgets
 */
require get_template_directory() . '/inc/widgets.php';

/**
 * Metabox
 */
require get_template_directory() . '/inc/metabox.php';

/**
 * Dynamic Styles
 */
require get_template_directory() . '/css/style.php';

/**
 * Typography Functions
 */
require get_template_directory() . '/inc/typography.php';

/**
 * Plugin Recommendation
*/
require get_template_directory() . '/inc/tgmpa/recommended-plugins.php';

/**
 * Add theme compatibility function for woocommerce if active
*/
if( blossom_spa_is_woocommerce_activated() ){
    require get_template_directory() . '/inc/woocommerce-functions.php';    
}

/**
 * Toolkit Filters
*/
if( blossom_spa_is_bttk_activated() ) {
	require get_template_directory() . '/inc/toolkit-functions.php';
}

/**
 * Getting Started
*/
require get_template_directory() . '/inc/getting-started/getting-started.php';

/**
 * Implement Local Font Method functions.
 */
require get_template_directory() . '/inc/class-webfont-loader.php';



/**
 * Enqueue styles.
 *
 * @since add_customize_style
 *
 * @return void
 */
function add_customize_style() {
		// Register theme stylesheet.
		$theme_version = wp_get_theme()->get( 'Version' );
		$version_string = is_string( $theme_version ) ? $theme_version : false;
		wp_register_style('bootstrap-style', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css');
		wp_register_style('normalize-style', get_template_directory_uri() . '/css/normalize.css',array(),$version_string);
		wp_register_style('customize-style', get_template_directory_uri() . '/css/customize.css', array(),$version_string);

		// Enqueue theme stylesheet.
		wp_enqueue_style( 'bootstrap-style' );
		wp_enqueue_style( 'normalize-style' );
		wp_enqueue_style( 'customize-style' );
}

add_action( 'wp_enqueue_scripts', 'add_customize_style' );