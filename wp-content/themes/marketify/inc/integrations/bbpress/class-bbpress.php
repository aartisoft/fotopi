<?php

class Marketify_bbPress extends Marketify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	public function setup_actions() {
		add_filter( 'bbp_before_get_breadcrumb_parse_args', array( $this, 'breadcrumb_args' ) );
	}

	public function breadcrumb_args( $args ) {
		$args['home_text'] = __( 'Home', 'marketify' );

		return $args;
	}

}
