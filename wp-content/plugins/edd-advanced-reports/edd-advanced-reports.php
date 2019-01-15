<?php
/**
 * Plugin Name:     Easy Digital Downloads - Advanced Reports
 * Plugin URI:      http://cpothemes.com
 * Description:     Provides tools to build your own custom reports for earnings, sales, and other data.
 * Version:         1.0.1
 * Author:          Manuel Vicedo
 * Author URI:      http://cpothemes.com
 * Text Domain:     edd-advanced-reports
 *
 * @package         EDD\EDD_Advanced_Reports
 * @author          Manuel Vicedo
 * @copyright       Copyright (c) Manuel Vicedo
 *
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'EDD_Advanced_Reports' ) ) {

    /**
     * Main EDD_Advanced_Reports class
     *
     * @since       1.0.0
     */
    class EDD_Advanced_Reports {

        /**
         * @var         EDD_Advanced_Reports $instance The one true EDD_Advanced_Reports
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true EDD_Advanced_Reports
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new EDD_Advanced_Reports();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'EDD_ADVANCED_REPORTS_VER', '1.0.1' );

            // Plugin path
            define( 'EDD_ADVANCED_REPORTS_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'EDD_ADVANCED_REPORTS_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include necessary files
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function includes() {
            //Include scripts
			require_once(EDD_ADVANCED_REPORTS_DIR.'includes/scripts.php');
            require_once(EDD_ADVANCED_REPORTS_DIR.'includes/post-types.php');
            require_once(EDD_ADVANCED_REPORTS_DIR.'includes/meta.php');
			require_once(EDD_ADVANCED_REPORTS_DIR.'includes/metadata.php');
			require_once(EDD_ADVANCED_REPORTS_DIR.'includes/reports.php');
			require_once(EDD_ADVANCED_REPORTS_DIR.'includes/database.php');
			require_once(EDD_ADVANCED_REPORTS_DIR.'includes/dashboard.php');
			require_once(EDD_ADVANCED_REPORTS_DIR.'includes/forms.php');
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         *
         */
        private function hooks() {
            //Register settings
            //add_filter( 'edd_settings_extensions', array( $this, 'settings' ), 1 );
			add_action('edd_reports_tabs', 'edd_advanced_reports_tab');
			add_action('edd_reports_tab_advanced_reports', 'edd_advanced_reports_page');

            //Handle licensing
            if( class_exists( 'EDD_License' ) ) {
                $license = new EDD_License( __FILE__, 'Advanced Reports', EDD_ADVANCED_REPORTS_VER, 'Manuel Vicedo' );
            }
        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = EDD_ADVANCED_REPORTS_DIR . '/languages/';
            $lang_dir = apply_filters( 'edd_advanced_reports_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'edd-advanced-reports' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'edd-advanced-reports', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR.'/edd-advanced-reports/'.$mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/edd-advanced-reports/ folder
                load_textdomain( 'edd-advanced-reports', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/edd-advanced-reports/languages/ folder
                load_textdomain( 'edd-advanced-reports', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'edd-advanced-reports', false, $lang_dir );
            }
        }


        /**
         * Add settings
         *
         * @access      public
         * @since       1.0.0
         * @param       array $settings The existing EDD settings array
         * @return      array The modified EDD settings array
         */
        public function settings( $settings ) {
            $new_settings = array(
                array(
                    'id'    => 'edd_advanced_reports_settings',
                    'name'  => '<strong>' . __( 'Advanced Reports Settings', 'edd-advanced-reports' ) . '</strong>',
                    'desc'  => __( 'Configure Advanced Reports Settings', 'edd-advanced-reports' ),
                    'type'  => 'header',
                )
            );
            return array_merge( $settings, $new_settings );
        }
    }
} // End if class_exists check


/**
 * The main function responsible for returning the one true EDD_Advanced_Reports
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \EDD_Advanced_Reports The one true EDD_Advanced_Reports
 *
 */
function EDD_Advanced_Reports_load() {
    if( ! class_exists( 'Easy_Digital_Downloads' ) ) {
        if( ! class_exists( 'EDD_Extension_Activation' ) ) {
            require_once 'includes/class.extension-activation.php';
        }

        $activation = new EDD_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
        $activation = $activation->run();
        return EDD_Advanced_Reports::instance();
    } else {
        return EDD_Advanced_Reports::instance();
    }
}
add_action( 'plugins_loaded', 'EDD_Advanced_Reports_load' );


/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class, since we are preferring the plugins_loaded
 * hook for compatibility, we also can't reference a function inside the plugin class
 * for the activation function. If you need an activation function, put it here.
 *
 * @since       1.0.0
 * @return      void
 */
function edd_advanced_reports_activation() {
    /* Activation functions here */
}
register_activation_hook( __FILE__, 'edd_advanced_reports_activation' );
