<?php
/**
 * Class EDD_FM_Distance_Calculation
 *
 * Given two sets of Latitude and Longitude for two points on earth, determine the distance
 * between them by either miles (mi) or kilometers (km)
 *
 * Adopted from: http://rosettacode.org/wiki/Haversine_formula#PHP
 *
 * @copyright   Copyright (c) 2017, Easy Digital Downloads, LLC
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.1
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class EDD_FM_Distance_Calculation {

	private $ip_location;
	private $billing_location;
	private $unit;

	/**
	 * EDD_FM_Distance_Calculation constructor.
	 *
	 * @since 1.1
	 *
	 * @param array  $ip_location      Contains key value pair of longitude and latitude
	 * @param array  $billing_location Contains key value pair of longitude and latitude
	 * @param string $unit             The unit to calculate in 'mi' (miles) or 'km' (kilometers)
	 */
	public function __construct( $ip_location = array(), $billing_location = array(), $unit = 'mi' ) {
		$this->ip_location = array(
			'latitude'  => deg2rad( $ip_location['latitude'] ),
			'longitude' => deg2rad( $ip_location['longitude'] ),
		);

		$this->billing_location = array(
			'latitude'  => deg2rad( $billing_location['latitude'] ),
			'longitude' => deg2rad( $billing_location['longitude'] ),
		);

		$this->unit      = $unit;
	}

	/**
	 * Return the radius of the earth, depending on the requested unit of measurement
	 *
	 * 3959 miles
	 * 6371 kilometers
	 *
	 * @since 1.1
	 * @return int
	 */
	private function get_radius() {
		return $this->unit === 'mi' ? 3959 : 6371;
	}

	/**
	 * Get the distance between the two points provided when instantiating the class
	 *
	 * @since 1.1
	 * @return float
	 */
	public function get_distance() {
		$earths_radius = $this->get_radius();

		$lat_diff   = $this->ip_location['latitude'] - $this->billing_location['latitude'];
		$long_diff  = $this->ip_location['longitude'] - $this->billing_location['longitude'];

		$a             = sin( $lat_diff / 2 ) * sin( $lat_diff / 2 ) +
		                 cos( $this->ip_location['latitude'] ) * cos( $this->billing_location['latitude'] ) *
		                 sin( $long_diff / 2 ) * sin( $long_diff / 2 );
		$c             = 2 * asin( sqrt( $a ) );
		$distance      = $earths_radius * $c;

		return round( $distance );
	}

}