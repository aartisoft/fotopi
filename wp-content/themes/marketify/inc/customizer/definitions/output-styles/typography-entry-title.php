<?php
/**
 * Output Widget Title  typography.
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$family = astoundify_themecustomizer_get_typography_mod( 'typography-entry-title-font-family' );
$weight = astoundify_themecustomizer_get_typography_mod( 'typography-entry-title-font-weight' );
$size   = astoundify_themecustomizer_get_typography_mod( 'typography-entry-title-font-size' );
$line   = astoundify_themecustomizer_get_typography_mod( 'typography-entry-title-line-height' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.entry-title--hentry'
	),
	'declarations' => array(
		'font-family' => astoundify_themecustomizer_get_font_stack( $family, 'googlefonts' ),
		'font-weight' => $weight,
		'line-height' => $line,
	),
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.entry-title--hentry'
	),
	'declarations' => array(
		'font-size' => $size . 'px',
	),
	'media' => 'screen and (min-width: 1200px)',
) );
