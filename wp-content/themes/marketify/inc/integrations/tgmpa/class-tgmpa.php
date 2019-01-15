<?php

class Marketify_TGMPA {

	public function __construct() {
		require( dirname( __FILE__ ) . '/class-tgm-plugin-activation.php' );

		add_action( 'tgmpa_register', array( $this, 'register_plugins' ) );
	}

	public function register_plugins() {
		$plugins = array(
			array(
				'name'      => 'Easy Digital Downloads',
				'slug'      => 'easy-digital-downloads',
				'required'  => true,
			),
			array(
				'name'      => 'Easy Digital Downloads - Featured Downloads',
				'slug'      => 'edd-featured-downloads',
				'required'  => false,
			),
			array(
				'name'      => 'Jetpack',
				'slug'      => 'jetpack',
				'required'  => false,
			),
			array(
				'name'      => 'Features by WooThemes',
				'slug'      => 'features-by-woothemes',
				'required'  => false,
			),
			array(
				'name'      => 'Testimonials by WooThemes',
				'slug'      => 'testimonials-by-woothemes',
				'required'  => false,
			),
			array(
				'name'      => 'Multiple Post Thumbnails',
				'slug'      => 'multiple-post-thumbnails',
				'required'  => false,
			),
			array(
				'name'      => 'If Menu',
				'slug'      => 'if-menu',
				'required'  => false,
			),
		);

		$config = array(
			'id'          => 'tgmpa-marketify-' . get_option( 'marketify_version', '2.0.0' ),
			'has_notices' => false,
			'parent_slug' => Astoundify_Setup_Guide::get_page_id(),
			'is_automatic' => true,
		);

		tgmpa( $plugins, $config );
	}

}
