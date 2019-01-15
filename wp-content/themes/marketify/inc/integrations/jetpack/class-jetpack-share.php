<?php

class Marketify_Jetpack_Share {

	public function __construct() {
		add_action( 'marketify_widget_download_share', array( $this, 'output' ) );
	}

	public function output() {
		global $post;

		if ( ! function_exists( 'sharing_display' ) ) {
			return;
		}

		$buttons = sharing_display( '' );

		if ( '' == $buttons ) {
			return;
		}

		echo $buttons;
	}
}
