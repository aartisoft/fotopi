<?php
/**
 * Plugin Name: WPWeb Updater
 * Plugin URI: http://www.wpweb.co.in/
 * Description: WPWeb Updater - The license and updater plugin for all wpweb products
 * Version: 1.0.9
 * Author: WPWeb
 * Network: true
 * Author URI: http://www.wpweb.co.in/
 * Text Domain: wpwebupd
 * Domain Path: languages
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Basic plugin definitions 
 * 
 * @package WPWeb Updater
 * @since 1.0.0
 */
if( !defined( 'WPWEB_UPD_VERSION' ) ) {
	define( 'WPWEB_UPD_VERSION', '1.0.9' ); // plugin version
}
if( !defined( 'WPWEB_UPD_DIR' ) ) {
	define( 'WPWEB_UPD_DIR', dirname( __FILE__ ) ); // plugin dir
}
if( !defined( 'WPWEB_UPD_PLUGINS_DIR' ) ) {
	define( 'WPWEB_UPD_PLUGINS_DIR', dirname( dirname( __FILE__ ) ) ); // plugin dir
}
if( !defined( 'WPWEB_UPD_URL' ) ) {
	define( 'WPWEB_UPD_URL', plugin_dir_url( __FILE__ ) ); // plugin url
}
if( !defined( 'WPWEB_UPD_ADMIN' ) ) {
	define( 'WPWEB_UPD_ADMIN', WPWEB_UPD_DIR . '/includes/admin' ); // plugin admin dir
}

//Include misc functions file
require_once( WPWEB_UPD_DIR . '/includes/wpweb-upd-misc-functions.php' );

/**
 * Load Text Domain
 * 
 * This gets the plugin ready for translation.
 * 
 * @package WPWeb Updater
 * @since 1.0.0
 */
load_plugin_textdomain( 'wpwebupd', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

/**
 * Activation Hook
 * 
 * Register plugin activation hook.
 * 
 * @package WPWeb Updater
 * @since 1.0.0
 */
register_activation_hook( __FILE__, 'wpweb_upd_install' );

/**
 * Deactivation Hook
 * 
 * Register plugin deactivation hook.
 * 
 * @package WPWeb Updater
 * @since 1.0.0
 */
register_deactivation_hook( __FILE__, 'wpweb_upd_uninstall');

/**
 * Plugin Setup (On Activation)
 * 
 * Does the initial setup,
 * stest default values for the plugin options.
 * 
 * @package WPWeb Updater
 * @since 1.0.0
 */
function wpweb_upd_install() {
	//activation code here
}

/**
 * Plugin Setup (On Deactivation)
 * 
 * Delete  plugin options.
 * 
 * @package WPWeb Updater
 * @since 1.0.0
 */
function wpweb_upd_uninstall() {
	//deactivation code here
}

if( is_admin() ) {
	
	//Include admin class file
	require_once( WPWEB_UPD_DIR . '/includes/admin/class-wpweb-upd-admin.php' );
	
	$wpweb_upd_admin = new Wpweb_Upd_Admin();
	$wpweb_upd_admin->add_hooks();
	
	include_once( WPWEB_UPD_DIR . '/updates/class-plugin-update-checker.php' );
	
	$WPSUPDUpdateChecker = new WpwebPluginUpdateChecker(
		'http://wpweb.co.in/Updates/WPWUPD/info.json',
		__FILE__,
		'wpwupd'
	);
}

/**
 * Change plugin load order
 * 
 * Loads Updater plugin first
 * 
 * @package WPWeb Updater
 * @since 1.0.0
 */
if ( ! function_exists( 'wpweb_upd_plugin_first' ) ) {
    function wpweb_upd_plugin_first() {

        // ensure path to this file is via main wp plugin path
        $wp_path_to_this_file	= preg_replace( '/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", __FILE__ );
        $this_plugin			= plugin_basename( trim( $wp_path_to_this_file ) );
        $active_plugins			= get_option( 'active_plugins' );
        $this_plugin_key		= array_search( $this_plugin, $active_plugins );

        if( $this_plugin_key ) { // if it's 0 it's the first plugin already, no need to continue

            array_splice( $active_plugins, $this_plugin_key, 1 );
            array_unshift( $active_plugins, $this_plugin );
            update_option( 'active_plugins', $active_plugins );
        }
    }
}   
add_action( 'activated_plugin', 'wpweb_upd_plugin_first' );