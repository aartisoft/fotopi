<?php

class Marketify_EDD_FES_Vendors {

	public function __construct() {
		add_filter( 'fes_vendor_dashboard_menu', array( $this, 'dashboard_menu_icons' ) );
		add_filter( 'marketify_page_header_image', array( $this, 'profile_cover_image' ), 1 );

		add_action( 'marketify_download_entry_meta', array( $this, 'byline' ) );

		add_action( 'save_post', array( $this, 'clear_download_count_cache' ), 10, 2 );
	}

	public function clear_download_count_cache( $post_id, $post ) {
		if ( 'download' != $post->post_type ) {
			return;
		}

		delete_transient( $post->post_author . 'download' );
	}

	public function byline() {
		global $post;

		$vendor = marketify()->get( 'edd-fes' )->vendor( $post->post_author );

		printf(
			__( '<span class="byline"> by %1$s</span>', 'marketify' ),
			sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s %4$s</a></span>',
				$vendor->url(),
				esc_attr( sprintf( __( 'View all %1$s by %2$s', 'marketify' ), edd_get_label_plural(), get_the_author_meta( 'display_name', $post->post_author ) ) ),
				esc_html( get_the_author_meta( 'display_name', $post->post_author ) ),
				get_avatar( get_the_author_meta( 'ID', $post->post_author ), 50, apply_filters( 'marketify_default_avatar', null ) )
			)
		);
	}

	public function dashboard_menu_icons( $menu ) {
		$menu['home']['icon'] = 'house';

		if ( EDD_FES()->vendors->vendor_can_view_orders() ) {
			$menu['orders']['icon'] = 'ticket';
		}

		if ( EDD_FES()->integrations->is_commissions_active() ) {
			$menu['earnings']['icon'] = 'graph';
		}

		$menu['logout']['icon'] = 'logout';

		return $menu;
	}

	public function profile_cover_image( $background ) {
		global $wp_query;

		if ( ! is_page_template( 'page-templates/vendor.php' ) ) {
			return $background;
		}

		$vendor = fes_get_vendor();

		if ( ! $vendor ) {
			return $background;
		}

		$image = get_user_meta( $vendor->data->ID, 'cover_image', true );

		if ( ! $image ) {
			return $background;
		}

		$image = wp_get_attachment_image_src( $image[0], 'fullsize' );

		if ( is_array( $image ) ) {
			return $image[0];
		}

		return $background;
	}

}
