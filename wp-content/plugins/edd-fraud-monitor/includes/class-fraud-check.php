<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EDD_Fraud_Monitor_Check {

	private $payment_id = null;

	public $bad_email;
	public $ip;
	public $bad_ip;
	public $bad_country;
	public $bad_range;
	public $over_limits;
	public $fraud_notes;
	public $is_fraud;
	public $bad_combo;

	public function __construct( $payment_id, $run_on_load = true ) {

		$this->payment_id = $payment_id;

		$this->amount  = edd_get_payment_amount( $this->payment_id );
		$this->email   = edd_get_payment_user_email( $this->payment_id );
		$this->ip      = edd_get_payment_user_ip( $this->payment_id );
		$this->gateway = edd_get_payment_gateway( $this->payment_id );

		if ( true === $run_on_load ) {
			$this->run_fraud_check();
		}
	}

	/**
	 * Maybe is fraud
	 *
	 * @since  1.0
	 *
	 * @return boolean If the payment has been marked as fraud
	 */
	public function run_fraud_check() {

		$this->is_fraud        = false;
		$this->fraud_notes     = array();

		$moderate_free         = EDD_Fraud_Monitor()->moderate_free();
		$excluded_gateways     = EDD_Fraud_Monitor()->excluded_gateways();
		$gateway_excluded      = in_array( $this->gateway, $excluded_gateways );

		if ( ( $this->amount > 0 || $moderate_free ) ) {

			$user_history_settings = EDD_Fraud_Monitor()->user_history();
			$check_prev_customers  = ! empty( $user_history_settings['has_purchases'] );
			$has_purchases         = $this->has_purchases();

			if ( $check_prev_customers || ! $has_purchases ) {

				$customer_creation_check = absint( $user_history_settings['customer_age'] );

				if ( ! empty( $customer_creation_check ) ) {

					$customer     = new EDD_Customer( edd_get_payment_customer_id( $this->payment_id ) );
					$date_created = strtotime( $customer->date_created );
					$customer_age = floor( ( time() - $date_created ) / DAY_IN_SECONDS );

					if ( $customer_age > $customer_creation_check ) {
						return $this->is_fraud;
					}
				}

				$this->bad_email   = $this->is_bad_email( $this->email );
				$this->bad_country = $this->is_bad_country();
				$this->bad_range   = $this->is_bad_range();
				$this->bad_ip      = $this->is_bad_ip();
				$this->over_limits = $this->is_purchase_over_thresholds();
				$this->bad_combo   = $this->has_bad_combination();

				if ( ! empty ( $this->fraud_notes ) ) {
					$this->is_fraud = true;
				}

			}

		}

		if ( $this->is_fraud && $gateway_excluded ) {

			// Set in the array so we can run an accurate test on if it's bypassed
			$this->fraud_notes[] = sprintf( __( 'This payment failed fraud checks but has been allowed due to a gateway exception: %s', 'edd-fm' ), $this->gateway );
			$this->is_fraud = false;
			$this->log_reasons();

		} elseif ( $this->is_fraud ) {

			$this->mark_as_fraud();

		}

		return $this->is_fraud;

	}

	/**
	 * Check if the payment was made from a country that's in the flagged list
	 *
	 * @since  1.0
	 *
	 * @return boolean [description]
	 */
	public function is_bad_country() {

		$return = false;

		$selected_geoip_service = EDD_Fraud_Monitor()->selected_geoip_service();


		if ( doing_filter( 'edd_file_download_has_access' ) ) {

			$return = apply_filters( 'edd_fm_geoip_download_' . $selected_geoip_service, $return, edd_get_ip(), $this->payment_id );

		} else {

			$return = apply_filters( 'edd_fm_geoip_' . $selected_geoip_service, $return, $this, $this->payment_id );

		}

		return $return;
	}

	/**
	 * Checks the GeoFence API for defined rules
	 *
	 * @since  1.1
	 * @return bool
	 */
	public function is_bad_range() {

		$bad_range = false;

		$geofence_settings = EDD_Fraud_Monitor()->geofence_settings();
		if ( false === $geofence_settings['enabled'] ) {
			return $bad_range;
		}

		$payment      = edd_get_payment( $this->payment_id );
		$billing_info = array(
			'city'    => ! empty( $payment->address['city'] )    ? $payment->address['city']    : false,
			'state'   => ! empty( $payment->address['state'] )   ? $payment->address['state']   : false,
			'zip'     => ! empty( $payment->address['zip'] )     ? $payment->address['zip']     : false,
			'country' => ! empty( $payment->address['country'] ) ? $payment->address['country'] : false,
		);

		$billing_info = array_filter( $billing_info );
		if ( ! empty( $this->ip ) && ! empty( $billing_info ) ) {
			$geofence_check = new EDD_FM_Geofence( $this->ip, $billing_info );
			$bad_range      = $geofence_check->inside_fence ? false : true;
			if ( $bad_range ) {
				$this->fraud_notes[] = $geofence_check->error_message;
			}
		} else {
			$payment->add_note( __( 'Geofence check skipped due to empty billing or IP', 'edd-fm' ) );
		}

		return $bad_range;

	}

	/**
	 *  Check if the email address passes our validation
	 *
	 * @since  1.0
	 *
	 * @param  string  $email The email address
	 * @return boolean        If the email address passes all checks
	 */
	public function is_bad_email( $email ) {
		$parts = explode( '@', $email );
		$result = false;

		if ( true === $this->is_domain_banned( $parts[1] ) ) {
			$result = true;
		}

		if ( EDD_Fraud_Monitor()->use_extended_email_checks() && false === $this->passes_extended_email_checks( $email ) ) {
			$result = true;
		}

		return $result;
	}

	/**
	 * Check if the IP address on the payment is in our list of flagged IPs
	 *
	 * @since  1.0
	 *
	 * @return boolean If the IP address is flagged or not
	 */
	public function is_bad_ip() {
		$ip = $this->ip;

		$banned_ips = EDD_Fraud_Monitor()->banned_ips();

		if ( in_array( $ip, $banned_ips ) ) {

			if( doing_filter( 'edd_file_download_has_access' ) ) {

				$this->fraud_notes[] = sprintf( __( 'IP during download is banned: %s', 'edd-fm' ), $this->ip );

			} else {

				$this->fraud_notes[] = sprintf( __( 'Bad IP: %s', 'edd-fm' ), $this->ip );

			}

			return true;
		}

		return false;
	}

	/**
	 * Checks if the domain associated with the email address is flagged
	 * @since  1.0
	 *
	 * @param  stirng  $domain The domain associted with the email address
	 * @return boolean         If the domain is in our flagged list
	 */
	public function is_domain_banned( $domain ) {
		$banned_domains = EDD_Fraud_Monitor()->banned_email_domains();

		if ( in_array( $domain, $banned_domains ) ) {
			$this->fraud_notes[] = __( 'Bad Email Domain', 'edd-fm' );
			return true;
		}

		return false;

	}

	/**
	 * Additional email checks
	 *
	 * @since  1.0
	 *
	 * @param  string $email The email address
	 * @return boolean       If the email address given passes the checks
	 */
	public function passes_extended_email_checks( $email ) {

		$parts = explode( '@', $email );

		// Anything with 4 or more numbers in the email address is subject
		preg_match_all( "/[0-9]/", $parts[0], $digits );
		if ( count( $digits[0] ) >= 4 ) {
			$this->fraud_notes[] = __( 'Bad Email Format: Too Many Numbers', 'edd-fm' );
			return false;
		}

		// Anything with 3 or more . is suspect, seen in spam comments
		if ( substr_count( $parts[0], '.' ) >= 3 ) {
			$this->fraud_notes[] = __( 'Bad Email Format: Too Many Periods', 'edd-fm' );
			return false;
		}

		return true;

	}

	/**
	 * Checks if a payment has crosses specifiec thresholds
	 *
	 * @since  1.0
	 *
	 * @return boolean If the payment has crosses thresholds
	 */
	public function is_purchase_over_thresholds() {

		$amount               = $this->amount;
		$payment_cart_details = edd_get_payment_meta_cart_details( $this->payment_id );
		$total_items          = count( $payment_cart_details );

		$bundles = 0;
		foreach ( $payment_cart_details as $cart_item ) {
			if ( edd_is_bundled_product( $cart_item['id'] ) ) {
				$bundles++;
			}
		}

		$over_amount  = $this->is_over_amount_threshold( $amount );
		$over_items   = $this->is_over_total_item_threshold( $total_items );
		$over_bundles = $this->is_over_bundle_threshold( $bundles );

		if ( $over_amount || $over_items || $over_bundles ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks for the specific total amount threshold
	 *
	 * @since  1.0
	 *
	 * @param  float  $amount The total purchase amount of the payment
	 * @return boolean        true if the amount is at or over our limit, or false if it's under
	 */
	public function is_over_amount_threshold( $amount ) {
		$thresholds = EDD_Fraud_Monitor()->thresholds();
		$over_limit = false;

		if ( ! empty( $thresholds['amount'] ) && $amount >= $thresholds['amount'] ) {
			$this->fraud_notes[] = sprintf( __( 'Threshold Triggered: Purchase Amount Total %s', 'edd-fm' ), edd_currency_filter( edd_format_amount( $amount ) ) );
			$over_limit = true;
		}

		return $over_limit;
	}

	/**
	 * Checks if the number of items purchased crosses a threshold
	 *
	 * @since  1.0
	 *
	 * @param  integer  $total_items The number of items in the payment
	 * @return boolean               If the total items is at or over our limit
	 */
	public function is_over_total_item_threshold( $total_items ) {
		$thresholds = EDD_Fraud_Monitor()->thresholds();
		$over_limit = false;

		if ( ! empty( $thresholds['items'] ) && $total_items >= $thresholds['items'] ) {
			$this->fraud_notes[] = sprintf( __( 'Threshold Triggered: Total Items %d', 'edd-fm' ), $total_items );
			$over_limit = true;
		}

		return $over_limit;
	}

	/**
	 * Checks if the numberm of bundles purchased crosses our threshold
	 *
	 * This was added specifically for the EDD Team, but is useful as bundles are usually more expensive
	 * and we've witnessed that many fraud purcahses have multiple bundles to 'maximize' the fraud attempt
	 *
	 * @since  1.0
	 *
	 * @param  integer  $bundles The number of bundles in the payment
	 * @return boolean           If this number is over our limit or not
	 */
	public function is_over_bundle_threshold( $bundles ) {
		$thresholds = EDD_Fraud_Monitor()->thresholds();
		$over_limit = false;

		if ( ! empty( $thresholds['bundles'] ) && $bundles >= $thresholds['bundles'] ) {
			$this->fraud_notes[] = sprintf( __( 'Threshold Triggered: Bundle Count %d', 'edd-fm' ), $bundles );
			$over_limit = true;
		}

		return $over_limit;
	}

	/**
	 * Checks if the cart contains a conbination that we have set to falg
	 *
	 * @since  1.0
	 *
	 * @return boolean If the cart contains an combination that is configured to flag the payment or not
	 */
	public function has_bad_combination() {
		$product_combinations = EDD_Fraud_Monitor()->product_combinations();
		$bad_combination = false;

		if ( ! empty( $product_combinations ) ) {

			$cart_contents     = edd_get_payment_meta_downloads( $this->payment_id );
			$cart_download_ids = wp_list_pluck( $cart_contents, 'id' );

			foreach ( $product_combinations as $key => $combination ) {

				if ( ! in_array( $combination['item1'], $cart_download_ids ) ) {
					continue;
				}

				$intersect = array_intersect( $cart_download_ids, $combination['item2'] );

				if ( count( $intersect ) > 0 ) {
					$bad_combination = true;
					$this->fraud_notes[] = sprintf( __( 'Bad Product Combo: #%d', 'edd-fm' ), $key );
					break;
				}

			}

		}

		return $bad_combination;

	}

	/**
	 * The method used to mark a pyament as fraud, does the business end of things\
	 * Adds the _edd_maybe_is_fraud and _edd_maybe_is_fraud_reason payment meta
	 * Also logs a payment note with the reasons
	 *
	 * @since  1.0
	 *
	 * @return void
	 */
	public function mark_as_fraud() {

		// update the payment meta
		update_post_meta( $this->payment_id, '_edd_maybe_is_fraud', true );

		$this->log_reasons();

		// add IP to blacklist
		EDD_Fraud_Monitor()->add_ip_to_blacklist( array( $this->ip ) );

		// Add email address to banned list
		EDD_Fraud_Monitor()->add_email_to_blacklist( $this->email );

	}

	/**
	 * Check if the customer in this fraud check has previous purchases
	 *
	 * @since  1.1
	 * @return boolean If the user has purcahses or not, meeting criteria
	 */
	public function has_purchases() {

		$user_history_settings = EDD_Fraud_Monitor()->user_history();
		$ignore_free_purchases = ! empty( $user_history_settings['ignore_free'] );

		$payments              = edd_get_users_purchases( $this->email, -1 );
		$has_purchases         = ! empty( $payments );

		// If we should ignore free purcahses, look for a non-zero payment.
		if ( $ignore_free_purchases && $has_purchases ) {
			foreach ( $payments as $payment ) {

				if ( $payment->total > 0 ) {
					$has_purchases = true;
					break;
				}

				$has_purchases = false;

			}
		}

		return $has_purchases;

	}

	/**
	 * Logs the reasons found into a payment note
	 *
	 * @since  1.0.3
	 * @return void
	 */
	private function log_reasons() {
		// log reason
		$reasons = $this->fraud_notes;
		$reasons = implode( "\n", $reasons );

		update_post_meta( $this->payment_id, '_edd_maybe_is_fraud_reason', $reasons );
		// Log a note about possible fraud
		edd_insert_payment_note( $this->payment_id, sprintf( __( 'This payment was flagged as possible fraud due to %s.', 'edd-fm' ), $reasons ) );
	}

}
