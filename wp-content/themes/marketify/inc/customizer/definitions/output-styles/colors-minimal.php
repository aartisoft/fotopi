<?php
/**
 * Output colors for the "Minmal" Page Template
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$page_header_background = marketify_theme_color( 'color-page-header-background' );
$accent = marketify_theme_color( 'color-accent' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.minimal',
		'.custom-background.minimal',
	),
	'declarations' => array(
		'background-color' => esc_attr( $page_header_background ),
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.minimal .section-title__inner',
		'.minimal .edd_form fieldset > span legend',
		'.minimal .edd_form fieldset legend span',
		'.minimal #edd_checkout_form_wrap .edd_form fieldset > span legend',
		'.minimal .entry-content .edd-slg-social-container > span legend',
		'.minimal .fes-headers span',
	),
	'declarations' => array(
		'background-color' => esc_attr( $page_header_background ),
		'color' => '#fff',
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		// edd
		'.minimal #edd_login_form input[type=submit]',
		'.minimal #edd_register_form input[type=submit]',
		'.minimal #edd-purchase-button.edd-submit.button',

		// fes
		'.minimal .fes-submit .edd-submit.button',
	),
	'declarations' => array(
		'background-color' => esc_attr( $accent ),
		'border-color' => esc_attr( $accent ),
		'color' => '#fff',
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		// edd
		'.minimal #edd_login_form input[type=submit]:hover',
		'.minimal #edd_register_form input[type=submit]:hover',
		'.minimal #edd-purchase-button.button.edd-submit:hover',

		// fes
		'.minimal .fes-submit .edd-submit.button:hover',
	),
	'declarations' => array(
		'background-color' => 'transparent',
		'border-color' => esc_attr( $accent ),
		'color' => esc_attr( $accent ),
	),
) );
