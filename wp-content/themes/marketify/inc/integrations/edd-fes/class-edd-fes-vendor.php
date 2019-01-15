<?php

class Marketify_EDD_FES_Vendor {

	public $obj;

	public function __construct( $author = false ) {
		if ( ! $author ) {
			$author = $this->find();
		} elseif ( is_numeric( $author ) ) {
			$author = new WP_User( $author );
		}

		$this->obj = $author;
		$this->ID = $this->obj->ID;
	}

	private function find() {
		$author = fes_get_vendor();

		if ( ! $author ) {
			$author = wp_get_current_user();
		}

		return $author;
	}

	public function url() {
		return esc_url( EDD_FES()->vendors->get_vendor_store_url( $this->obj->ID ) );
	}

	public function display_name() {
		$display_name = esc_attr( $this->obj->display_name );

		if ( '' == $display_name ) {
			$display_name = esc_attr( $this->obj->user_login );
		}

		return $display_name;
	}

	public function date_registered() {
		return date_i18n( get_option( 'date_format' ), strtotime( $this->obj->user_registered ) );
	}

	public function downloads_count( $user_id = false, $post_type = 'download' ) {
		if ( ! $user_id ) {
			$user_id = $this->ID;
		}

		if ( false === ( $count = get_transient( $user_id . $post_type ) ) ) {
			global $wpdb;

			$where = get_posts_by_author_sql( $post_type, true, $user_id );
			$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->posts $where" );

			set_transient( $user_id . $post_type, $count, 12 * HOUR_IN_SECONDS );
		}

		return apply_filters( 'get_usernumposts_' . $post_type, $count, $user_id );
	}

}
