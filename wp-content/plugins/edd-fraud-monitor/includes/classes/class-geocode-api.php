<?php

class EDD_FM_Geocode_API {

	private $location_data;
	private $api_key;
	private $api_url = 'https://maps.googleapis.com/maps/api/geocode/json';

	/**
	 * EDD_FM_Geocode_API constructor.
	 *
	 * @since 1.1
	 * @param array  $location_data
	 * @param string $api_key
	 */
	public function __construct( $location_data = array(), $api_key = '' ) {
		$geofence_settings = EDD_Fraud_Monitor()->geofence_settings();
		$this->api_key = empty( $api_key ) ? $geofence_settings['google_api_key'] : $api_key;

		$this->location_data = urlencode( implode( ',', $location_data ) );
	}

	/**
	 * Get the results from the GeoCode API
	 *
	 * @since 1.1
	 * @return array
	 */
	public function get_results() {
		global $geocode_lookups;
		if ( is_array( $geocode_lookups ) && ! empty( $geocode_lookups[ $this->location_data ]) ) {
			return $geocode_lookups[ $this->location_data ];
		}

		$results = array();

		try {

			$request_url = $this->api_url . '?address=' . $this->location_data . '&key=' . $this->api_key;
			$response    = wp_remote_retrieve_body( wp_remote_get( $request_url ) );
			$response    = json_decode( $response, true );

			if ( 'OK' !== $response['status'] ) {
				throw new Exception( 'Geocode Status: ' . $response['status'] );
			}

			if ( ! empty( $response['results'][0]['geometry']['location'] ) ) {
				$geo_location = $response['results'][0]['geometry']['location'];
				$results = array(
					'latitude'  => $geo_location['lat'],
					'longitude' => $geo_location['lng'],
				);

				$geocode_lookups[ $this->location_data ] = $results;
			}

		} catch( Exception $e ) {
			error_log( $e->getMessage() );
			$results = array();
		}

		return $results;
	}

}