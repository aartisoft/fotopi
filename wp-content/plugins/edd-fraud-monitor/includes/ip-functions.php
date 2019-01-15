<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the registered GeoIP Services
 *
 * @since  1.0.1
 * @return array The GeoIP Services registered
 */
function edd_fm_get_ip_lookup_services() {
	$services = apply_filters( 'edd_fm_registered_ip_lookup_services', array() );

	return $services;
}

/**
 * Register our default GeoIP lookup services
 *
 * @since  1.0.1
 * @return void
 */
function edd_fm_default_ip_lookup_service() {
	return array(
		'hostip_info' => array(
			'label'  => 'hostip.info',
			'notice' => __( 'HostIP.info is a crowdsourced GeoIP Service that is free to use with no limitations on lookups. Due to being crowdsourced, it relies on user-supplied data, and therefore can be slightly inaccurate in edge cases.', 'edd-fm' ),
		),
		'ipinfo_io'   => array(
			'label'  => 'ipinfo.io',
			'notice' => __( 'IPInfo.io is a GeoIP Service that allows up 1000 IP lookups per day, for free.', 'edd-fm' ),
		),
	);
}
add_filter( 'edd_fm_registered_ip_lookup_services', 'edd_fm_default_ip_lookup_service', 10, 1 );

/**
 * The callback filter for running the HostIP.info GeoLocation check
 *
 * @since  1.0.1
 * @param  bool $bad_country          If the country has been determiend to be in the blocked list
 * @param  obj  $fraud_check_object    The instance of the fraud check that is requesting the information
 * @param  int  $payment_id            The payment ID being checked for fraud
 * @return bool                       If the country found is in the flagged list
 */
function edd_fm_hostip_info_lookup_on_purchase( $bad_country, $fraud_check_object, $payment_id = 0 ) {

	$country_data = edd_fm_hostip_info_api( $fraud_check_object->ip, $payment_id );

	if ( true === $country_data['bad_country'] ) {
		$fraud_check_object->fraud_notes[] = sprintf( __( 'Bad Country: %s', 'edd-fm' ), $country_data['country_name'] );
	}

	return $country_data['bad_country'];

}
add_filter( 'edd_fm_geoip_hostip_info', 'edd_fm_hostip_info_lookup_on_purchase', 10, 3 );

/**
 * Function to pass in an IP and payment ID to verify the download is not from a banned country
 *
 * @since  1.0.4
 * @param  string  $ip         The IP downloading the file
 * @param  integer $payment_id The Payment ID the download is associated with
 * @return bool                If the IP downloading is in a banned country
 */
function edd_fm_hostip_info_lookup_on_download( $is_fraud = false, $ip = '', $payment_id = 0 ) {

	if ( ! empty( $ip ) && ! empty( $payment_id ) ) {
		$country_data = edd_fm_hostip_info_api( $ip, $payment_id );

		if ( true === $country_data['bad_country'] ) {

			$fraud_check = new EDD_Fraud_Monitor_Check( $payment_id, false );
			$fraud_check->is_fraud = true;
			$fraud_check->fraud_notes[] = sprintf( __( 'Bad Country during download: %s', 'edd-fm' ), $country_data['country_name'] );
			$fraud_check->mark_as_fraud();

			$is_fraud = true;
		}
	}

	return $is_fraud;

}
add_filter( 'edd_fm_geoip_download_hostip_info', 'edd_fm_hostip_info_lookup_on_download', 10, 3 );

/**
 * Run a GeoIP Lookup for HostIP.info
 *
 * @since  1.0.4
 * @param  string $ip The IP Address to check
 * @return array      The data returned from HostIP.info for the given IP
 */
function edd_fm_hostip_info_api( $ip, $payment_id ) {

	$default_data = array(
		'bad_country' => false,
		'country_name' => '',
		'country_code' => ''
	);

	$found_country_data = array();

	$api = wp_remote_get( 'http://api.hostip.info/get_json.php?ip=' . $ip );

	if( ! is_wp_error( $api ) ) {

		$response = wp_remote_retrieve_body( $api );

		try {
			// decode response
			$response = json_decode( $response );

			$country_name = $response->country_name;
			$country_code = $response->country_code;

			$banned_countries = EDD_Fraud_Monitor()->banned_countries();
			$bad_country      = false;

			if ( in_array( $country_code, $banned_countries ) ) {
				$bad_country = true;
			}

			$found_country_data = array(
				'bad_country'  => $bad_country,
				'country_name' => $country_name,
				'country_code' => $country_code,
			);

		} catch( Exception $e ) {

			edd_insert_payment_note( $payment_id, __( 'GeoIP lookup returned invalid response. Unable to detect country.', 'edd-fm' ) );

		}

	} else {

		edd_insert_payment_note( $payment_id, __( 'GeoIP lookup unavailable at time of query. Unable to detect country.', 'edd-fm' ) );

	}
	$return = wp_parse_args( $found_country_data, $default_data );

	return $return;
}

/**
 * The callback filter for running the IPInfo.io GeoLocation check
 *
 * @since  1.0.1
 * @param  bool $bad_country          If the country has been determiend to be in the blocked list
 * @param  obj  $fraud_check_object   The instance of the fraud check that is requesting the information
 * @param  int  $payment_id           The payment ID being checked for fraud
 * @return bool                       If the country found is in the flagged list
 */
function edd_fm_ipinfo_io_lookup_on_purchase( $bad_country, $fraud_check_object, $payment_id ) {

	$country_data = edd_fm_ipinfo_io_get_country_data( $fraud_check_object->ip, $payment_id );

	if ( true === $country_data['bad_country'] ) {
		$fraud_check_object->fraud_notes[] = sprintf( __( 'Bad Country: %s', 'edd-fm' ), $country_data['country_name'] );
	}

	return $country_data['bad_country'];

}
add_filter( 'edd_fm_geoip_ipinfo_io', 'edd_fm_ipinfo_io_lookup_on_purchase', 10, 3 );

/**
 * Function to pass in an IP and payment ID to verify the download is not from a banned country
 *
 * @since  1.0.4
 * @param  string  $ip         The IP downloading the file
 * @param  integer $payment_id The Payment ID the download is associated with
 * @return bool                If the IP downloading is in a banned country
 */
function edd_fm_ipinfo_io_lookup_on_download( $is_fraud = false, $ip = '', $payment_id = 0 ) {

	if ( ! empty( $ip ) && ! empty( $payment_id ) ) {
		$country_data = edd_fm_ipinfo_io_get_country_data( $ip, $payment_id );

		if ( true === $country_data['bad_country'] ) {

			$fraud_check = new EDD_Fraud_Monitor_Check( $payment_id, false );
			$fraud_check->is_fraud = true;
			$fraud_check->fraud_notes[] = sprintf( __( 'Bad Country during download: %s', 'edd-fm' ), $country_data['country_name'] );
			$fraud_check->mark_as_fraud();

			$is_fraud = true;

		} else {

			$timeframe_check = EDD_Fraud_Monitor()->download_country_check();
			$payment_ip = edd_get_payment_user_ip( $payment_id );
			$current_time  = current_time( 'timestamp' );
			$payment_time  = strtotime( edd_get_payment_completed_date( $payment_id ) );
			$compare_limit = apply_filters( 'edd_fm_download_country_check_limit', DAY_IN_SECONDS, $payment_id );

			if ( $timeframe_check && ( $current_time - $payment_time ) < $compare_limit ) {

				$payment_country_info = edd_fm_ipinfo_io_get_country_data( $payment_ip, $payment_id );

				if ( strtolower( $country_data['country_code'] ) !== strtolower( $payment_country_info['country_code'] ) ) {

						$fraud_check = new EDD_Fraud_Monitor_Check( $payment_id, false );
						$fraud_check->is_fraud = true;
						$fraud_check->fraud_notes[] = sprintf( __( 'Country changed between purchase and download. Purchased: %s. Downloaded: %s', 'edd-fm' ), $payment_country_info['country_name'], $country_data['country_name'] );
						$fraud_check->mark_as_fraud();

						$is_fraud = true;

					}

			}

		}

	}

	return $is_fraud;

}
add_filter( 'edd_fm_geoip_download_ipinfo_io', 'edd_fm_ipinfo_io_lookup_on_download', 10, 3 );

/**
 * Get the country data for an IP Address
 *
 * @since 1.1
 * @param $ip
 * @param $payment_id
 *
 * @return array
 */
function edd_fm_ipinfo_io_get_country_data( $ip, $payment_id = false ) {

	$default_data = array(
		'bad_country' => false,
		'country_name' => '',
		'country_code' => ''
	);
	$found_country_data = array();

	$response = edd_fm_ipinfo_io_api( $ip, $payment_id, 'geo' );
	if ( ! empty( $response['error'] ) ) {
		if ( $payment_id ) {
			edd_insert_payment_note( $payment_id, $response['message'] );
		}
	}  else {
		try {
			// decode response
			$response      = json_decode( $response, true );
			$country_code  = strtoupper( trim( $response['country'] ) );
			$edd_countries = edd_get_country_list();
			$country_name  = array_key_exists( $country_code, $edd_countries ) ? $edd_countries[ $country_code ] : $country_code;
			$banned_countries = EDD_Fraud_Monitor()->banned_countries();
			$bad_country      = false;
			if ( in_array( $country_code, $banned_countries ) ) {
				$bad_country = true;
			}
			$found_country_data = array(
				'bad_country'  => $bad_country,
				'country_name' => $country_name,
				'country_code' => $country_code,
			);
		} catch( Exception $e ) {
			if ( $payment_id ) {
				edd_insert_payment_note( $payment_id, __( 'GeoIP lookup returned invalid response. Unable to detect country.', 'edd-fm' ) );
			}
		}
	}


	$country_data = wp_parse_args( $found_country_data, $default_data );

	return $country_data;

}

/**
 * Get the country data for an IP Address
 *
 * @since 1.1
 * @param $ip
 * @param $payment_id
 *
 * @return array
 */
function edd_fm_ipinfo_io_get_geo_data( $ip, $payment_id = false ) {

	$geo_data = array();

	$response = edd_fm_ipinfo_io_api( $ip, $payment_id, 'geo' );
	if ( ! empty( $response['error'] ) ) {
		if ( $payment_id ) {
			edd_insert_payment_note( $payment_id, $response['message'] );
		}
	}  else {
		try {
			$response    = json_decode( $response, true );
			$coordinates = explode( ',', $response['loc'] );
			$geo_data = array(
				'latitude'  => $coordinates[0],
				'longitude' => $coordinates[1],
			);
		} catch( Exception $e ) {
			if ( $payment_id ) {
				edd_insert_payment_note( $payment_id, __( 'GeoIP lookup returned invalid response. Unable to detect country.', 'edd-fm' ) );
			}
		}
	}

	return $geo_data;

}

/**
 * Run a GeoIP Lookup for ipinfo.io
 *
 * @since  1.0.4
 * @param string $ip The IP Address to check
 * @param int    $payment_id
 * @since 1.1
 * @param string $endpoint The endpoint to call in the API
 *
 * @return array      The data returned from ipinfo.io for the given IP
 */
function edd_fm_ipinfo_io_api( $ip, $payment_id, $endpoint = 'country' ) {

	// Only make one call per page load for the same IP
	global $requested_data_ipinfo_io;
	if ( is_array( $requested_data_ipinfo_io ) && ! empty( $requested_data_ipinfo_io[ $ip ]) ) {
		return $requested_data_ipinfo_io[ $ip ];
	}

	$api = wp_remote_get( 'http://ipinfo.io/' . $ip . '/' . $endpoint );

	/* TODO: Account for API Key as a constant */

	if( ! is_wp_error( $api ) ) {

		$response = wp_remote_retrieve_body( $api );
		$requested_data_ipinfo_io[ $ip ] = $response;

	} else {

		$response = array( 'error' => true, 'message' => __( 'GeoIP lookup unavailable at time of query. Unable to detect country.', 'edd-fm' ) );

	}

	return $response;

}
