<?php

class Marketify_EDD_Recommended_Products extends Marketify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	public function setup_actions() {
		add_action( 'init', array( $this, 'remove_auto_output' ), 12 );

		if ( edd_get_option( 'edd_rp_display_single', false ) ) {
			add_action( 'marketify_single_download_after', array( $this, 'output' ) );
		}

		if ( edd_get_option( 'edd_rp_display_checkout', false ) ) {
			add_action( 'edd_before_purchase_form', array( $this, 'output' ) );
		}
	}

	public function remove_auto_output() {
		remove_filter( 'edd_after_download_content', 'edd_rp_display_single', 10, 1 );
		remove_filter( 'edd_after_checkout_cart', 'edd_rp_display_checkout' );
	}

	public function output() {
		if ( is_singular( 'download' ) ) {
			global $post;

			$suggestion_data = edd_rp_get_suggestions( $post->ID );
		} else {
			$cart_items = edd_get_cart_contents();

			if ( empty( $cart_items ) ) {
				return;
			}

			$post_ids        = wp_list_pluck( $cart_items, 'id' );
			$user_id         = is_user_logged_in() ? get_current_user_id() : false;
			$suggestion_data = edd_rp_get_multi_suggestions( $post_ids, $user_id );
		}

		if ( ! is_array( $suggestion_data ) || empty( $suggestion_data ) ) {
			return;
		}

		$suggestions = array_keys( $suggestion_data );
		$suggestions = array_splice( $suggestions, edd_get_option( 'edd_rp_suggestions_count' ) );
		$suggestions = implode( ',', $suggestions );

		add_filter( 'edd_add_schema_microdata', '__return_false' );
	?>

		<div class="edd-recommended-products">
			<h3 class="section-title recommended-products"><span><?php _e( 'Recommended Products', 'marketify' ); ?></span></h3>

			<?php echo do_shortcode( "[downloads ids={$suggestions}]" ); ?>
		</div>
	<?php
		remove_filter( 'edd_add_schema_microdata', '__return_false' );
	}

}
