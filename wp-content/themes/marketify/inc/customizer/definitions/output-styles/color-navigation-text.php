<?php
/**
 * Output "Navigation Text" CSS.
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$text = marketify_theme_color( 'color-navigation-text' );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.nav-menu--primary li a',
		'.nav-menu li.menu-item-has-children:after',
		'.nav-menu li.page_item_has_children:after',
	),
	'declarations' => array(
		'color' => esc_attr( $text ),
	),
	'media' => 'screen and (min-width: 992px)',
) );

astoundify_themecustomizer_add_css( array(
	'selectors' => array(
		'.nav-menu--primary-toggle',
		'.page-title',
	),
	'declarations' => array(
		'color' => esc_attr( $text ),
	),
) );
