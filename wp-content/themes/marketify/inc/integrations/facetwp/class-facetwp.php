<?php

class Marketify_FacetWP extends Marketify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	public function setup_actions() {
		add_filter( 'downloads_shortcode', array( $this, 'facetwp_template' ), 20, 2 );
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		add_filter( 'facetwp_facets', array( $this, 'register_facets' ) );

		add_filter( 'facetwp_sort_options', array( $this, 'sort_options' ), 10, 2 );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'marketify-facetwp', $this->get_url() . 'js/facetwp.js', array( 'marketify' ), marketify_version() );
	}

	public function widgets_init() {
		unregister_widget( 'Marketify_Widget_Download_Archive_Sorting' );
	}

	public function facetwp_template( $output, $atts ) {
		if ( ! isset( $atts['salvattore'] ) || 'no' != $atts['salvattore'] ) {
			$output = str_replace( 'class="edd_downloads_list', 'class="edd_downloads_list facetwp-template', $output );
			$output .= do_shortcode( '[facetwp pager="true"]' );
		}

		return $output;
	}

	public function register_facets( $facets ) {
		$facets[] = array(
			'label' => 'Keywords',
			'name' => 'keywords',
			'type' => 'search',
			'search_engine' => '',
			'placeholder' => 'Keywords',
		);

		$facets[] = array(
			'label' => 'Categories',
			'name' => 'categories',
			'type' => 'checkboxes',
			'source' => 'tax/download_category',
		);

		$facets[] = array(
			'label' => 'Tags',
			'name' => 'tags',
			'type' => 'checkboxes',
			'source' => 'tax/download_tags',
		);

		return $facets;
	}

	public function sort_options( $options ) {
		unset( $options['distance'] );

		$options['sales_desc'] = array(
			'label' => __( 'Sales (Most)', 'marketify' ),
			'query_args' => array(
				'meta_key' => '_edd_download_sales',
				'orderby'  => 'meta_value_num',
				'order'  => 'DESC',
			),
		);

		$options['sales_asc'] = array(
			'label' => __( 'Sales (Fewest)', 'marketify' ),
			'query_args' => array(
				'meta_key' => '_edd_download_sales',
				'orderby'  => 'meta_value_num',
				'order'  => 'ASC',
			),
		);

		$options['price_desc'] = array(
			'label' => __( 'Price (Highest)', 'marketify' ),
			'query_args' => array(
				'meta_key' => 'edd_price',
				'orderby'  => 'meta_value_num',
				'order'  => 'DESC',
			),
		);

		$options['price_asc'] = array(
			'label' => __( 'Price (Lowest)', 'marketify' ),
			'query_args' => array(
				'meta_key' => 'edd_price',
				'orderby'  => 'meta_value_num',
				'order'  => 'ASC',
			),
		);

		return $options;
	}

}
