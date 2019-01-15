<?php

class Marketify_EDD_Featured_Downloads extends Marketify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	public function setup_actions() {
		add_filter( 'edd_fd_shortcode', array( $this, 'shortcode' ) );
	}

	public function shortcode( $output ) {
		$output = str_replace( '<div style="clear:both;"></div>', '', $output );

		return $output;
	}
}
