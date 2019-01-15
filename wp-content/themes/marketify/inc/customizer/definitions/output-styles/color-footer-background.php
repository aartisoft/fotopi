<?php
/**
 * Output "Footer Background" CSS.
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$footer = marketify_theme_color( 'color-footer-dark-background' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.site-footer.site-footer--dark'
	),
	'declarations' => array(
		'background-color' => esc_attr( $footer ),
	),
) );
