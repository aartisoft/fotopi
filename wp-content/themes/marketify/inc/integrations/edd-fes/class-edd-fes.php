<?php

class Marketify_EDD_FES extends Marketify_Integration {

	public function __construct() {
		$this->includes = array(
			'class-edd-fes-vendors.php',
			'class-edd-fes-vendor.php',
			'class-edd-fes-widgets.php',

			'widgets/class-widget-vendor.php',
			'widgets/class-widget-vendor-description.php',
			'widgets/class-widget-vendor-contact.php',
			'widgets/class-widget-product-details.php',
		);

		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {
		$this->vendors = new Marketify_EDD_FES_Vendors();
		$this->widgets = new Marketify_EDD_FES_Widgets();
	}

	public function setup_actions() {
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_styles' ), 20 );
		add_filter( 'fes_render_recaptcha_field_frontend_size', array( $this, 'fes_render_recaptcha_field_frontend_size' ) );
	}

	public function vendor( $author = false ) {
		return new Marketify_EDD_FES_Vendor( $author );
	}

	public function fes_render_recaptcha_field_frontend_size( $size ) {
		if ( is_page_template( 'page-templates/vendor.php' ) ) {
			return 'compact';
		}

		return $size;
	}

	public function dequeue_styles() {
		wp_dequeue_style( 'fes-css' );
	}

}
