<?php
/*
Plugin Name: Easy Digital Downloads - Fraud Monitor
Plugin URI: https://easydigitaldownloads.com/extensions/edd-fraud-monitor/
Description: An EDD extension that auto detects possible fraud based on some pre-defined rules and puts any payment that gets caught as potential fraud in a moderation queue
Version: 1.1.2
Author: Easy Digital Downloads
Author URI: https://easydigitaldownloads.com
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'EDD_FM_VERSION', '1.1.2' );

if ( class_exists( 'EDD_License' ) ) {
	$edd_fraud_monitor_license = new EDD_License( __FILE__, 'Fraud Monitor', EDD_FM_VERSION, 'Easy Digital Downloads', 'fraud_monitor_license' );
}


if ( ! class_exists( 'EDD_Fraud_Monitor' ) ) :

	final class EDD_Fraud_Monitor {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @var object
		 * @static
		 * @since 1.0
		 */
		private static $instance;

		/**
		 * The count of pending fraud payments
		 * @since  1.1
		 * @var null
		 */
		private $pending_count = null;

		/**
		 * Main Instance
		 *
		 * Ensures that only one instance exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 *
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EDD_Fraud_Monitor ) ) {
				self::$instance = new EDD_Fraud_Monitor;
			}

			return self::$instance;
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.0
		 * @access private
		 * @see EDD_Fraud_Monitor::init()
		 * @see EDD_Fraud_Monitor::activation()
		 */
		private function __construct() {
			$this->setup_globals();
			$this->includes();
			$this->hooks();
			$this->init();
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.0
		 * @access public
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Globals
		 *
		 * @since 1.0
		 * @return void
		 */
		private function setup_globals() {
			$this->title = 'EDD Fraud Monitor';

			// constants
			if ( ! defined( 'EDD_FM_PLUGIN_FILE' ) )
				define( 'EDD_FM_PLUGIN_FILE', __FILE__ );

			if ( ! defined( 'EDD_FM_PLUGIN_URL' ) )
				define( 'EDD_FM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

			if ( ! defined( 'EDD_FM_PLUGIN_DIR' ) )
				define( 'EDD_FM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

			// paths
			$this->file         = __FILE__;
			$this->basename     = apply_filters( 'edd_fm_plugin_basenname', plugin_basename( $this->file ) );
			$this->plugin_dir   = apply_filters( 'edd_fm_plugin_dir_path',  plugin_dir_path( $this->file ) );

			$this->plugin_url   = apply_filters( 'edd_fm_plugin_dir_url',   plugin_dir_url ( $this->file ) );
		}

		private function includes() {

			include EDD_FM_PLUGIN_DIR . 'includes/class-fraud-check.php';
			include EDD_FM_PLUGIN_DIR . 'includes/email-functions.php';
			include EDD_FM_PLUGIN_DIR . 'includes/ip-functions.php';
			include EDD_FM_PLUGIN_DIR . 'includes/classes/class-distance-calculation.php';
			include EDD_FM_PLUGIN_DIR . 'includes/classes/class-geofence-check.php';
			include EDD_FM_PLUGIN_DIR . 'includes/classes/class-geocode-api.php';

			if ( is_admin() ) {
				include EDD_FM_PLUGIN_DIR . 'includes/admin/class-tools.php';
				include EDD_FM_PLUGIN_DIR . 'includes/admin/payment-details.php';
				include EDD_FM_PLUGIN_DIR . 'includes/admin/payment-filters.php';
			}

		}

		/**
		 * Function fired on init
		 *
		 * This function is called on WordPress 'init'. It's triggered from the
		 * constructor function.
		 *
		 * @since 1.0
		 * @access public
		 *
		 * @uses EDD_Fraud_Monitor::load_textdomain()
		 *
		 * @return void
		 */
		public function init() {
			do_action( 'edd_fm_before_init' );

			$this->load_textdomain();

			if ( is_admin() ) {
				EDD_Fraud_Monitor_Tools::instance();
			}

			do_action( 'edd_fm_after_init' );

		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function hooks() {
			add_action( 'admin_init', array( $this, 'activation' ) );

			add_action( 'init', array( $this, 'post_status' ) );
			add_filter( 'edd_payment_statuses', array( $this, 'register_edd_post_status' ) );


			add_action( 'admin_enqueue_scripts', array( $this, 'load_custom_scripts' ) );

			add_action( 'edd_add_email_tags', array( $this, 'register_email_tags' ) );

			// check for fraud
			add_action( 'edd_insert_payment', array( $this, 'check_for_fraud_on_payment' ), 10, 2 );

			// check IP during file download
			add_filter( 'edd_file_download_has_access', array( $this, 'check_file_download' ), 99, 3 );

			// prevent purchase if fraud
			add_filter( 'edd_should_update_payment_status', array( $this, 'prevent_purchase' ), 11, 4 );

			// Remove the fraud flag when editing a purchase (if not fraud)
			add_action( 'edd_update_payment_status', array( $this, 'remove_fraud_flag_on_update' ), 10, 3 );
			add_action( 'edd_clear_fraud_flag', array( $this, 'remove_fraud_flag_only' ), 10 );

			// Remove the fraud flag when clicking the remove link
			add_action( 'edd_not_fraud', array( $this, 'remove_fraud_flag_on_click' ), 10 );
			add_action( 'edd_is_fraud', array( $this, 'remove_fraud_notice_on_click' ), 10 );
			add_action( 'edd_manual_fraud', array( $this, 'manually_flag_payment' ), 10, 1 );

			// admin menu
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 10 );

			// admin messages
			add_action( 'admin_notices', array( $this, 'admin_message' ) );

			add_filter( 'edd_should_process_refund', array( $this, 'process_refund' ), 10, 2 );
			add_filter( 'edds_should_process_refund', array( $this, 'allow_stripe_refund' ), 10, 4 );

			add_action( 'edd_fm_payment_confirmed_as_fraud', array( $this, 'leave_customer_fraud_note' ), 10, 1 );

			// insert actions
			do_action( 'edd_fm_setup_actions' );

		}



		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.0
		 * @return void
		 */
		public function load_textdomain() {
			// Set filter for plugin's languages directory
			$lang_dir = dirname( plugin_basename( $this->file ) ) . '/languages/';
			$lang_dir = apply_filters( 'edd_fm_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale        = apply_filters( 'plugin_locale',  get_locale(), 'edd-fm' );
			$mofile        = sprintf( '%1$s-%2$s.mo', 'edd-fm', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/edd-fraud-moderation/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				load_textdomain( 'edd-fm', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				load_textdomain( 'edd-fm', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'edd-fm', false, $lang_dir );
			}
		}

		public function load_custom_scripts() {
			wp_register_style( 'eddfm_admin_css', EDD_FM_PLUGIN_URL . 'assets/css/admin-styles.css', false, EDD_FM_VERSION );
			wp_enqueue_style( 'eddfm_admin_css' );

			wp_register_script( 'eddfm_admin_scripts', EDD_FM_PLUGIN_URL . 'assets/js/admin-scripts.js', array( 'jquery' ), EDD_FM_VERSION );
			wp_enqueue_script( 'eddfm_admin_scripts' );
		}

		/**
		 * Activation function fires when the plugin is activated.
		 *
		 * This function is fired when the activation hook is called by WordPress,
		 * it flushes the rewrite rules and disables the plugin if EDD isn't active
		 * and throws an error.
		 *
		 * @since 1.0
		 * @access public
		 *
		 * @return void
		 */
		public function activation() {
			global $wpdb;

			if ( ! class_exists( 'Easy_Digital_Downloads' ) || version_compare( EDD_VERSION, '1.9.6', '<' ) ) {
				// is this plugin active?
				if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
					// deactivate the plugin
					deactivate_plugins( plugin_basename( __FILE__ ) );
					// unset activation notice
					unset( $_GET[ 'activate' ] );
					// display notice
					add_action( 'admin_notices', array( $this, 'admin_notices' ) );
				}
			}

		}

		/**
		 * Register the Pending Review post status
		 *
		 * @since 1.1
		 */
		public function post_status() {
			register_post_status( 'pending_review', array(
				'label'                     => is_admin() ? _x( 'Pending Review', 'Payment status for items pending fraud review in admin.', 'edd-fm' ) : _x( 'Pending', 'Payment status for items pending fraud review on the front end.', 'edd-fm' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Pending Review <span class="count">(%s)</span>', 'Pending Reviews <span class="count">(%s)</span>', 'edd-fm' )
			) );
		}

		/**
		 * Register the custom post status for EDD_Payments
		 *
		 * @since 1.1
		 * @param $statuses
		 *
		 * @return array
		 */
		public function register_edd_post_status( $statuses ) {
			$statuses['pending_review'] = is_admin() ? __( 'Pending Review', 'edd-fm' ) : __( 'Pending', 'edd-fm' );

			return $statuses;
		}

		/**
		 * Admin notices
		 *
		 * @since 1.0
		*/
		public function admin_notices() {

			if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
				echo '<div class="error"><p>' . sprintf( __( 'You must install %sEasy Digital Downloads%s to use %s.', 'edd-fm' ), '<a href="http://wordpress.org/plugins/easy-digital-downloads/" title="Easy Digital Downloads" target="_blank">', '</a>', $this->title ) . '</p></div>';
			}

			if ( version_compare( EDD_VERSION, '1.9.6', '<' ) ) {
				echo '<div class="error"><p>' . sprintf( __( '%s requires Easy Digital Downloads Version 1.9.6 or greater. Please update Easy Digital Downloads.', 'edd-fm' ), $this->title ) . '</p></div>';
			}
		}

		/**
		 * Check payment for fraud
		 *
		 * @since  1.0
		 *
		 * @param  integer $payment      The Payment ID to check for fraud on
		 * @param  array   $payment_data The Payment Data from the insertion of the payment
		 * @return void
		 */
		public function check_for_fraud_on_payment( $payment, $payment_data ) {

			if( ! empty( $_POST['edd-gateway'] ) && 'manual_purchases' == $_POST['edd-gateway'] ) {
				return; // Never check for fraud on manual purchases
			}

			$fraud_check = new EDD_Fraud_Monitor_Check( $payment );
			if ( true === $fraud_check->is_fraud ) {

				if ( $this->send_admin_email() ) {
					$this->email_admin( $payment );
				}

				if ( $this->send_customer_email() ) {
					$this->email_customer( $payment );
				}

			}
		}

		/**
		 * Prevent purchase if fraud is detected
		 *
		 * @since  1.0
		 *
		 * @param  string $status      The status of the payment
		 * @param  integer $payment_id The payment ID
		 * @param  string $new_status  The new status of the payment
		 * @param  string $old_status  The old status of the payment
		 * @return string|bool         The status being passed
		 */
		public function prevent_purchase( $status, $payment_id, $new_status, $old_status ) {
			static $fraud_monitor_updating_status; // Prevent an infinite loop of tom-foolery

			if ( empty( $fraud_monitor_updating_status ) ) {
				if ( edd_get_payment_meta( $payment_id, '_edd_maybe_is_fraud', true ) && ! is_admin() ) {
					$fraud_monitor_updating_status = true;

					$payment = edd_get_payment( $payment_id );
					$payment->status = 'pending_review';
					$payment->save();
					$status  = false;
				}
			}

			return $status;
		}

		/**
		 * Retrieve the current setting for the GeoIP Service
		 *
		 * @since  1.0.1
		 *
		 * @return string The GeoIP Lookup service selected
		 */
		public function selected_geoip_service() {
			$selected_service = get_option( '_edd_fm_selected_geoip_service', 'hostip_info' );

			return apply_filters( 'edd_fm_selected_geoip_service', $selected_service );
		}

		/**
		 * Retrieve the geofence settings
		 *
		 * @since 1.1
		 * @return array
		 */
		public function geofence_settings() {
			$defaults = array(
				'enabled'        => false,
				'google_api_key' => '',
				'fence'          => 'country',
				'range'          => 250,
				'range_unit'     => 'mi',
			);

			$settings = get_option( '_edd_fm_geofence_settings', array() );
			$settings = wp_parse_args( $settings, $defaults );

			return apply_filters( '_edd_fm_geofence_settings', $settings );
		}

		/**
		 * Retrieve the current setting for blacklisted countries
		 *
		 * @since  1.0
		 *
		 * @return array Array of banned countries
		 *
		 */
		public function banned_countries() {
			$countries = get_option( '_edd_country_blacklist', array() );

			return apply_filters( 'eddfm_banned_countries', $countries );
		}

		/**
		 * Get the option to check if the download and purchase have the same country.
		 *
		 * @since 1.1
		 * @return bool
		 */
		public function download_country_check() {
			$check_country_on_download = get_option( '_edd_check_country_on_download', false );

			return apply_filters( '_edd_check_country_on_download', $check_country_on_download );
		}

		/**
		 * Retrieve the current setting for blacklisted IPs
		 *
		 * @since  1.0
		 *
		 * @return array Array of banned IPs
		 */
		public function banned_ips() {
			$ip_blacklist = get_option( '_edd_ip_blacklist', array() );

			if ( empty( $ip_blacklist ) ) {
				$ip_blacklist = array();
			}

			// filter out duplicate IPs
			$ip_blacklist = array_unique( $ip_blacklist );

			return apply_filters( 'eddfm_banned_ips', $ip_blacklist );
		}

		/**
		 * Retrieve the current threshold settings
		 *
		 * @since  1.0
		 *
		 * @return array Array of threshold settings
		 */
		public function thresholds() {
			$thresholds = get_option( '_edd_fm_thresholds', array() );

			$defaults = array(
				'amount'  => '',
				'items'   => '',
				'bundles' => '',
			);

			$thresholds = wp_parse_args( $thresholds, $defaults );

			return apply_filters( 'eddfm_thresholds', $thresholds );
		}

		/**
		 * Retrieve the setting for product combinations
		 *
		 * @since  1.0
		 *
		 * @return array Array of product combination triggers
		 */
		public function product_combinations() {
			$combinations = get_option( '_edd_fm_product_combinations', array() );

			return apply_filters( 'eddfm_product_combinations', $combinations );
		}

		/**
		 * Retrieve the settings for user behavior settings
		 *
		 * @since  1.0
		 *
		 * @return array Array of settings for the user behavior
		 */
		public function user_history() {
			$user_history = get_option( '_edd_fm_user_history', array() );

			$defaults = array(
				'has_purchases' => false,
				'customer_age'  => 0,
			);

			$user_history = wp_parse_args( $user_history, $defaults );

			return apply_filters( 'eddfm_user_history', $user_history );
		}

		/**
		 * Retreive the excluded gateways for gateway behavior settings
		 *
		 * @since  1.0.3
		 * @return array Array of gateways that should be exlucded from being flagged for fraud
		 */
		public function excluded_gateways() {
			$gateways = get_option( '_edd_fm_excluded_gateways', array() );

			return apply_filters( 'eddfm_excluded_gateways', $gateways );
		}

		/**
		 * Retrieve the list of bad email domains that are moderated if the email address is from them
		 *
		 * @since  1.0
		 *
		 * @return array Array of domains that get moderated automatically
		 */
		public function banned_email_domains() {
			$domains = get_option( '_edd_email_domain_blacklist', array() );
			$domains = array_unique( $domains );

			return apply_filters( 'eddfm_banned_email_domains', $domains );
		}

		/**
		 * Retrieve the extended email check setting
		 *
		 * @since  1.0
		 *
		 * @return string|bool If the extended email checks are enabled
		 */
		public function use_extended_email_checks() {
			$use_extended_email_checks = get_option( '_edd_fm_extended_email_checks', false );

			return $use_extended_email_checks;
		}

		/**
		 * Retrieve if free products should be moderated
		 *
		 * @since  1.0
		 *
		 * @return string|bool 1 if yes | false if no
		 */
		public function moderate_free() {
			return (bool) get_option( '_edd_fm_moderate_free', false );
		}

		/**
		 * Should we send to the admin when a fraud purchase is detected
		 *
		 * @since  1.0
		 *
		 * @return string|bool 1 if yes | false if no
		 */
		public function send_admin_email() {
			$send_admin_email = get_option( '_edd_fm_send_admin_email', false );

			return $send_admin_email;
		}

		/**
		 * Should we send and email to the customer
		 *
		 * @since  1.0
		 *
		 * @return string|bool 1 if yes | false if no
		 */
		public function send_customer_email() {
			$send_customer_email = get_option( '_edd_fm_send_customer_email', false );

			return $send_customer_email;
		}

		/**
		 * Register our email tags for use in the admin and customer emails
		 *
		 * @since  1.0
		 *
		 * @return void
		 */
		public function register_email_tags() {
			edd_add_email_tag(
				'fraud_reasons',
				__( 'Display a list of reasons a payment was labeled as possibly fraud.', 'edd-fm' ),
				'edd_fm_fraud_reasons_callback'
			);

			edd_add_email_tag(
				'payment_moderation_link',
				__( 'Display a link to review a possibly fraudulent payment.', 'edd-fm' ),
				'edd_fm_fraud_link_callback'
			);

		}

		/**
		 * Should a file download be allowed
		 *
		 * @since  1.0
		 * @param  string  $status     The status
		 * @param  integer $payment_id The payment ID associted with the download
		 * @param  array   $args       Array of arguements in the download process
		 * @return boolean             If the download should be processed or not
		 */
		public function check_file_download( $status, $payment_id, $args ) {

			$gateway           = edd_get_payment_gateway( $payment_id );
			$excluded_gateways = $this->excluded_gateways();

			if( 'manual_purchases' == $gateway || in_array( $gateway, $excluded_gateways ) ) {
				return $status; // Never check for fraud on manual purchases
			}
			if( edd_get_payment_meta( $payment_id, '_edd_not_fraud', true ) ) {
				return $status; // This payment has been manually cleared
			}

			// If it has already been marked as fraud, don't allow the download
			$has_fraud_flag = edd_get_payment_meta( $payment_id, '_edd_maybe_is_fraud', true );
			if ( ! empty( $has_fraud_flag ) ) {
				return false;
			}

			$payment_amount = edd_get_payment_amount( $payment_id );

			if ( empty( $payment_amount ) && ! EDD_Fraud_Monitor()->moderate_free() ) {
				return $status;
			}

			// Check if the IP being downloaded from is in a bad country or is in the banned list
			$fraud_check     = new EDD_Fraud_Monitor_Check( $payment_id, false );
			$fraud_check->ip = edd_get_ip();
			$is_fraud        = $fraud_check->run_fraud_check();

			if ( true === $is_fraud ) {
				$status = false;
			}

			return $status;
		}

		/**
		 * Adds the IP provided to the blacklist
		 * This happens whenever a payment is found to be fraud, even if the IP isn't on the blacklist
		 * Which prevents further purchases from going through until the first one is cleard up.
		 *
		 * @since  1.0
		 *
		 * @param string $ip The IP to add to the block list
		 */
		public function add_ip_to_blacklist( $ip = '' ) {

			// get current blacklist
			$ip_blacklist = get_option( '_edd_ip_blacklist', array() );

			if ( empty( $ip_blacklist ) ) {
				$ip_blacklist = array();
			}

			// merge
			$ip_blacklist = array_merge( $ip, $ip_blacklist );

			$ip_blacklist = array_unique( $ip_blacklist );

			// update blacklist
			update_option( '_edd_ip_blacklist', $ip_blacklist );
		}

		/**
		 * Add an email address to the EDD Core banned email list
		 *
		 * @since 1.1
		 * @param string $email
		 *
		 * @return bool
		 */
		public function add_email_to_blacklist( $email = '' ) {
			if ( empty( $email ) ) {
				return false;
			}

			if ( ! is_email( $email ) ) {
				return false;
			}

			$email = strtolower( sanitize_text_field( $email ) );

			// Get the existing emails
			$emails   = edd_get_option( 'banned_emails', array() );
			$emails[] = $email;

			$emails = array_unique( $emails );

			return edd_update_option( 'banned_emails', $emails );
		}

		/**
		 * Remove an email address from the EDD Core banned email list
		 *
		 * @since 1.1
		 * @param string $email
		 *
		 * @return bool
		 */
		public function remove_email_from_blacklist( $email = '' ) {
			if ( empty( $email ) ) {
				return false;
			}

			if ( ! is_email( $email ) ) {
				return false;
			}

			$email = strtolower( sanitize_text_field( $email ) );

			// Get the existing emails.
			$emails = edd_get_option( 'banned_emails', '' );
			$emails = array_map( 'strtolower', $emails );

			// Just in case the email ended up in there more than once.
			$keys   = array_keys( $emails, $email );

			foreach ( $keys as $key ) {
				unset( $emails[ $key ] );
			}

			return edd_update_option( 'banned_emails', $emails );
		}

		/**
		 * Count pending payments, for the Admin Notice
		 *
		 * @since  1.0
		 *
		 * @return integer The count of pending payments caught by Fraud Monitor
		 */
		public function count_pending() {
			if ( ! is_null( $this->pending_count ) ) {
				return $this->pending_count;
			}

			$query = array(
				'post_type'  => 'edd_payment',
				'nopaging'   => true,
				'meta_key'   => '_edd_maybe_is_fraud',
				'meta_value' => true
			);

			$lists = new WP_Query( $query );

			if ( isset( $lists->found_posts ) ) {
				$this->pending_count = $lists->found_posts;
			}

			return $this->pending_count;
		}

		/**
		 * Remove the fraud flag if teh payment is changed to 'publish' (complete)
		 *
		 * @since  1.0
		 *
		 * @param  integer $payment_id The Payment ID
		 * @param  string  $new_status The new status
		 * @param  string  $old_status The old status
		 * @return void
		 */
		public function remove_fraud_flag_on_update( $payment_id, $new_status, $old_status ) {

			if ( ! edd_get_payment_meta( $payment_id, '_edd_maybe_is_fraud', true ) ) {
				return;
			}

			if ( 'publish' == $new_status ) {
				$this->remove_fraud_flag( $payment_id );
			}

		}

		/**
		 * Remove fraud status when the 'Accept as Valid' is clicked
		 * from the Payment Details page
		 *
		 * @since  1.0
		 *
		 * @param  array $data The $_GET data passed via the link
		 * @return void
		 */
		public function remove_fraud_flag_on_click( $data ) {

			if( empty( $data['id'] ) ) {
				return;
			}

			$nonce = ! empty( $data['_wpnonce'] ) ? $data['_wpnonce'] : false;
			if ( ! wp_verify_nonce( $nonce, 'eddfm_accept_nonce' ) ) {
				return;
			}

			$payment_id = absint( $data['id'] );

			if ( ! edd_get_payment_meta( $payment_id, '_edd_maybe_is_fraud', true ) ) {
				return;
			}

			$this->remove_fraud_flag( $payment_id );

			wp_safe_redirect( remove_query_arg( 'edd_action' ) ); exit;

		}

		/**
		 * Process a fraud payment from the 'Confirm as Fraud' button on the Payment Details
		 *
		 * @since  1.0
		 *
		 * @param  array $data The $_POST data passed via AJAX
		 * @return void
		 */
		public function remove_fraud_notice_on_click( $data ) {
			$response = array();

			if ( empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'eddfm-is-fraud' ) ) {
				$response['error']   = true;
				$response['message'] = __( 'Nonce verification failed', 'edd-fm' );
			}

			if ( empty( $data['payment_id'] ) ) {
				$response['error']   = true;
				$response['message'] = __( 'Invalid Payment ID', 'edd-fm' );;
			}

			$payment_id = absint( $data['payment_id'] );
			$payment    = new EDD_Payment( $payment_id );

			if ( ! $payment->get_meta( '_edd_maybe_is_fraud' ) ) {
				$response['error']   = true;
				$response['message'] = __( 'Payment is not pending fraud verification', 'edd-fm' );
			}

			if ( empty( $response['error'] ) ) {
				$response['success'] = true;

				$allowed_statuses = edd_get_payment_statuses();
				$allowed_statuses['delete'] = 'delete';
				$status = array_key_exists( strtolower( $data['status'] ), $allowed_statuses ) ? $data['status'] : 'revoked';

				if ( 'delete' === $status ) {
					edd_delete_purchase( $payment_id );
					$response['message']  = __( 'Payment deleted', 'edd-fm' );
					$response['redirect'] = admin_url( 'edit.php?post_type=download&page=edd-payment-moderation&edd-message=payment_deleted' );
				} else {
					$payment->status = $status;
					$payment->save();

					delete_post_meta( $payment_id, '_edd_maybe_is_fraud' );
					delete_post_meta( $payment_id, '_edd_maybe_is_fraud_reason' );
					$payment->update_meta( '_edd_confirmed_as_fraud', '1' );

					do_action( 'edd_fm_payment_confirmed_as_fraud', $payment_id );

					$response['redirect'] = admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&edd-message=payment-updated&id=' . $payment_id );
					$response['message']  = sprintf( __( 'Payment status updated to %s', 'eddfm' ), $status );

					$this->add_ip_to_blacklist( $payment->ip );
					$this->add_email_to_blacklist( $payment->email );
				}

			}

			echo json_encode( $response );
			die();

		}

		/**
		 * Process manually marking as payment as fraud from the admin
		 *
		 * @since 1.1
		 * @param $data
		 *
		 */
		public function manually_flag_payment( $data ) {

			$response = array();

			if ( empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'edd-fm-flag-payment' ) ) {
				$response['error']   = true;
				$response['message'] = __( 'Nonce verification failed', 'edd-fm' );

				echo json_encode( $response );
				die();
			}

			if ( empty( $data['payment_id'] ) ) {
				$response['error']   = true;
				$response['message'] = __( 'Invalid Payment ID', 'edd-fm' );;
			}

			$payment = edd_get_payment( absint( $data['payment_id'] ) );

			if ( $payment->get_meta( '_edd_maybe_is_fraud' ) || $payment->get_meta( '_edd_confirmed_as_fraud' ) ) {
				$response['error']   = true;
				$response['message'] = __( 'Payment is already flagged', 'edd-fm' );
			}

			$allowed_statuses = array( 'revoked', 'refunded' );
			$status           = 'false' === $data['status'] ? false : sanitize_text_field( $data['status'] );
			if ( ! empty( $status ) && ! in_array( $status, $allowed_statuses ) ) {
				$response['error']   = true;
				$response['message'] = __( 'Invalid status', 'edd-fm' );
			}

			if ( empty( $response['error'] ) ) {
				$response['success'] = true;
				$reason              = ! empty( $data['reason'] ) ? strip_tags( $data['reason'] ) : '';

				if ( 'fraud' === $data['flag_as'] ) {

					$payment->status = $status;
					$payment->add_note( sprintf( __( 'Payment manually flagged as fraud: %s', 'edd-fm' ), $reason ) );
					$payment->save();
					add_post_meta( $payment->ID, '_edd_confirmed_as_fraud', 1 );

					do_action( 'edd_fm_payment_confirmed_as_fraud', $payment->ID );

					$response['message']  = __( 'Payment marked as fraud.', 'edd-fm' );

				} else {

					$payment->status = 'pending_review';
					$payment->add_note( sprintf( __( 'Payment manually flagged for review: %s', 'edd-fm' ), $reason ) );
					$payment->save();
					add_post_meta( $payment->ID, '_edd_maybe_is_fraud', 1 );
					add_post_meta( $payment->ID, '_edd_maybe_is_fraud_reason', __( 'Manually flagged for review', 'edd-fm' ) );
					$response['message']  = __( 'Payment flagged for review.', 'edd-fm' );

					// Send the email to the customer since this is now setup for review.
					if ( $this->send_customer_email() ) {
						$this->email_customer( $payment->ID );
					}

				}

				$this->add_ip_to_blacklist( $payment->ip );
				$this->add_email_to_blacklist( $payment->email );
				$response['redirect'] = admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&edd-message=payment-updated&id=' . $payment->ID );

			}

			echo json_encode( $response );
			die();

		}

		/**
		 * Remove a fraud flag for a payment ID
		 * 1. Deletes the _edd_maybe_is_fraud and _edd_maybe_is_fraud_reason postmeta
		 * 2. Adds the _edd_not_fraud postmeta
		 * 3. Logs a Payment Note with the user who approved it
		 * 4. Removes the IP Address of the payment from the blacklisted IPs
		 *
		 * @since  1.0
		 *
		 * @param  integer $payment_id The Payment ID
		 * @return void
		 */
		public function remove_fraud_flag( $payment_id = 0 ) {

			delete_post_meta( $payment_id, '_edd_maybe_is_fraud' );
			delete_post_meta( $payment_id, '_edd_maybe_is_fraud_reason' );
			add_post_meta( $payment_id, '_edd_not_fraud', '1' );

			edd_update_payment_status( $payment_id );

			$user = wp_get_current_user();

			// Log a note about possible fraud
			edd_insert_payment_note( $payment_id, sprintf( __( 'This payment was cleared as legitimate by %s.', 'edd-fm' ), $user->user_login ) );

			// clear IP from blacklist
			$user_ip   = edd_get_payment_user_ip( $payment_id );
			$blacklist =  $this->banned_ips();

			// check if IP in in blacklist
			if ( in_array( $user_ip, $blacklist ) ) {

				// find key which includes the IP address
				$key_to_remove = array_search( $user_ip, $blacklist );
				// unset the key
				if ( isset( $key_to_remove ) ) {
					unset( $blacklist[ $key_to_remove ] );
				}

				// update blacklist
				update_option( '_edd_ip_blacklist', $blacklist );
			}

			$email_address = edd_get_payment_user_email( $payment_id );
			$this->remove_email_from_blacklist( $email_address );
		}

		/**
		 * Remove a fraud flag for a payment ID without doing anything else
		 *
		 * @since  1.0.8
		 *
		 * @return void
		 */
		public function remove_fraud_flag_only() {

			if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'eddfm_fraud_nonce' ) ) {
				return;
			}

			if( ! current_user_can( 'manage_shop_payments' ) ) {
				return;
			}

			$payment_id = absint( $_REQUEST['id'] );

			delete_post_meta( $payment_id, '_edd_maybe_is_fraud' );
			delete_post_meta( $payment_id, '_edd_maybe_is_fraud_reason' );

			edd_insert_payment_note( $payment_id, sprintf( __( 'Fraud flag removed by %s', 'edd-fm' ), wp_get_current_user()->user_login ) );

			wp_safe_redirect( remove_query_arg( array( '_wpnonce', 'edd_action' ) ) ); exit;

		}

		/**
		 * Register the Admin menu item
		 *
		 * @since  1.0
		 *
		 * @return void
		 */
		public function admin_menu() {
			$count = $this->count_pending() ? '(' . $this->count_pending() . ')' : '';

			add_submenu_page( 'edit.php?post_type=download', __( 'Payment Moderation', 'edd-fm' ), sprintf( __( 'Payment Moderation %s', 'edd-fm' ), $count ), 'edit_shop_payments', 'edd-payment-moderation', array( $this, 'edd_payment_moderation_page' ) );
		}

		/**
		 * List Table of payments marked as possible fraud
		 *
		 * @since  1.0
		 *
		 * @return void
		 */
		public function edd_payment_moderation_page() {
			global $edd_options;

			if ( isset( $_GET['view'] ) && 'view-order-details' == $_GET['view'] ) {
				require_once EDD_PLUGIN_DIR . 'includes/admin/payments/view-order-details.php';
			} else {
				require_once EDD_FM_PLUGIN_DIR . 'includes/admin/class-payments-table.php';
				$payments_table = new EDD_Payment_Moderation_Table();
				$payments_table->prepare_items();
			?>
			<div class="wrap">
				<h2><?php _e( 'Payment Moderation', 'edd-fm' ); ?></h2>

				<form id="edd-payments-filter" method="get" action="<?php echo admin_url( 'edit.php?post_type=download&page=edd-payment-moderation' ); ?>">
					<input type="hidden" name="post_type" value="download" />
					<input type="hidden" name="page" value="edd-payment-moderation" />

					<?php $payments_table->display() ?>
				</form>
			</div>
		<?php
			}
		}

		/**
		 * Sends an email to the admin
		 *
		 * @since  1.0
		 *
		 * @param  integer $payment The payment ID
		 * @return void
		 */
		public function email_admin( $payment ) {
			$to = $this->get_admin_email_address();

			if ( empty( $to ) ) {
				return;
			}

			$from_name  = edd_get_option( 'from_name', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
			$from_email = edd_get_option( 'from_email', get_bloginfo( 'admin_email' ) );

			$subject = apply_filters( 'eddfm_email_admin_subject', $this->get_admin_email_subject() );

			$headers     = "From: " . stripslashes_deep( html_entity_decode( $from_name, ENT_COMPAT, 'UTF-8' ) ) . " <$from_email>\r\n";
			$headers    .= "Reply-To: ". $from_email . "\r\n";
			$headers    .= "Content-Type: text/html; charset=utf-8\r\n";

			$message = $this->admin_email_message( $payment );
			$message = stripslashes( str_replace( '<br />', "\n", $message ) );

			$emails = EDD()->emails;
			$emails->__set( 'from_name', $from_name );
			$emails->__set( 'from_email', $from_email );
			$emails->__set( 'headers', $headers );
			$emails->__set( 'heading', __( 'Payment held for moderation', 'edd-fm' ) );

			$emails->send( $to, $subject, $message );
		}

		/**
		 * Gets the email addresses for sending new purchase moderation notices.
		 *
		 * @since  1.0
		 * @return string The email address
		 */
		public function get_admin_email_address() {
			$email_addresses = get_option( '_edd_fm_admin_email_address', '' );

			$valid_emails    = array();
			$email_addresses = array_map( 'trim', explode( ',', $email_addresses ) );

			foreach ( $email_addresses as $email_address ) {

				if ( is_email( $email_address ) ) {

					$valid_emails[] = $email_address;

				}

			}

			if ( empty( $valid_emails ) ) {
				return;
			}

			$email_addresses = implode( ',', $valid_emails );

			return $email_addresses;
		}

		/**
		 * Retrieve subject to use for the admin email
		 *
		 * @since  1.0
		 *
		 * @return string The Subject to use for the admin email
		 */
		public function get_admin_email_subject() {
			$default_email_subject = get_option( '_edd_fm_admin_subject', __( 'Possible fraudulent purchase', 'edd-fm' ) );

			return $default_email_subject;
		}

		/**
		 * Retrieve the email body for a specific payment to the admin
		 *
		 * @since 1.0
		 *
		 * @param  integer $payment The payment ID the email is for
		 * @return string $default_email_body Body of the email
		 */
		public function admin_email_message( $payment ) {
			$default_email_body = get_option( '_edd_fm_admin_notice', '' );
			$default_email_body = edd_do_email_tags( $default_email_body, $payment );

			return stripslashes( apply_filters( 'eddfm_admin_email_message', $default_email_body ) );
		}

		/**
		 * Send an email to the customer about a payment being marked as fraud
		 *
		 * @since   1.0
		 *
		 * @param  integer $payment The payment ID to send the email for
		 * @return void
		 */
		public function email_customer( $payment ) {
			$from_name  = edd_get_option( 'from_name', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
			$from_email = edd_get_option( 'from_email', get_bloginfo( 'admin_email' ) );

			$subject = apply_filters( 'eddfm_email_customer_subject', $this->get_customer_email_subject() );

			$headers     = "From: " . stripslashes_deep( html_entity_decode( $from_name, ENT_COMPAT, 'UTF-8' ) ) . " <$from_email>\r\n";
			$headers    .= "Reply-To: ". $from_email . "\r\n";
			$headers    .= "Content-Type: text/html; charset=utf-8\r\n";

			$message = $this->customer_email_message( $payment );

			$email   = edd_get_payment_user_email( $payment );

			$emails = EDD()->emails;
			$emails->__set( 'from_name', $from_name );
			$emails->__set( 'from_email', $from_email );
			$emails->__set( 'headers', $headers );
			$emails->__set( 'heading', __( 'Information about your purchase', 'edd-fm' ) );

			$emails->send( $email, $subject, $message );
		}

		/**
		 * Retrieve subject to use for the customer email
		 *
		 * @since  1.0
		 *
		 * @return string The Subject to use for the customer email
		 */
		public function get_customer_email_subject() {
			$default_email_subject = get_option( '_edd_fm_customer_subject', __( 'Your purchase was automatically flagged as potential fraud', 'edd-fm' ) );

			return $default_email_subject;
		}

		/**
		 * Retrieve the email body for a specific payment to the customer
		 *
		 * @since 1.0
		 *
		 * @param  integer $payment The payment ID the email is for
		 * @return string $default_email_body Body of the email
		 */
		public function customer_email_message( $payment ) {
			$default_email_body = get_option( '_edd_fm_customer_notice', '' );
			$default_email_body = edd_do_email_tags( $default_email_body, $payment );

			return stripslashes( apply_filters( 'eddfm_customer_email_message', $default_email_body ) );
		}

		/**
		 * Show the admin notice that payments need moderation
		 *
		 * @since  1.0
		 *
		 * @return void
		 */
		public function admin_message() {
			if ( ! empty( $_GET['page'] ) && $_GET['page'] == 'edd-payment-moderation' ) {
				return;
			}

			if ( current_user_can( 'view_shop_reports' ) && $this->count_pending() ) {

				$plural = $this->count_pending() >= 2 ? 's' : '';
				add_settings_error( 'eddfm-notices', 'edd-receipt-added', sprintf( __( 'You have %s possible <a href="%s">fraudulent payment%s</a>.', 'edd-fm' ), $this->count_pending(), admin_url( 'edit.php?post_type=download&page=edd-payment-moderation' ), $plural ), 'error' );
			}

			settings_errors( 'eddfm-notices' );
		}

		/**
		 * Tells EDD_Payment to process the refund actions if the payment being refunded was done so from a Fraud Status
		 *
		 * @since  1.0.7
		 * @param  bool   $should_refund   The default state of the refund action
		 * @param  object $payment         The EDD_Payment object of the payment being refunded
		 * @return bool                    If EDD should continue through with the refund request
		 */
		public function process_refund( $should_refund, $payment ) {
			if ( $payment->get_meta( '_edd_maybe_is_fraud' ) && ( 'pending' === $payment->old_status || 'pending_review' === $payment->old_status ) ) {
				// Set a $_POST global so we can run the stripe refund...since it requires that.
				$_POST['edd_refund_in_stripe'] = true;
				$should_refund                 = true;
			}

			return $should_refund;
		}

		/**
		 * Allows the processing of a refund in stripe when marked as fraud, and marked for refund
		 *
		 * @since  1.0.7
		 * @param  bool   $should_refund The default action of if we should refund
		 * @param  int    $payment_id    The Payment ID being refunded
		 * @param  string $new_status    The New Payment Status
		 * @param  string $old_status    The Old Payment Status
		 * @return bool                  If the payment should pass the refund status to Stripe
		 */
		public function allow_stripe_refund( $should_refund, $payment_id, $new_status, $old_status ) {
			if ( edd_get_payment_meta( $payment_id, '_edd_maybe_is_fraud', true ) && ( 'pending' === $old_status || 'pending_review' === $old_status ) ) {
				$should_refund = true;
			}

			return $should_refund;
		}

		/**
		 * When a payment is confirmed as fraud, add a customer note.
		 *
		 * @since 1.1.1
		 * @param $payment_id
		 * @return void
		 */
		public function leave_customer_fraud_note( $payment_id ) {
			$payment  = new EDD_Payment( $payment_id );
			$customer = new EDD_Customer( $payment->customer_id );
			$user     = wp_get_current_user();

			$customer->add_note( sprintf( __( 'Payment %d confirmed as fraud by %s', 'edd-fraud-monitor' ), $payment_id, $user->user_login ) );
		}
	}

/**
 * Loads a single instance of EDD Fraud Moderation
 *
 * This follows the PHP singleton design pattern.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * @example <?php $EDD_Fraud_Monitor = EDD_Fraud_Monitor(); ?>
 *
 * @since 1.0
 *
 * @see EDD_Fraud_Monitor::get_instance()
 *
 * @return object Returns an instance of the EDD_Fraud_Monitor class
 */
function EDD_Fraud_Monitor() {
	if ( class_exists( 'Easy_Digital_Downloads' ) ) {
		return EDD_Fraud_Monitor::get_instance();
	}
}

/**
 * Loads plugin after all the others have loaded and have registered their hooks and filters
 *
 * @since 1.0
*/
add_action( 'plugins_loaded', 'EDD_Fraud_Monitor', apply_filters( 'eddfm_action_priority', 10 ) );

endif;
