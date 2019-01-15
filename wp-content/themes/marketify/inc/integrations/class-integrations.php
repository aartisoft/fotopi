<?php

class Marketify_Integrations {

	private $supported_integrations;
	public $integrations;

	public function __construct() {
		$this->supported_integrations = array(
			'bbpress' => array(
				class_exists( 'bbPress' ),
				'Marketify_bbPress',
			),
			'edd' => array(
				class_exists( 'Easy_Digital_Downloads' ),
				'Marketify_EDD',
			),
			'edd-fes' => array(
				class_exists( 'EDD_Front_End_Submissions' ),
				'Marketify_EDD_FES',
			),
			'edd-product-reviews' => array(
				class_exists( 'EDD_Reviews' ),
				'Marketify_EDD_Product_Reviews',
			),
			'edd-featured-downloads' => array(
				function_exists( 'edd_fd_textdomain' ),
				'Marketify_EDD_Featured_Downloads',
			),
			'edd-recommended-products' => array(
				function_exists( 'edd_rp_get_suggestions' ),
				'Marketify_EDD_Recommended_Products',
			),
			'edd-cross-sell-upsell' => array(
				defined( 'EDD_CSAU_VERSION' ),
				'Marketify_EDD_Cross_Sell_UpSell',
			),
			'edd-wish-lists' => array(
				class_exists( 'EDD_Wish_Lists' ),
				'Marketify_EDD_Wish_Lists',
			),
			'edd-bookings' => array(
				defined( 'EDD_BK_VERSION' ),
				'Marketify_EDD_Bookings',
			),
			'facetwp' => array(
				class_exists( 'FacetWP' ),
				'Marketify_FacetWP',
			),
			'jetpack' => array(
				class_exists( 'Jetpack' ),
				'Marketify_Jetpack',
			),
			'love-it' => array(
				defined( 'LI_BASE_DIR' ),
				'Marketify_Love_It',
			),
			'multiple-post-thumbnails' => array(
				class_exists( 'MultiPostThumbnails' ),
				'Marketify_Multiple_Post_Thumbnails',
			),
			'woothemes-features' => array(
				class_exists( 'WooThemes_Features' ),
				'Marketify_WooThemes_Features',
			),
			'woothemes-testimonials' => array(
				class_exists( 'WooThemes_Testimonials' ),
				'Marketify_WooThemes_Testimonials',
			),
			'soliloquy' => array(
				function_exists( 'soliloquy' ),
				'Marketify_Soliloquy',
			),
			'tgmpa' => array(
				true,
				'Marketify_TGMPA',
			),
			'sidekick' => array(
				false, // defined( 'SK_API' ),
				'Marketify_Sidekick',
			),
		);

		$this->load_integrations();
	}

	public function has( $key ) {
		return isset( $this->integrations[ $key ] );
	}

	public function get( $key ) {
		if ( ! $this->has( $key ) ) {
			return false;
		}

		return $this->integrations[ $key ];
	}

	public function add( $key, $class ) {
		$this->integrations[ $key ] = $class;
	}

	private function load_integrations() {
		foreach ( $this->supported_integrations as $key => $integration ) {
			if ( $integration[0] ) {
				require_once( trailingslashit( dirname( __FILE__ ) ) . trailingslashit( $key ) . 'class-' . $key . '.php' );

				$class = new $integration[1];

				$this->add( $key, $class );
			}
		}
	}

}
