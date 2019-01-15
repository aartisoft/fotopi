<?php

class Marketify_Soliloquy extends Marketify_Integration {

	public function __construct() {
		$this->includes = array(
			'widgets/class-widget-slider-soliloquy.php'
		);

		parent::__construct( dirname( __FILE__ ) );
	}

	public function setup_actions() {
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}

	public function register_widgets() {
		register_widget( 'Marketify_Widget_Slider_Soliloquy' );
	}


}
