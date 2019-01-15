<?php

class Marketify_WooThemes_Features extends Marketify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}


	public function setup_actions() {
		add_filter( 'woothemes_features_default_args', array( $this, 'default_args' ) );
	}

	public function default_args( $args ) {
		$args['link_title'] = false;

		return $args;
	}

}
