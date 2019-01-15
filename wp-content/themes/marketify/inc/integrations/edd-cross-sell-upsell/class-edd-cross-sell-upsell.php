<?php

class Marketify_EDD_Cross_Sell_UpSell extends Marketify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	public function setup_actions() {
		add_filter( 'edd_csau_show_excerpt', '__return_false' );
		add_filter( 'edd_csau_show_price', '__return_false' );
		add_filter( 'edd_csau_upsell_show_button', '__return_false' );
	}

}
