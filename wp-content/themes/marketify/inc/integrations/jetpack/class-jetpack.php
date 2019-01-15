<?php

class Marketify_Jetpack extends Marketify_Integration {

	public function __construct() {
		$this->includes = array(
			'class-jetpack-share.php',
			'widgets/class-widget-download-share.php',
		);

		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {
		$this->sharing = new Marketify_Jetpack_Share();
	}

	public function setup_actions() {
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
	}

	public function register_widgets() {
		register_widget( 'Marketify_Widget_Download_Share' );
	}

}
