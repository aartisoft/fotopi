<?php

class Marketify_EDD_Template_Purchase_Form {

	public function __construct() {
		add_action( 'marketify_download_actions', array( $this, 'purchase_link' ) );
		add_action( 'marketify_download_content_actions_before', array( $this, 'purchase_link' ) );

		add_filter( 'edd_purchase_download_form', array( $this, 'download_form_class' ), 10, 2 );
		add_filter( 'edd_button_colors', array( $this, 'button_colors' ) );
	}

	public function purchase_link( $download_id = null ) {
		global $post, $edd_options;

		if ( ! $download_id ) {
			$download_id = $post->ID;
		}

		$variable = edd_has_variable_prices( $download_id );
		$form = edd_get_purchase_link( array(
			'download_id' => $download_id,
			'price' => false,
			'booking_options' => false,// @see https://easydigitaldownloads.com/downloads/edd-bookings/
		) );

		// ghetto check for sold out
		$label = edd_get_option( 'edd_purchase_limit_sold_out_label', 'Sold Out' );
		$sold_out = strpos( $form, $label );

		$in_cart = edd_item_in_cart( $download_id );

		if ( ( ! $variable || $sold_out != false || ( $in_cart ) ) && apply_filters( 'marketify_disable_buy_popup', true ) ) {
			echo $form;
		} else {

			$button_text = apply_filters( 'marketify_purchase_button_text', __( 'Purchase', 'marketify' ), $download_id );

			$button = ! empty( $edd_options['add_to_cart_text'] ) ? $edd_options['add_to_cart_text'] : $button_text;
			$class = 'button buy-now popup-trigger';

			printf( '<a href="#buy-now-%s" class="%s">%s</a>', $post->ID, $class, $button );

			// ouput the form here so things like wishlists can find something
			echo $form;
		}
	}

	public function download_form_class( $purchase_form, $args ) {
		$download_id = $args['download_id'];

		if ( ! $download_id || edd_has_variable_prices( $download_id ) ) {
			return $purchase_form;
		}

		$purchase_form = str_replace( 'class="edd_download_purchase_form"', 'class="edd_download_purchase_form download-variable"', $purchase_form );

		return $purchase_form;
	}

	public function button_colors( $colors ) {
		$unset = array( 'white', 'blue', 'gray', 'red', 'green', 'yellow', 'orange', 'dark-gray' );

		foreach ( $unset as $color ) {
			unset( $colors[ $color ] );
		}

		return $colors;
	}

}
