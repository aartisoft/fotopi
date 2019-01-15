<?php

/**
 * Class EDD_FM_Geofence
 *
 * @copyright   Copyright (c) 2017, Easy Digital Downloads, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class EDD_FM_Geofence {

	private $ip_address;
	private $billing_info;
	private $settings;

	public $inside_fence  = true;
	public $error_message = false;

	/**
	 * EDD_FM_Geofence constructor.
	 *
	 * @since 1.1
	 *
	 * @param string $ip_address   The IP Address to check location for.
	 * @param array $billing_info Contains an array of values that relate to the address we're looking for
	 *                            (minimum of city, state, postal code and country)
	 */
	public function __construct( $ip_address = '', $billing_info = array() ) {

		$this->ip_address   = $ip_address;
		$this->billing_info = $billing_info;
		$this->settings     = EDD_Fraud_Monitor()->geofence_settings();

		$this->inside_fence = $this->run_check();
	}

	/**
	 * Get the IP Address Geo Location data
	 *
	 * @since 1.1
	 * @return array|bool
	 */
	private function get_ip_location() {
		$ip_location = edd_fm_ipinfo_io_get_geo_data( $this->ip_address );
		return ! empty( $ip_location ) ? $ip_location : false;
	}

	/**
	 * Get the Billing information Geo Location data
	 *
	 * @since 1.1
	 * @return array|bool
	 */
	private function get_billing_location() {
		$geocode = new EDD_FM_Geocode_API( $this->billing_info );
		$results = $geocode->get_results();
		return ! empty( $results ) ? $results : false;
	}

	/**
	 * Get the country associated with the IP Address (2 character country code)
	 *
	 * @since 1.1
	 * @return string|bool
	 */
	private function get_ip_country() {
		$ip_location = edd_fm_ipinfo_io_get_country_data( $this->ip_address );
		return ! empty( $ip_location['country_code'] ) ? $ip_location['country_code'] : false;
	}

	/**
	 * Get the country associated with the billing information.
	 *
	 * @since 1.1
	 * @return bool|mixed
	 */
	private function get_billing_country() {
		$countries = edd_get_country_list();

		foreach ( $this->billing_info as $address_part ) {
			if ( ! array_key_exists( $address_part, $countries ) ) {
				continue;
			}

			$country = $address_part;
			break;
		}

		return ! empty( $country ) ? $country : false;
	}

	/**
	 * Run the check for the geofence based off settings provided.
	 *
	 * @since 1.1
	 * @return bool
	 */
	private function run_check() {
		$enabled = $this->settings['enabled'];
		if ( false === $enabled ) {
			return true;
		}

		$fence_type = $this->settings['fence'];
		if ( 'distance' === $fence_type ) {
			$fence_range = $this->settings['range'];
			$range_unit  = $this->settings['range_unit'];
		}

		$inside_fence = true;

		switch( $fence_type ) {

			case 'country':
				$ip_country      = strtoupper( $this->get_ip_country() );
				$billing_country = strtoupper( $this->get_billing_country() );

				if ( ! empty( $ip_country ) && ! empty( $billing_country ) && $ip_country !== $billing_country ) {
					$inside_fence = false;
					$this->error_message = sprintf( __( 'Geofence Failure: IP - %s, Billing - %s', 'edd-fm' ), $ip_country, $billing_country );
				}
				break;

			case 'distance':
				$ip_location      = $this->get_ip_location();
				$billing_location = $this->get_billing_location();

				$distance_calculator = new EDD_FM_Distance_Calculation( $ip_location, $billing_location, $range_unit );
				$distance = $distance_calculator->get_distance();

				if ( $distance > $fence_range ) {
					$inside_fence = false;
					$this->error_message = sprintf( __( 'Geofence Failure: Distance between IP and Billing - %d %s', 'edd-fm'), $distance, $range_unit );
				}
				break;

		}

		return $inside_fence;

	}

}