<?php

/**
 * Plugin Name: Easy Digital Downloads - Download Image Watermark
 * Plugin URI: https://easydigitaldownloads.com/extensions/
 * Description: Display Watermark images on Easy Digital Download images.
 * Version: 1.0.6
 * Author: WPWeb
 * Author URI: http://wpweb.co.in
 * Text Domain: eddimgwtm
 * Domain Path: languages
 * 
 * @package Easy Digital Downloads - Image Watermark
 * @category Core
 * @author WPWeb
 */
/**
 * Basic plugin definitions 
 * 
 * @package Easy Digital Downloads - Image Watermark
 * @since 1.0.0
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

if (!defined('EDD_IMG_WTM_VERSION')) {
    define('EDD_IMG_WTM_VERSION', '1.0.6'); // Plugin version
}
if (!defined('EDD_IMG_WTM_URL')) {
    define('EDD_IMG_WTM_URL', plugin_dir_url(__FILE__)); // plugin url
}
if (!defined('EDD_IMG_WTM_DIR')) {
    define('EDD_IMG_WTM_DIR', dirname(__FILE__)); // plugin dir
}
if (!defined('EDD_IMG_WTM_ADMIN')) {
    define('EDD_IMG_WTM_ADMIN', EDD_IMG_WTM_DIR . '/includes/admin'); // plugin admin dir
}
if (!defined('EDD_IMG_WTM_BACKUP_PREFIX')) {
    define('EDD_IMG_WTM_BACKUP_PREFIX', '_edd_img_wtm_');
}
if (!defined('EDD_IMG_WTM_MAIN_POSTTYPE')) { // Plugin main post type
    define('EDD_IMG_WTM_MAIN_POSTTYPE', 'download');
}
if (!defined('EDD_IMG_WTM_BASENAME')) {
    define('EDD_IMG_WTM_BASENAME', basename(EDD_IMG_WTM_DIR)); // base name
}
if (!defined('EDD_IMG_WTM_PLUGIN_KEY')) {
    define('EDD_IMG_WTM_PLUGIN_KEY', 'eddimgwtm');
}

// Required Wpweb updater functions file
if (!function_exists('wpweb_updater_install')) {
    require_once( 'includes/wpweb-upd-functions.php' );
}

/**
 * Admin notices
 *
 * @package Easy Digital Downloads - Image Watermark
 * @since 1.1.2
 */
function edd_img_wtm_admin_notices() {

    if (!class_exists('Easy_Digital_Downloads')) {

        echo '<div class="error">';
        echo "<p><strong>" . __('Easy Digital Downloads needs to be activated to be able to use the Download Image Watermark.', 'eddimgwtm') . "</strong></p>";
        echo '</div>';
    }
}

/**
 * Check Easy Digital Downloads Plugin
 *
 * Handles to check Easy Digital Downloads plugin
 * if not activated then deactivate our plugin
 *
 * @package Easy Digital Downloads - Image Watermark
 * @since 1.1.2
 */
function edd_img_wtm_check_activation() {

    if (!class_exists('Easy_Digital_Downloads')) {
        // is this plugin active?
        if (is_plugin_active(plugin_basename(__FILE__))) {
            // deactivate the plugin
            deactivate_plugins(plugin_basename(__FILE__));
            // unset activation notice
            unset($_GET['activate']);
            // display notice
            add_action('admin_notices', 'edd_img_wtm_admin_notices');
        }
    }
}

//Check Easy Digital Downloads plugin is Activated or not
add_action('admin_init', 'edd_img_wtm_check_activation');

//check easy digital downloads is activated or not
if (class_exists('Easy_Digital_Downloads')) {

    // loads the Misc Functions file
    require_once ( EDD_IMG_WTM_DIR . '/includes/edd-img-wtm-misc-functions.php' );

    /**
     * Activation Hook
     *
     * Register plugin activation hook.
     *
     * @package Easy Digital Downloads - Image Watermark
     * @since 1.0.0
     */
    register_activation_hook(__FILE__, 'edd_img_wtm_install');

    /**
     * Plugin Setup (On Activation)
     *
     * Does the initial setup,
     * stest default values for the plugin options.
     *
     * @package Easy Digital Downloads - Image Watermark
     * @since 1.0.0
     */
    function edd_img_wtm_install() {

        global $wpdb, $edd_options;

        $udpopt = false;

        $img_types = edd_img_wtm_get_types();
        foreach ($img_types as $img_type) {

            //check watermark image not set
            if (!isset($edd_options['edd_img_wtm_' . $img_type . '_img'])) {
                $edd_options['edd_img_wtm_' . $img_type . '_img'] = '';
                $udpopt = true;
            }//end if
            //check watermark image alignment not 
            if (!isset($edd_options['edd_img_wtm_' . $img_type . '_align'])) {
                $edd_options['edd_img_wtm_' . $img_type . '_align'] = '';
                $udpopt = true;
            } //end if
        }
        //check need to update the defaults value to options
        if ($udpopt == true) { // if any of the settings need to be updated 				
            update_option('edd_settings', $edd_options);
        }
    }

} //end if to check class Easy_Digital_Downloads exist

/**
 * Load Text Domain
 *
 * This gets the plugin ready for translation.
 *
 * @package Easy Digital Downloads - Image Watermark
 * @since 1.1.2
 */
function edd_img_wtm_load_text_domain() {

    // Set filter for plugin's languages directory
    $edd_img_wtm_lang_dir = dirname(plugin_basename(__FILE__)) . '/languages/';
    $edd_img_wtm_lang_dir = apply_filters('edd_img_wtm_languages_directory', $edd_img_wtm_lang_dir);

    // Traditional WordPress plugin locale filter
    $locale = apply_filters('plugin_locale', get_locale(), 'eddimgwtm');
    $mofile = sprintf('%1$s-%2$s.mo', 'eddimgwtm', $locale);

    // Setup paths to current locale file
    $mofile_local = $edd_img_wtm_lang_dir . $mofile;
    $mofile_global = WP_LANG_DIR . '/' . EDD_IMG_WTM_BASENAME . '/' . $mofile;

    if (file_exists($mofile_global)) { // Look in global /wp-content/languages/edd-download-image-watermark folder
        load_textdomain('eddimgwtm', $mofile_global);
    } elseif (file_exists($mofile_local)) { // Look in local /wp-content/plugins/edd-download-image-watermark/languages/ folder
        load_textdomain('eddimgwtm', $mofile_local);
    } else { // Load the default language files
        load_plugin_textdomain('eddimgwtm', false, $edd_img_wtm_lang_dir);
    }
}

/**
 * Add plugin action links
 *
 * Adds a Settings, Docs link to the plugin list.
 *
 * @package Easy Digital Downloads - Image Watermark
 * @since 1.1.1
 */
function edd_img_wtm_add_plugin_links($links) {
    $plugin_links = array(
        '<a href="edit.php?post_type=download&page=edd-settings&tab=extensions">' . __('Settings', 'eddimgwtm') . '</a>',
        '<a href="http://wpweb.co.in/documents/edd-download-image-watermark/">' . __('Docs', 'eddimgwtm') . '</a>'
    );

    return array_merge($plugin_links, $links);
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'edd_img_wtm_add_plugin_links');

//add action to load plugin
add_action('plugins_loaded', 'edd_img_wtm_plugin_loaded');

/**
 * Load Plugin
 * 
 * Handles to load plugin after
 * dependent plugin is loaded 
 * successfully
 *
 * @package Easy Digital Downloads - Image Watermark
 * @since 1.0.0
 * */
function edd_img_wtm_plugin_loaded() {

    //check easy digital downloads is activated or not
    if (class_exists('Easy_Digital_Downloads')) {

        // load first plugin text domain
        edd_img_wtm_load_text_domain();

        /* //check EDD_License class is exist
          if( class_exists( 'EDD_License' ) ) {

          // Instantiate the licensing / updater. Must be placed in the main plugin file
          $license = new EDD_License( __FILE__, 'Download Image Watermark', EDD_IMG_WTM_VERSION, 'WPWeb' );
          } */

        /**
         * Deactivation Hook
         *
         * Register plugin deactivation hook.
         *
         * @package Easy Digital Downloads - Image Watermark
         * @since 1.0.0
         */
        register_deactivation_hook(__FILE__, 'edd_img_wtm_uninstall');

        /**
         * Plugin Setup (On Deactivation)
         *
         * Delete  plugin options.
         *
         * @package Easy Digital Downloads - Image Watermark
         * @since 1.0.0
         */
        function edd_img_wtm_uninstall() {

            global $wpdb, $edd_options;
        }

        /**
         * Includes Files
         * 
         * Includes some required files for plugin
         *
         * @package Easy Digital Downloads - Image Watermark
         * @since 1.0.0
         */
        global $edd_img_wtm_model, $edd_img_wtm_scripts, $edd_img_wtm_admin, $edd_img_wtm_public;

        //Model Class for generic functions
        require_once( EDD_IMG_WTM_DIR . '/includes/class-edd-img-wtm-model.php' );
        $edd_img_wtm_model = new EDD_Img_Wtm_Model();

        //Scripts Class for scripts / styles
        require_once( EDD_IMG_WTM_DIR . '/includes/class-edd-img-wtm-scripts.php' );
        $edd_img_wtm_scripts = new EDD_Img_Wtm_Scripts();
        $edd_img_wtm_scripts->add_hooks();

        //Admin Pages Class for admin side
        require_once( EDD_IMG_WTM_ADMIN . '/class-edd-img-wtm-admin.php' );
        $edd_img_wtm_admin = new EDD_Img_Wtm_Admin();
        $edd_img_wtm_admin->add_hooks();

        //Public Pages Class for public side
        require_once( EDD_IMG_WTM_DIR . '/includes/class-edd-img-wtm-public.php' );
        $edd_img_wtm_public = new EDD_Img_Wtm_Public();
        $edd_img_wtm_public->add_hooks();

        $current_user = get_current_user_id();

        $edd_img_wtm_user_enable = get_the_author_meta('edd_img_wtm_enable', $current_user);
        $edd_img_wtm_user_enable = !empty($edd_img_wtm_user_enable) ? $edd_img_wtm_user_enable : '';

        if ($edd_img_wtm_user_enable != '1') {
            //add filter for generate image watermark 
            add_filter('wp_generate_attachment_metadata', array($edd_img_wtm_admin, 'edd_generate_image_watermark'));
        }
    }//end if to check class Easy_Digital_Downloads is exist or not
}

//end if to check plugin loaded is called or not

if (class_exists('Wpweb_Upd_Admin')) { //check WPWEB Updater is activated
    // Plugin updates
    wpweb_queue_update(plugin_basename(__FILE__), EDD_IMG_WTM_PLUGIN_KEY);

    /**
     * Include Auto Updating Files
     * 
     * @package Easy Digital Downloads - Image Watermark
     * @since 1.0.0
     */
    require_once( WPWEB_UPD_DIR . '/updates/class-plugin-update-checker.php' ); // auto updating

    $WpwebEddImgWtmUpdateChecker = new WpwebPluginUpdateChecker(
            'http://wpweb.co.in/Updates/EDDIMGWTM/license-info.php', __FILE__, EDD_IMG_WTM_PLUGIN_KEY
    );

    /**
     * Auto Update
     * 
     * Get the license key and add it to the update checker.
     * 
     * @package Easy Digital Downloads - Image Watermark
     * @since 1.0.0
     */
    function edd_img_wtm_add_secret_key($query) {

        $plugin_key = EDD_IMG_WTM_PLUGIN_KEY;

        $query['lickey'] = wpweb_get_plugin_purchase_code($plugin_key);
        return $query;
    }

    $WpwebEddImgWtmUpdateChecker->addQueryArgFilter('edd_img_wtm_add_secret_key');
} // end check WPWeb Updater is activated