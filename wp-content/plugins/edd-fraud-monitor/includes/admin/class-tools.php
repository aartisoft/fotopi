<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EDD_Fraud_Monitor_Tools {

	private static $instance = null;

	/**
	 * Load the Tools page
	 *
	 * @since  1.0
	 */
	private function __construct() {
		$this->hooks();
		$this->filters();
	}

	/**
	 * Get the one true instance of the Tools tab
	 *
	 * @since  1.0
	 *
	 * @return object The EDD_Fraud_Monitor_Tools instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof EDD_Fraud_Monitor_Tools ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register the hooks for the Fraud Monitor Tools tab and sections
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	private function hooks() {
		add_action( 'edd_tools_tab_fraud_monitor', array( $this, 'tools_tab_callback' ) );

		// General Section
		add_action( 'edd_fraud_monitor_section-general', array( $this, 'general_fraud_settings' ) );
		add_action( 'edd_fm_save_general',               array( $this, 'save_general'           ), 10, 1 );

		// IP Section
		add_action( 'edd_fraud_monitor_section-ip', array( $this, 'ip_blacklist_admin' ) );
		add_action( 'edd_save_banned_ips',          array( $this, 'save_ips'           ), 10, 1 );

		// Country Section
		add_action( 'edd_fraud_monitor_section-country', array( $this, 'country_blacklist_admin' ) );
		add_action( 'edd_save_banned_countries',         array( $this, 'save_countries'          ), 10, 1 );

		// Email Section
		add_action( 'edd_fraud_monitor_section-email', array( $this, 'email_settings_admin' ) );
		add_action( 'edd_save_fm_email_settings',      array( $this, 'save_email_settings'  ), 10, 1 );

		// Behavior Section
		add_action( 'edd_fraud_monitor_section-behavior', array( $this, 'behavior_admin' ) );
		add_action( 'edd_save_fm_behaviors',              array( $this, 'save_behaviors' ), 10, 1 );

	}

	/**
	 * Register the filters for the Fraud Monitor Tools Tab
	 *
	 * @since  1.0
	 * @return void
	 */
	private function filters() {
		add_filter( 'edd_tools_tabs', array( $this, 'register_tab' ), 10, 1 );
	}

	/**
	 * Add the Fraud Monitor Tab to the Tools Page
	 *
	 * @since  1.0
	 *
	 * @param  array $tabs Array of the existing tabs
	 * @return array       The Tabs for the Tools page
	 */
	public function register_tab( $tabs ) {
		$tabs['fraud_monitor'] = __( 'Fraud Monitor', 'edd-fm' );

		return $tabs;
	}

	/**
	 * The Default sections for the Fraud Monitor tab
	 *
	 * @since  1.0
	 *
	 * @return array Array of the sections
	 */
	public function get_sections() {
		$default_sections = array(
			'general'  => __( 'General', 'edd-fm' ),
			'ip'       => __( 'IP Addresses', 'edd-fm' ),
			'country'  => __( 'Country', 'edd-fm' ),
			'email'    => __( 'Email', 'edd-fm' ),
			'behavior' => __( 'Behavior', 'edd-fm' ),
		);

		return apply_filters( 'eddfm_sections', $default_sections );
	}

	/**
	 * Display the tools tab and determine the section to display
	 *
	 * @since  1.0
	 *
	 * @return void
	 */
	public function tools_tab_callback() {
		$sections  = $this->get_sections();

		$keys      = array_keys( $sections );
		$last_key  = end( $keys );
		reset( $sections );

		$current_view = ! empty( $_GET['view'] ) && array_key_exists( $_GET['view'], $sections ) ? sanitize_text_field( $_GET['view'] ) : 'general';
		?>
		<ul class="subsubsub">
		<?php
		foreach ( $sections as $view => $title ) {
			$url   = admin_url( 'edit.php?post_type=download&page=edd-tools&tab=fraud_monitor&view=' . $view );
			$class = $current_view === $view ? ' class="current"' : '';
			echo '<li>';
			printf( __( '<a href="%s"%s>%s</a>', 'edd-fm' ), $url, $class, $title );
			echo $view !== $last_key ? ' |' : '';
			echo '</li>';
		}
		?>
		</ul>
		<?php
		do_action( 'edd_fraud_monitor_section-' . $current_view );
	}

	/**
	 * Render the Genral Fraud settings section
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	function general_fraud_settings() {
		?>
		<form method="post" action="<?php echo admin_url( 'edit.php?post_type=download&page=edd-tools&tab=fraud_monitor&view=general' ); ?>">

			<div class="edd-fm-tools postbox">
				<h3><span><?php _e( 'General Settings', 'edd-fm' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'General settings for the Fraud Monitoring Extension', 'edd-fm' ); ?></p>
					<p>
						<?php $moderate_free = EDD_Fraud_Monitor()->moderate_free(); ?>
						<input type="checkbox" <?php checked( true, $moderate_free, true ); ?> id="moderate_free" name="moderate_free" value="1" /> <label for="moderate_free"><?php _e( 'Moderate Free Purchases', 'edd-fm' ); ?></label><br />
						<span class="description"><?php printf( __( 'Run payments of %s through moderation checks.', 'edd-fm' ), edd_currency_filter( edd_format_amount( 0 ) ) ); ?></span>
					</p>
				</div>
			</div>

			<div class="edd-fm-tools postbox">
				<h3><span><?php _e( 'Admin Notifications', 'edd-fm' ); ?></span></h3>
				<div class="inside">
					<p>
						<?php $send_admin_email = EDD_Fraud_Monitor()->send_admin_email(); ?>
						<input type="checkbox" class="email-check-box" <?php checked( true, $send_admin_email, true ); ?> id="send_admin_email" name="send_admin_email" value="1" /> <label for="send_admin_email"> <?php _e( 'Send Admin Emails', 'edd-fm' ); ?></label><br />
						<span class="description"><?php _e( 'Sends an email when new items are flagged for review.', 'edd-fm' ); ?></span>
						<?php $show_admin_notice = $send_admin_email ? '' : ' style="display: none;"' ?>
						<div class="email-content" <?php echo $show_admin_notice; ?>>
							<h3><?php _e( 'Admin Notification Message', 'edd-fm' ); ?></h3>

							<div>
								<?php $admin_email_address = EDD_Fraud_Monitor()->get_admin_email_address(); ?>
								<input type="text" class="regular-text" name="admin_email_address" id="admin-email-address" value="<?php echo $admin_email_address; ?>" placeholder="<?php _e( 'Email Address', 'edd-fm' ); ?>" /> <label for="admin-email-address"><?php _e( 'Email Address', 'edd-fm' ); ?></label>
								<br /><span class="description"><?php _e( 'Send notifications of payments that need moderation to the above email address(es). Comma separate multiple email addresses.', 'edd-fm' ); ?></span>
							</div>

							<div>
								<?php $admin_subject = EDD_Fraud_Monitor()->get_admin_email_subject(); ?>
								<input type="text" class="regular-text" name="admin_email_subject" id="admin-email-subject" value="<?php echo $admin_subject; ?>" placeholder="<?php _e( 'Enter Admin Message Subject', 'edd-fm' ); ?>" /> <label for="admin-email-subject"><?php _e( 'Admin email subject', 'edd-fm' ); ?></label>
								<br /><span class="description"><?php _e( 'The subject line of the email sent to the admin when a payment is marked as fraud.', 'edd-fm' ); ?></span>
							</div>

							<div>
							<?php
								$default_notice  = '';
								$default_notice  = __( "Dear Admin,", "edd-fm" ) . "\n\n";
								$default_notice .= __( "We've detected a possible fraudulent payment which requires your immediate action.", "edd-fm" ) . "\n\n";

								$default_notice .= __( 'Reasons:', 'edd-fm' ) . "\n";
								$default_notice .= '{fraud_reasons}' . "\n\n";
								$default_notice .= __( 'Visit {payment_moderation_link} for more details.', 'edd-fm' ) . "\n";

								$saved_message = get_option( '_edd_fm_admin_notice', '' );
								if ( empty( $saved_message ) ) {
									$saved_message = $default_notice;
								}
								ob_start();
								wp_editor( stripslashes( $saved_message ), 'admin_email_message', array( 'textarea_name' => 'admin_email_message', 'textarea_rows' => 10 ) );
								echo ob_get_clean();
							?>
							</div>
							<h4><?php _e( 'Available email tags:', 'edd-fm' ); ?> <a href="#" class="eddfm-show-tags"><?php _e( 'Show Tags', 'edd-fm' ); ?></a></h4>
							<div class="eddfm-email-tags" style="display: none;" <?php echo ( $send_admin_email ) ? '' : 'style="display: none"'; ?> >
								<?php echo edd_get_emails_tags_list(); ?>
								<a href="#" class="eddfm-hide-tags"><?php _e( 'Hide Tags', 'edd-fm' ); ?></a>
							</div>
						</div>
					</p>
				</div>
			</div>

			<div class="edd-fm-tools postbox">
				<h3><?php _e( 'Customer Notifications', 'edd-fm' ); ?></h3>
				<div class="inside">
					<p>
						<?php $send_customer_email = EDD_Fraud_Monitor()->send_customer_email(); ?>
						<input type="checkbox" class="email-check-box" <?php checked( true, $send_customer_email, true ); ?> id="send_customer_email" name="send_customer_email" value="1" /> <label for="send_customer_email"><?php _e( 'Send Customer Emails', 'edd-fm' ); ?></label><br />
						<span class="description"><?php _e( 'Sends an email to the customer, letting them know their purchase is under review.', 'edd-fm' ); ?></span>
						<?php $show_customer_notice = $send_customer_email ? '' : ' style="display: none;"' ?>
						<div class="email-content" <?php echo $show_customer_notice; ?>>
							<h3><?php _e( 'Customer Notification Message', 'edd-fm' ); ?></h3>

							<div>
								<?php $customer_subject = EDD_Fraud_Monitor()->get_customer_email_subject(); ?>
								<input type="text" class="regular-text" name="customer_email_subject" id="customer-email-subject" value="<?php echo $customer_subject; ?>" placeholder="<?php _e( 'Enter Customer Message Subject', 'edd-fm' ); ?>" /><label for="customer-email-subject"> <?php _e( 'Customer email subject', 'edd-fm' ); ?></label>
								<br /><span class="description"><?php _e( 'The subject line of the email sent to the customer when a payment is marked as fraud.', 'edd-fm' ); ?></span>
							</div>

							<div>
							<?php
								$default_notice  = '';
								$default_notice .= __( "Hi {name},", "edd-fm" ) . "\n\n";
								$default_notice .= __( "We're emailing you because you've just made a purchase on {sitename} for {price}. ", "edd-fm" ) . "\n\n";
								$default_notice .= __( "Your purchase was automatically flagged as potential fraud, so we will need you to verify that you did in fact authorize this purchase before we release your download links.", "edd-fm" ) . "\n\n";
								$default_notice .= __( "In order to verify your purchase, could you please provide the last 4 digits of the credit / debit card you used, your billing address, the expiration date of the card, and also your IP address.", "edd-fm" ) . "\n\n";
								$default_notice .= __( "To find your IP address, simply visit http://www.whatsmyip.org/ and note the number at the top of the website after \"Your IP address is\".", "edd-fm" ) . "\n\n";
								$default_notice .= __( "If you have any questions, or you know for sure that you did not authorize this purchase, just let us know.", "edd-fm" ) . "\n\n";
								$default_notice .= __( "Thank you", "edd-fm" ) . "\n";
								$default_notice .= __( "The team at {sitename}", "edd-fm" ) . "\n";

								$saved_message = get_option( '_edd_fm_customer_notice', '' );
								if ( empty( $saved_message ) ) {
									$saved_message = $default_notice;
								}
								ob_start();
								wp_editor( stripslashes( $saved_message ), 'customer_email_message', array( 'textarea_name' => 'customer_email_message', 'textarea_rows' => 20 ) );
								echo ob_get_clean();
							?>
							</div>
							<h4><?php _e( 'Available email tags:', 'edd-fm' ); ?> <a href="#" class="eddfm-show-tags"><?php _e( 'Show Tags', 'edd-fm' ); ?></a></h4>
							<div class="eddfm-email-tags" style="display: none;" <?php echo ( $send_customer_email ) ? '' : 'style="display: none"'; ?> >
								<?php echo edd_get_emails_tags_list(); ?>
								<a href="#" class="eddfm-hide-tags"><?php _e( 'Hide Tags', 'edd-fm' ); ?></a>
							</div>
						</div>
					</p>
				</div>
			</div>

			<input type="hidden" name="edd_action" value="fm_save_general" />
			<?php wp_nonce_field( 'edd_fm_general_nonce', 'edd_fm_general_nonce' ); ?>
			<?php submit_button( __( 'Save Settings', 'edd-fm' ), 'primary', 'submit', false ); ?>
		</form>
		<?php
	}

	/**
	 * Save the General Section
	 *
	 * @since  1.0
	 *
	 * @param  array $data The $_POST data to save
	 * @return void
	 */
	public function save_general( $data ) {

		if ( ! wp_verify_nonce( $data['edd_fm_general_nonce'], 'edd_fm_general_nonce' ) ) {
			return;
		}

		$moderate_free = empty( $data['moderate_free'] ) ? false : true;
		if ( false === $moderate_free ) {
			delete_option( '_edd_fm_moderate_free' );
		} else {
			update_option( '_edd_fm_moderate_free', true );
		}

		$send_admin_email = empty( $data['send_admin_email'] ) ? false : true;
		if ( false === $send_admin_email ) {
			delete_option( '_edd_fm_send_admin_email' );
		} else {
			update_option( '_edd_fm_send_admin_email', true );
		}

		$admin_address = isset( $data['admin_email_address'] ) ? sanitize_text_field( $data['admin_email_address'] ) : '';
		update_option( '_edd_fm_admin_email_address', $admin_address );

		$admin_subject = isset( $data['admin_email_subject'] ) ? sanitize_text_field( $data['admin_email_subject'] ) : '';
		update_option( '_edd_fm_admin_subject', $admin_subject );

		$admin_message = isset( $data['admin_email_message'] ) ? $data['admin_email_message']: '';
		update_option( '_edd_fm_admin_notice', $admin_message );


		$send_customer_email = empty( $data['send_customer_email'] ) ? false : true;
		if ( false === $send_customer_email ) {
			delete_option( '_edd_fm_send_customer_email' );
		} else {
			update_option( '_edd_fm_send_customer_email', true );
		}

		$customer_subject = isset( $data['customer_email_subject'] ) ? sanitize_text_field( $data['customer_email_subject'] ) : '';
		update_option( '_edd_fm_customer_subject', $customer_subject );

		$customer_message = isset( $data['customer_email_message'] ) ? $data['customer_email_message'] : '';
		update_option( '_edd_fm_customer_notice', $customer_message );

		do_action( 'eddfm_save_general_settings', $data );

	}

	/**
	 * Render the IP Fraud Settings
	 *
	 * @since  1.0
	 *
	 * @return void
	 */
	function ip_blacklist_admin() {
		?>
		<form method="post" action="<?php echo admin_url( 'edit.php?post_type=download&page=edd-tools&tab=fraud_monitor&view=ip' ); ?>">

			<div class="edd-fm-tools postbox">
				<h3><span><?php _e( 'Flagged IPs', 'edd-fm' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'IPs placed in the box below will not be allowed to make purchases.', 'edd-fm' ); ?></p>
						<p>
							<textarea name="banned_ips" rows="10" class="large-text"><?php echo implode( "\n", EDD_Fraud_Monitor()->banned_ips() ); ?></textarea>
							<span class="description"><?php _e( 'Enter IP addresses to disallow, one per line', 'edd-fm' ); ?></span>
						</p>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<input type="hidden" name="edd_action" value="save_banned_ips" />
			<?php wp_nonce_field( 'edd_banned_ips_nonce', 'edd_banned_ips_nonce' ); ?>
			<?php submit_button( __( 'Save Settings', 'edd-fm' ), 'primary', 'submit', false ); ?>
		</form>
		<?php
	}

	/**
	 * Save the Fraud IP settings
	 *
	 * @since  1.0
	 *
	 * @param  array $data The $_POST data
	 * @return void
	 */
	public function save_ips( $data ) {

		if ( ! wp_verify_nonce( $data['edd_banned_ips_nonce'], 'edd_banned_ips_nonce' ) ) {
			return;
		}

		// Sanitize the input
		$ips = array_unique( array_filter( array_map( 'trim', explode( "\n", $data['banned_ips'] ) ) ) );

		update_option( '_edd_ip_blacklist', $ips );

		do_action( 'eddfm_save_ip_settings', $data );

	}

	/**
	 * Render the Fraud Country settings
	 *
	 * @since  1.0
	 *
	 * @return void
	 */
	public function country_blacklist_admin() {
		$selected_service   = EDD_Fraud_Monitor()->selected_geoip_service();
		$ip_lookup_services = edd_fm_get_ip_lookup_services();

		$countries = edd_get_country_list();
		$current   = get_option( '_edd_country_blacklist', true );
		if ( false === $current ) {
			$current = array();
		}

		$args = array(
			'name'             => 'banned_countries[]',
			'class'            => 'edd-bannedcountries-select',
			'id'               => 'edd-fm-banned-countries',
			'options'          => $countries,
			'chosen'           => true,
			'multiple'         => true,
			'placeholder'      => __( 'Type to search Countries', 'edd-fm' ),
			'selected'         => $current,
			'show_option_all'  => false,
			'show_option_none' => false,
			'data'             => array(
				'search-type'  => 'no_ajax'
			)
		);
		?>
		<form method="post" action="<?php echo admin_url( 'edit.php?post_type=download&page=edd-tools&tab=fraud_monitor&view=country' ); ?>">

			<div class="edd-fm-tools postbox">
				<h3><span><?php _e( 'GeoLocation Service', 'edd-fm' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Select the geo-location service for helping determine the buyer\'s country', 'edd-fm' ); ?></p>
						<p>
							<select name="ip_lookup_service" id="ip-lookup-service">
								<?php foreach ( $ip_lookup_services as $key => $service ) : ?>
									<option value="<?php echo $key; ?>" <?php selected( $key, $selected_service, true ); ?>><?php echo $service['label']; ?></option>
								<?php endforeach; ?>
							</select>
							<label for="ip-lookup-service"><?php _e( 'Select a service', 'edd-fm' ); ?></label>
							<br />
							<p id="geo-ip-service-notices">
							<?php foreach( $ip_lookup_services as $key => $service ) : ?>
								<?php $display_notice = $selected_service != $key ? ' style="display:none;"' : ''; ?>
								<span data-service="<?php echo $key; ?>" <?php echo $display_notice; ?>><?php echo $service['notice']; ?></span>
							<?php endforeach; ?>
							</p>
						</p>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<div class="edd-fm-tools postbox">
				<script>
				jQuery(document).ready(function() {
					jQuery('#edd_fm_banned_countries_chosen .search-field input').attr( 'placeholder', '<?php echo $args['placeholder']; ?>' );
				});
				</script>
				<h3><span><?php _e( 'Flagged Countries', 'edd-fm' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Purchases made from IPs within the countries below will be put into moderation automatically.', 'edd-fm' ); ?></p>
					<p>
						<?php
							$input = EDD()->html->select( $args );
							echo $input;
						?>
					</p>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<?php
				$geofence_settings  = EDD_Fraud_Monitor()->geofence_settings();
				$display            = $geofence_settings['enabled'] === true ? '' : ' style="display:none;"';
			?>
			<div class="edd-fm-tools postbox">
				<h3><span><?php _e( 'Geofence Settings', 'edd-fm' ); ?></span></h3>
				<div class="inside geofence-settings">

					<p><?php _e( 'Uses both the IP Address during purchase, and the billing information to determine if there is a descrepency in location.', 'edd-fm' ); ?></p>

					<p>
						<input type="checkbox" <?php checked( true, $geofence_settings['enabled'], true ); ?> name="geofence_settings[enabled]" id="geofence-enabled" value="1" /> <label for="geofence-enabled"><?php _e( 'Enable Geofence', 'edd-fm' ); ?></label><br />
					</p>

					<p class="edd-fm-toggle" <?php echo $display; ?>>
						<input class="regular-text" placeholder="<?php echo __( 'Google GeoCode API Key', 'edd-fm' ); ?>" type="text" name="geofence_settings[google_api_key]" id="google-api-key" value="<?php echo $geofence_settings['google_api_key']; ?>" /><br />
						<span class="description">
							<?php
							printf(
								__( 'To use this feature, you need to create a <a href="%s">Google APP and provide access to the GeoCode API</a>.', 'edd-fm' ),
								'http://docs.easydigitaldownloads.com/article/1824-fraud-monitor-configuring-geofencing'
							);
							?>
						</span>
					</p>

					<p class="edd-fm-toggle" <?php echo $display; ?>>
						<?php
							$fence_args         = array(
								'name'             => 'geofence_settings[fence]',
								'class'            => 'edd-fm-geofence-fence-select',
								'id'               => 'edd-fm-geofence-fence',
								'options'          => array( 'country' => __( 'Country', 'edd-fm' ), 'distance' => __( 'Distance', 'edd-fm' ) ),
								'chosen'           => false,
								'selected'         => $geofence_settings['fence'],
								'show_option_all'  => false,
								'show_option_none' => false,
							);
							$input = EDD()->html->select( $fence_args );
							echo $input;
						?>
						<label for="edd-fm-geofence-fence"><?php _e( 'Geofence Type', 'edd-fm' ); ?></label><br />
						<span class="description geofence-type country"<?php echo $geofence_settings['fence'] === 'country' ? '' : ' style="display: none;"'; ?>><?php _e( 'Moderate payments where the IP Address and Billing Information are in different countries.', 'edd-fm' ); ?></span>
						<span class="description geofence-type distance"<?php echo $geofence_settings['fence'] === 'distance' ? '' : ' style="display: none;"'; ?>><?php _e( 'Moderate payments where the IP Address and Billing Information are over the threshold of the defined range.', 'edd-fm' ); ?></span>
					</p>

					<?php $fence_range_display = empty( $display ) && 'distance' === $geofence_settings['fence'] ? '' : ' style="display:none;"'; ?>
					<p class="edd-fm-toggle geofence-range" <?php echo $fence_range_display; ?>>
						<input class="small-text" placeholder="<?php echo __( 'Range', 'edd-fm' ); ?>" type="text" name="geofence_settings[range]" id="geofence-range" value="<?php echo $geofence_settings['range']; ?>" />
						<?php
							$range_args         = array(
								'name'             => 'geofence_settings[range_unit]',
								'class'            => 'edd-fm-geofence-range-unit-select',
								'id'               => 'edd-fm-geofence-range-unit',
								'options'          => array( 'mi' => __( 'Miles', 'edd-fm' ), 'km' => __( 'Kilometers', 'edd-fm' ) ),
								'chosen'           => false,
								'selected'         => $geofence_settings['fence'],
								'show_option_all'  => false,
								'show_option_none' => false,
							);
							$input = EDD()->html->select( $range_args );
							echo $input;
						?>
						<label for="geofence-range"><?php _e( 'Range', 'edd-fm' ); ?></label><br />
						<span class="description"><?php _e( 'Flag any purchase where the IP Address and Billing Information meets or exceeds this distance.', 'edd-fm' ); ?></span>
					</p>

				</div><!-- .inside -->
			</div><!-- .postbox -->

			<div class="edd-fm-tools postbox">
				<h3><span><?php _e( 'Download Country Check', 'edd-fm' ); ?></span></h3>
				<div class="inside">
					<p>
						<input type="checkbox" <?php checked( true, EDD_Fraud_Monitor()->download_country_check(), true ); ?> name="download_country_check" id="download_country_check" value="1" /> <label for="download_country_check"><?php _e( 'Enable download country check', 'edd-fm' ); ?></label><br />
						<span class="description"><?php _e( 'Automatically prevent file downloads when the download is initiated within the first 24 hours and from a different country than the purchase was made from, and flag the payment for review.', 'edd-fm' ); ?></span>
					</p>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<input type="hidden" name="edd_action" value="save_banned_countries" />
			<?php wp_nonce_field( 'edd_banned_country_nonce', 'edd_banned_country_nonce' ); ?>
			<?php submit_button( __( 'Save Settings', 'edd-fm' ), 'primary', 'submit', false ); ?>
		</form>
		<?php
	}

	/**
	 * Save the fraud country settings
	 *
	 * @since  1.0
	 *
	 * @param  array $data The $_POST data being saved
	 * @return void
	 */
	public function save_countries( $data ) {

		if ( ! wp_verify_nonce( $data['edd_banned_country_nonce'], 'edd_banned_country_nonce' ) ) {
			return;
		}

		$registered_services = edd_fm_get_ip_lookup_services();
		$selected_service    = $data['ip_lookup_service'];

		if ( ! array_key_exists( $selected_service, $registered_services ) ) {
			$selected_service = 'hostip_info';
		}

		update_option( '_edd_fm_selected_geoip_service', $selected_service );

		// Sanitize the input
		if ( empty( $data['banned_countries'] ) ) {
			$countries = array();
		} else {
			$countries = array_filter( $data['banned_countries'] );
		}

		update_option( '_edd_country_blacklist', $countries );

		$geofence_settings = $data['geofence_settings'];
		$geofence_settings['enabled'] = ! empty( $geofence_settings['enabled'] ) ? true : false;

		update_option( '_edd_fm_geofence_settings', $geofence_settings );

		if ( ! empty( $data['download_country_check'] ) ) {
			update_option( '_edd_check_country_on_download', true );
		} else {
			delete_option( '_edd_check_country_on_download' );
		}

		do_action( 'eddfm_save_country_settings', $data );
	}

	/**
	 * Render the fraud email settings
	 *
	 * @since  1.0
	 *
	 * @return void
	 */
	public function email_settings_admin() {
		?>
		<form method="post" action="<?php echo admin_url( 'edit.php?post_type=download&page=edd-tools&tab=fraud_monitor&view=email' ); ?>">

			<div class="edd-fm-tools postbox">
				<h3><span><?php _e( 'Email Verification Settings', 'edd-fm' ); ?></span></h3>
				<div class="inside">
					<p>
						<h4><?php _e( 'Flagged email domains', 'edd-fm' ); ?></h4>
						<?php _e( 'Purchases made with emails from the domains below will be held for moderation.', 'edd-fm' ); ?>
					</p>
					<p>
						<textarea name="banned_domains" rows="10" class="large-text"><?php echo implode( "\n", EDD_Fraud_Monitor()->banned_email_domains() ); ?></textarea>
						<span class="description"><?php _e( 'Enter domains, one per line.', 'edd-fm' ); ?></span>
						<br />
						<span class="description">
							<?php _e( 'Example:', 'edd-fm' ); ?>
<pre>
	example.com
	example.co.uk
</pre>
						</span>
					</p>

					<p>
						<h4><?php _e( 'Additional Email Settings', 'edd-fm' ); ?></h4>

					</p>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<input type="hidden" name="edd_action" value="save_fm_email_settings" />
			<?php wp_nonce_field( 'edd_fm_email_settings_nonce', 'edd_fm_email_settings_nonce' ); ?>
			<?php submit_button( __( 'Save Settings', 'edd-fm' ), 'primary', 'submit', false ); ?>
		</form>
		<?php
	}

	/**
	 * Save the fraud email settings
	 *
	 * @since  1.0
	 *
	 * @param  array $data The $_POST data
	 * @return void
	 */
	public function save_email_settings( $data ) {
		if ( ! wp_verify_nonce( $data['edd_fm_email_settings_nonce'], 'edd_fm_email_settings_nonce' ) ) {
			return;
		}

		// Sanitize the input
		$domains       = array_unique( array_filter( array_map( 'trim', explode( "\n", $data['banned_domains'] ) ) ) );
		foreach ( $domains as $key => $domain ) {
			$domains[ $key ] = str_replace( '@', '', $domain );
		}

		$found_domains = array();

		foreach ( $domains as $domain ) {
			if ( strpos( $domain, '.' ) ) {
				// Not a perfect check, but a start
				$found_domains[] = $domain;
			}
		}

		update_option( '_edd_email_domain_blacklist', $found_domains );

		$extended_email_checks = empty( $data['extended_email_checks'] ) ? false : true;
		if ( false === $extended_email_checks ) {
			delete_option( '_edd_fm_extended_email_checks' );
		} else {
			update_option( '_edd_fm_extended_email_checks', true );
		}

		do_action( 'eddfm_save_email_settings', $data );
	}

	/**
	 * Render the fraud behavior section
	 *
	 * @since  1.0
	 *
	 * @return void
	 */
	public function behavior_admin() {
		$thresholds        = EDD_Fraud_Monitor()->thresholds();
		$user_history      = EDD_Fraud_Monitor()->user_history();
		$excluded_gateways = EDD_Fraud_Monitor()->excluded_gateways();

		$has_payments_setting = ! empty( $user_history['has_purchases'] ) ? true : false;
		$ignore_free_setting  = ! empty( $user_history['ignore_free'] )   ? true : false;

		$display_date_limit = empty( $has_payments_setting ) ? ' style="display: none;"' : '';
		?>
		<form method="post" action="<?php echo admin_url( 'edit.php?post_type=download&page=edd-tools&tab=fraud_monitor&view=behavior' ); ?>">

			<div class="edd-fm-tools postbox">
				<h3><span><?php _e( 'User History', 'edd-fm' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Moderate payments based on user history.', 'edd-fm' ); ?></p>
					<p>
						<input type="checkbox" <?php checked( true, $has_payments_setting, true ); ?> value="1" id="user-has-purchases" name="user_history[has_purchases]" /> <label for="user-has-purchases"><?php _e( 'Moderate users with previous payments.', 'edd-fm' ); ?></label>
						<br />
						<span class="description"><?php _e( 'When checked, even users with previously successful purchases will be run through fraud checks.', 'edd-fm' ); ?></span>
					</p>
					<p id="user-created-threshold" <?php echo $display_date_limit; ?>>
						<input type="number" class="small-text" value="<?php echo $user_history['customer_age']; ?>" id="customer-age" name="user_history[customer_age]" /> <label for="customer-age"><?php _e( 'day(s)', 'edd-fm' ); ?></label>
						<br />
						<span class="description"><?php _e( 'Only moderate customers created within the last X day(s). Use 0 to moderate all customer purchases.', 'edd-fm' ); ?></span>
					</p>
					<p id="user-created-threshold">
						<input type="checkbox" value="1" <?php checked( true, $ignore_free_setting, true ); ?> id="customer-ignore-free" name="user_history[ignore_free]" /> <label for="customer-ignore-free"><?php _e( 'Ignore free purchases', 'edd-fm' ); ?></label>
						<br />
						<span class="description"><?php _e( 'When analyzing purchase history, should Fraud Monitor ignore payments that were free?', 'edd-fm' ); ?></span>
					</p>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<div class="edd-fm-tools postbox">
				<h3><span><?php _e( 'Gateway Settings', 'edd-fm' ); ?></span></h3>
				<div class="inside">
					<p>
						<h4><?php _e( 'Exclude Gateways', 'edd-fm' ); ?></h4>
						<?php _e( 'Purchases made with the selected gateways, <strong>will not</strong> be held for moderation.', 'edd-fm' ); ?>
					</p>
					<p>
						<?php $registered_gateways = edd_get_payment_gateways(); ?>
						<?php unset($registered_gateways['manual'] ); ?>
						<?php foreach ( $registered_gateways as $key => $gateway ) : ?>
						<input type="checkbox" <?php checked( true, in_array( $key, $excluded_gateways), true ); ?> value="1" id="excluded-gateway-<?php echo $key; ?>" name="excluded_gateways[<?php echo $key; ?>]" /> <label for="excluded-gateway-<?php echo $key; ?>"><?php echo $gateway['admin_label']; ?></label><br />
						<?php endforeach; ?>
					</p>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<div class="edd-fm-tools postbox">
				<h3><span><?php _e( 'Limit Thresholds', 'edd-fm' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Moderate payments that meet any of the following thresholds.', 'edd-fm' ); ?></p>
					<p>
						<input type="number" min="0" increment="1" id="amount-threshold" name="thresholds[amount]" class="small-text" value="<?php echo $thresholds['amount']; ?>" /> <label for="amount-threshold"><?php _e( 'Total Purchase Amount', 'edd-fm' ); ?></label>
						<br />
						<span class="description"><?php _e( 'Moderate payments with a purchase total over a specific amount. Whole amounts only.', 'edd-fm' ); ?></span>
					</p>

					<p>
						<input type="number" min="0" max="99" increment="1" id="item-threshold" name="thresholds[items]" class="small-text" value="<?php echo $thresholds['items']; ?>" /> <label for="item-threshold"><?php _e( 'Total Purchased Items', 'edd-fm' ); ?></label>
						<br />
						<span class="description"><?php _e( 'Moderate payments that contain X or more items.', 'edd-fm' ); ?></span>
					</p>

					<p>
						<input type="number" min="0" max="99" increment="1" id="bundle-threshold" name="thresholds[bundles]" class="small-text" value="<?php echo $thresholds['bundles']; ?>" /> <label for="bundle-threshold"><?php _e( 'Total Purchased Bundles', 'edd-fm' ); ?></label>
						<br />
						<span class="description"><?php _e( 'Moderate payments that contain X or more bundles.', 'edd-fm' ); ?></span>
					</p>

					<p>
						<span class="description"><?php _e( 'Leave fields empty to remove restriction', 'edd-fm' ); ?></span>
					</p>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<div class="edd-fm-tools postbox">
				<h3><span><?php _e( 'Product Combinations', 'edd-fm' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Moderate payments that have specific product combinations.', 'edd-fm' ); ?></p>
					<p>
						<table id="eddfm-product-combinations" class="wp-list-table widefat fixed posts">
							<thead>
								<tr>
									<th scope="col" class="when-col"><?php _e( 'If cart contains&hellip;', 'edd-fm' ); ?></th>
									<th scope="col"><?php _e( 'and also contains any of the following.', 'edd-fm' ); ?></th>
									<th scope="col" class="remove-col"></th>
								</tr>
							</thead>
							<?php $combinations = EDD_Fraud_Monitor()->product_combinations(); ?>
							<?php if( ! empty( $combinations ) ) : ?>
								<?php foreach( $combinations as $key => $combination ) : ?>
								<tr data-key="<?php echo $key; ?>" class="condition-row">
									<td>
										<?php
										echo EDD()->html->product_dropdown( array(
											'name'     => 'product_combos[' . $key . '][item1]',
											'id'       => 'product_combos[' . $key . '][item1]',
											'selected' => ! empty( $combination['item1'] ) ? $combination['item1'] : false,
											'chosen'   => true,
										) );
										?>
									</td>
									<td>
										<?php
										echo EDD()->html->product_dropdown( array(
											'name'     => 'product_combos[' . $key . '][item2][]',
											'id'       => 'product_combos[' . $key . '][item2][]',
											'selected' => ! empty( $combination['item2'] ) ? $combination['item2'] : false,
											'multiple' => true,
											'chosen'   => true,
										) );
										?>
									</td>
									<td>
										<a href="#" class="eddfm_remove_condition" style="background: url(<?php echo admin_url('/images/xit.gif'); ?>) no-repeat;">&times;</a>
									</td>
								</tr>
								<?php endforeach; ?>
							<?php else : ?>
								<tr data-key="0" class="condition-row">
									<td>
										<?php
										echo EDD()->html->product_dropdown( array(
											'name' => 'product_combos[0][item1]',
											'id'   => 'product_combos[0][item1]',
										) );
										?>
									</td>
									<td>
										<?php
										echo EDD()->html->product_dropdown( array(
											'name'     => 'product_combos[0][item2][]',
											'id'       => 'product_combos[0][item2][]',
											'chosen'   => true,
											'multiple' => true,
											'selected' => false
										) );
										?>
									</td>
									<td>
										<a href="#" class="eddfm_remove_condition" style="background: url(<?php echo admin_url('/images/xit.gif'); ?>) no-repeat;">&times;</a>
									</td>
								</tr>
							<?php endif; ?>
						</table>
						<p>
							<span class="button-secondary" id="eddfm_add_condition"><?php _e( 'Add Condition', 'edd-fm' ); ?></span>
						</p>
					</p>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<input type="hidden" name="edd_action" value="save_fm_behaviors" />
			<?php wp_nonce_field( 'edd_behavior_nonce', 'edd_behavior_nonce' ); ?>
			<?php submit_button( __( 'Save Settings', 'edd-fm' ), 'primary', 'submit', false ); ?>
		</form>
		<?php
	}

	/**
	 * Save the fraud behavior settings
	 *
	 * @since  1.0
	 *
	 * @param  array $data The $_POST data
	 * @return void
	 */
	public function save_behaviors( $data ) {
		if ( ! wp_verify_nonce( $data['edd_behavior_nonce'], 'edd_behavior_nonce' ) ) {
			return;
		}

		update_option( '_edd_fm_thresholds', $data['thresholds'] );

		if ( empty( $data['product_combos'] ) || ! is_array( $data['product_combos'] ) ) {
			$product_combos = array();
		} else {
			$product_combos = $data['product_combos'];
			foreach ( $product_combos as $key => $combo ) {
				if ( ! empty( $combo['item2'] ) ) {
					$product_combos[ $key ]['item2'] = array_map( 'absint', $combo['item2'] );
				}
			}
		}
		update_option( '_edd_fm_product_combinations', $product_combos );

		$excluded_gateways = ! empty( $_POST['excluded_gateways'] ) ? $_POST['excluded_gateways'] : array();
		foreach ( $excluded_gateways as $gateway => $exclude ) {
			if ( '1' === $exclude ) {
				$excluded_gateways[] = $gateway;
			}
		}

		update_option( '_edd_fm_excluded_gateways', $excluded_gateways );

		$user_history = array();
		if ( is_array( $data['user_history'] ) ) {
			$user_history = $data['user_history'];
		}

		if ( ! empty( $user_history ) ) {
			update_option( '_edd_fm_user_history', $user_history );
		} else {
			delete_option( '_edd_fm_user_history' );
		}

		do_action( 'eddfm_save_behavior_settings', $data );

	}


}
