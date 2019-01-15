<?php

class Marketify_Page_Settings {

	public function __construct() {
		add_action( 'init', array( $this, 'register_meta' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );
	}

	public function register_meta() {
		do_action( 'marketify_register_page_meta' );
	}

	public function save_post( $post_id ) {
		global $post;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! is_object( $post ) ) {
			return;
		}

		if ( 'page' != $post->post_type ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			return;
		}

		do_action( 'marketify_save_page_meta', $post );
	}

}
