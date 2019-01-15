<?php
/**
 * Output "Page Header Background" CSS.
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$page_header_background = marketify_theme_color( 'color-page-header-background' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.header-outer',
		'.minimal',
		'.custom-background.minimal',
		'.wp-playlist .mejs-controls .mejs-time-rail .mejs-time-current',
	),
	'declarations' => array(
		'background-color' => esc_attr( $page_header_background ),
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.page-header .button:hover',
		'.page-header .button.button--color-white:hover',
		'.home .page-header .button:hover', // backwards compat
		'.page-header .edd-submit.button.edd_go_to_checkout:hover', // when an item is in the cart
		'.site-footer--light .site-title--footer a',
	),
	'declarations' => array(
		'color' => esc_attr( $page_header_background ),
	),
) );
