<?php

class Marketify_EDD_Sorting {

	public function __construct() {
		add_filter( 'query_vars', array( $this, 'query_vars' ) );
		add_filter( 'shortcode_atts_downloads', array( $this, 'shortcode_atts' ), 10, 3 );
		add_filter( 'edd_downloads_query', array( $this, 'edd_downloads_query' ), 10, 2 );
	}

	public function query_vars( $vars ) {
		$vars[] = 'm-orderby';
		$vars[] = 'm-order';

		return $vars;
	}

	public function options() {
		$options = apply_filters( 'marketify_sorting_options', array(
			'date'  => __( 'Date', 'marketify' ),
			'title' => __( 'Title', 'marketify' ),
			'price' => __( 'Price', 'marketify' ),
			'sales' => __( 'Sales', 'marketify' ),
		) );

		return $options;
	}

	function shortcode_atts( $out, $pairs, $atts ) {
		$safe = array_keys( $this->options() );

		$orderby = get_query_var( 'm-orderby' );
		$order   = get_query_var( 'm-order' );

		if ( ! ( $orderby || $order ) ) {
			return $out;
		}

		if ( ! in_array( $orderby, $safe ) ) {
			return $out;
		}

		$out['orderby'] = $orderby;
		$out['order'] = $order;

		return $out;
	}

	public function edd_downloads_query( $query, $atts ) {
		if ( 'sales' == $atts['orderby'] ) {
			$query['meta_key'] = '_edd_download_sales';
			$query['orderby']  = 'meta_value_num';
		} elseif ( 'featured' == $atts['orderby'] ) {
			$query['meta_key'] = 'edd_feature_download';
		}

		return $query;
	}

}
