<?php
/**
 * Output Widget Title  typography.
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$family = astoundify_themecustomizer_get_typography_mod( 'typography-widget-title-font-family' );
$weight = astoundify_themecustomizer_get_typography_mod( 'typography-widget-title-font-weight' );
$size   = astoundify_themecustomizer_get_typography_mod( 'typography-widget-title-font-size' );
$line   = astoundify_themecustomizer_get_typography_mod( 'typography-widget-title-line-height' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'#edd-wl-modal .modal-header h2',
		'#edd-wl-modal .modal-header span',
		'#edd_checkout_form_wrap fieldset#edd_cc_fields>legend span',
		'#edd_checkout_form_wrap fieldset#edd_cc_fields>span legend',
		'#edd_checkout_form_wrap fieldset#edd_cc_fields>span span',
		'#edd_checkout_form_wrap fieldset>legend span',
		'#edd_checkout_form_wrap fieldset>span legend',
		'#edd_checkout_form_wrap fieldset>span span',
		'#edd_checkout_wrap fieldset#edd_cc_fields>legend span',
		'#edd_checkout_wrap fieldset#edd_cc_fields>span legend',
		'#edd_checkout_wrap fieldset#edd_cc_fields>span span',
		'#edd_checkout_wrap fieldset>legend span',
		'#edd_checkout_wrap fieldset>span legend',
		'#edd_checkout_wrap fieldset>span span',
		'.edd-csau-products h2 span',
		'.edd-reviews-heading span',
		'.edd-reviews-title span',
		'.edd-reviews-vendor-feedback-item h3 a',
		'.edd-reviews-vendor-feedback-item h3 span',
		'.edd-reviews-vendor-feedback-item h4 a',
		'.edd-reviews-vendor-feedback-item h4 span',
		'.edd_form fieldset>legend span',
		'.edd_form fieldset>span legend',
		'.edd_form fieldset>span span',
		'.entry-content .edd-csau-products h2 span',
		'.entry-content .edd-slg-social-container>span legend',
		'.entry-content .edd-slg-social-container>span span',
		'.entry-content .fes-headers span',
		'.entry-content .pm-section-title span',
		'.gform_title span',
		'.section-title span',
		'.section-title__inner',
		'.widget-title--blog span',
	),
	'declarations' => array(
		'font-family' => astoundify_themecustomizer_get_font_stack( $family, 'googlefonts' ),
		'font-weight' => $weight,
		'line-height' => $line,
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'#edd-wl-modal .modal-header h2',
		'#edd-wl-modal .modal-header span',
		'#edd_checkout_form_wrap fieldset#edd_cc_fields>legend span',
		'#edd_checkout_form_wrap fieldset#edd_cc_fields>span legend',
		'#edd_checkout_form_wrap fieldset#edd_cc_fields>span span',
		'#edd_checkout_form_wrap fieldset>legend span',
		'#edd_checkout_form_wrap fieldset>span legend',
		'#edd_checkout_form_wrap fieldset>span span',
		'#edd_checkout_wrap fieldset#edd_cc_fields>legend span',
		'#edd_checkout_wrap fieldset#edd_cc_fields>span legend',
		'#edd_checkout_wrap fieldset#edd_cc_fields>span span',
		'#edd_checkout_wrap fieldset>legend span',
		'#edd_checkout_wrap fieldset>span legend',
		'#edd_checkout_wrap fieldset>span span',
		'.edd-csau-products h2 span',
		'.edd-reviews-heading span',
		'.edd-reviews-title span',
		'.edd-reviews-vendor-feedback-item h3 a',
		'.edd-reviews-vendor-feedback-item h3 span',
		'.edd-reviews-vendor-feedback-item h4 a',
		'.edd-reviews-vendor-feedback-item h4 span',
		'.edd_form fieldset>legend span',
		'.edd_form fieldset>span legend',
		'.edd_form fieldset>span span',
		'.entry-content .edd-csau-products h2 span',
		'.entry-content .edd-slg-social-container>span legend',
		'.entry-content .edd-slg-social-container>span span',
		'.entry-content .fes-headers span',
		'.entry-content .pm-section-title span',
		'.gform_title span',
		'.section-title span',
		'.section-title__inner',
		'.widget-title--blog span',
	),
	'declarations' => array(
		'font-size' => $size . 'px',
	),
	'media' => 'screen and (min-width: 1200px)',
) );
