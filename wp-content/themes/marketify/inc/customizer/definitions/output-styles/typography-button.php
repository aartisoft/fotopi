<?php
/**
 * Output button typography.
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$family = astoundify_themecustomizer_get_typography_mod( 'typography-button-font-family' );
$weight = astoundify_themecustomizer_get_typography_mod( 'typography-button-font-weight' );
$size   = astoundify_themecustomizer_get_typography_mod( 'typography-button-font-size' );
$line   = astoundify_themecustomizer_get_typography_mod( 'typography-button-line-height' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'button',
		'input[type=submit]',
		'input[type=button]',
		'input[type=reset]',

		'#edd-purchase-button',
		'#edd_checkout_form_wrap .edd-cart-adjustment .edd-apply-discount',
		'.button',
		'.button--private-message-link',
		'.cart_item.edd_checkout a',
		'.edd-submit',
		'.edd-submit.button',
		'.edd-submit.button:visited',
		'.edd-wl-button',
		'.edd-wl-button.edd-wl-action',
		'.edd_terms_links',
		'.entry-content #fes-view-comment a',
		'.facetwp-type-slider .facetwp-slider-reset',
		'a.edd-wl-button',
		'a.edd-wl-button.edd-wl-action',
		'body .marketify_widget_slider_hero .soliloquy-container .soliloquy-caption-outer .button',
		'.edd-submit',
	),
	'declarations' => array(
		'font-family' => astoundify_themecustomizer_get_font_stack( $family, 'googlefonts' ),
		'font-weight' => $weight,
		'line-height' => $line,
		'text-transform' => 'uppercase',
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'button',
		'input[type=submit]',
		'input[type=button]',
		'input[type=reset]',

		'#edd-purchase-button',
		'#edd_checkout_form_wrap .edd-cart-adjustment .edd-apply-discount',
		'.button',
		'.button--private-message-link',
		'.cart_item.edd_checkout a',
		'.edd-submit',
		'.edd-submit.button',
		'.edd-submit.button:visited',
		'.edd-wl-button',
		'.edd-wl-button.edd-wl-action',
		'.edd_terms_links',
		'.entry-content #fes-view-comment a',
		'.facetwp-type-slider .facetwp-slider-reset',
		'a.edd-wl-button',
		'a.edd-wl-button.edd-wl-action',
		'body .marketify_widget_slider_hero .soliloquy-container .soliloquy-caption-outer .button',
		'.edd-submit',
	),
	'declarations' => array(
		'font-size' => $size . 'px',
	),
	'media' => 'screen and (min-width: 1200px)',
) );
