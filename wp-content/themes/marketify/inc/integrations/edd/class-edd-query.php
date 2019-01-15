<?php

class Marketify_EDD_Query {

	public function __construct() {
		add_filter( 'edd_downloads_query', array( $this, 'shortcode_query' ), 10, 2 );
	}

	public function shortcode_query( $query, $atts ) {
		global $wp_query;

		if ( is_tax( array( 'download_category', 'download_tag' ) ) ) {
			$query['tax_query'] = $wp_query->tax_query->queries;
		}

		if ( isset( $_GET['s'] ) && 'download' == isset( $_GET['post_type'] ) ) {
			$query['s'] = esc_attr( $_GET['s'] );
		}

		return $query;
	}

}
