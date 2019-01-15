<?php

class Marketify_EDD_Shortcode {

	public function __construct() {
		add_action( 'marketify_downloads', array( $this, 'output' ) );

		add_filter( 'shortcode_atts_downloads', array( $this, 'shortcode_atts' ), 10, 3 );
		add_filter( 'edd_download_class', array( $this, 'grid_item_download_class' ), 10, 3 );

		add_filter( 'edd_downloads_list_wrapper_class', array( $this, 'grid_wrapper_class' ), 10, 2 );
		add_filter( 'downloads_shortcode', array( $this, 'grid_wrapper_columns' ), 10, 2 );
		add_filter( 'excerpt_length', array( $this, 'grid_excerpt_length' ) );

		add_filter( 'edd_download_pagination_args', array( $this, 'pagination_args' ), 10, 4 );
	}

	/**
	 * Output the [downloads] shortcode. If it is on a singular page
	 * try to use that page's content so custom parameters can be set.
	 *
	 * @since 2.6.0
	 *
	 * @return mixed
	 */
	public function output() {
		// if the current page has its own shortcode use that instead
		if ( is_singular( 'page' ) && has_shortcode( get_the_content(), 'downloads' ) ) {
			the_post();
			the_content();
			rewind_posts();
		} else {
			echo do_shortcode( '[downloads]' );
		}
	}

	public function shortcode_atts( $out, $pairs, $atts ) {
		$out['excerpt']      = 'no';
		$out['full_content'] = 'no';
		$out['price']        = 'no';
		$out['buy_button']   = 'no';

		if ( ! isset( $atts['columns'] ) ) {
			$out['columns'] = get_theme_mod( 'downloads-archives-columns', 3 );
		}

    if ( ! isset( $atts['number'] ) ) {
      $out['number'] = get_theme_mod( 'downloads-archives-per-page', 12 );
    }

		if ( isset( $atts['flat'] ) && $atts['flat'] == true ) {
			$out['salvattore'] = 'no';
		}

		return $out;
	}

	public function grid_item_download_class( $class, $id, $atts ) {
		$classes = array();
		$classes[] = $class;
		$classes[] = 'content-grid-download';
		$classes[] = implode( ' ', get_post_class( $id ) );

		return implode( ' ', $classes );
	}

	public function grid_wrapper_class( $class, $atts ) {
		$classes = array( 'row', 'download-grid-wrapper' );

		if ( isset( $atts['salvattore'] ) && 'no' == $atts['salvattore'] ) {
			$classes[] = 'has-slick';
		}

		return implode( ' ', $classes ) . ' ' . $class;
	}

	public function grid_wrapper_columns( $output, $atts ) {
		$output = str_replace( '<div style="clear:both;"></div>', '', $output );

		return $output;
	}

	public function grid_excerpt_length( $length ) {
		if ( 'download' == get_post_type() && ! is_singular( 'download' ) ) {
			return 15;
		}

		return $length;
	}

	public function pagination_args( $args, $atts, $downloads, $query ) {
		$args['prev_text'] = __( 'Previous', 'marketify' );
		$args['next_text'] = __( 'Next', 'marketify' );

		return $args;
	}
}
