<?php
/**
 * Output "Accent Color" CSS.
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$accent = marketify_theme_color( 'color-accent' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.widget--home-taxonomy-stylized',
	),
	'declarations' => array(
		'background-color' => esc_attr( $accent ),
	),
) );
