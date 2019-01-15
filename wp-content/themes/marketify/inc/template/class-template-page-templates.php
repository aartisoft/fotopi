<?php

class Marketify_Template_Page_Templates {

	public function __construct() {
		add_filter( 'theme_page_templates', array( $this, 'fes' ) );
		add_filter( 'theme_page_templates', array( $this, 'love_it' ) );
		add_filter( 'theme_page_templates', array( $this, 'facetwp' ) );

		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	public function fes( $page_templates ) {
		if ( marketify()->get( 'edd-fes' ) ) {
			return $page_templates;
		}

		unset( $page_templates['page-templates/vendor.php'] );

		return $page_templates;
	}

	public function love_it( $page_templates ) {
		if ( marketify()->get( 'love-it' ) ) {
			return $page_templates;
		}

		unset( $page_templates['page-templates/wishlist.php'] );

		return $page_templates;
	}

	public function facetwp( $page_templates ) {
		if ( ! marketify()->get( 'facetwp' ) ) {
			return $page_templates;
		}

		unset( $page_templates['page-templates/popular.php'] );

		return $page_templates;
	}

	public function body_class( $classes ) {
		if ( is_page_template( 'page-templates/home.php' ) ) {
			$classes[] = 'home-1';
		}

		if ( is_page_template( 'page-templates/home-search.php' ) ) {
			$classes[] = 'home-search';
		}

		if ( is_page_template( 'page-templates/minimal.php' ) ) {
			$classes[] = 'minimal';
		}

		return $classes;
	}

	public function find_page( $template_name ) {
		global $wpdb;

		$page = $wpdb->get_col( $wpdb->prepare(
			"
            SELECT      meta.post_id
            FROM        $wpdb->postmeta meta
            WHERE       meta.meta_key = '_wp_page_template'
                        AND meta.meta_value = %s
            " , $template_name ) );

		return $page;
	}

}
