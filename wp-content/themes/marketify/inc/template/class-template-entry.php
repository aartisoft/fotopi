<?php

class Marketify_Template_Entry {

	public function __construct() {
		apply_filters( 'marketify_entry_author_social', array( $this, 'author_social' ) );
		add_action( 'marketify_entry_meta', array( $this, 'byline' ) );
	}

	/**
	 * Add a blog byline to grid blog items.
	 *
	 * @since 2.4.0
	 */
	public function byline() {
		printf(
			__( '<span class="byline"> by %1$s</span>', 'marketify' ),
			sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s %4$s</a></span>',
				get_author_posts_url( get_the_author_meta( 'ID' ) ),
				esc_attr( sprintf( __( 'View all posts by %s', 'marketify' ), get_the_author_meta( 'display_name' ) ) ),
				esc_html( get_the_author_meta( 'display_name' ) ),
				get_avatar( get_the_author_meta( 'ID' ), 50, apply_filters( 'marketify_default_avatar', null ) )
			)
		);
	}

	/**
	 * Any custom social profiles that are added should take a full URL.
	 */
	public function social_profiles( $user_id = null ) {
		global $post;

		$methods = wp_get_user_contact_methods();
		$social  = array();

		if ( ! $user_id ) {
			$user_id = get_the_author_meta( 'ID', $post->post_author );
		}

		foreach ( $methods as $key => $method ) {
			$url = get_the_author_meta( $key, $user_id );

			if ( ! $url ) {
				continue;
			}

			if ( false === filter_var( $url, FILTER_VALIDATE_URL ) ) {
				$url = apply_filters( 'marketify_contact_method_' . $key . '_url', '' );
			}

			if ( '' != $url ) {
				$social[ $key ] = sprintf( '<a href="%1$s" target="_blank"><i class="ion-social-%2$s"></i></a>', esc_url( $url ), esc_attr( $key ) );
			}
		}

		$social = implode( ' ', $social );
		return $social;
	}

}
