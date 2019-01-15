<?php

class Marketify_Love_It extends Marketify_Integration {

	public function __construct() {
		$this->includes = array(
			'class-love-it-archives.php'
		);

		parent::__construct( dirname( __FILE__ ) );
	}

	public function init() {
		$this->archives = new Marketify_Love_It_Archives();
	}

	public function setup_actions() {
		add_filter( 'li_display_love_links_on', array( $this, 'restrict_output' ) );

		add_action( 'marketify_download_grid_previewer_before', array( $this, 'output' ) );
		add_action( 'marketify_download_content_image_overlay_before', array( $this, 'output' ) );
		add_action( 'marketify_download_previewer_before', array( $this, 'output' ) );
	}

	public function restrict_output( $types ) {
		return array( '__marketify__' );
	}

	public function output() {
		global $post;

		if ( ! is_object( $post ) ) {
			return;
		}

		if ( class_exists( 'Love_It_Pro' ) ) {
			echo lip_love_it_link( $post->ID, '', '' );
		} else {
			echo li_love_link();
		}
	}

}
