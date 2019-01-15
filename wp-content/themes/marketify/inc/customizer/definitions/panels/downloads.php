<?php
/**
 * Downloads
 *
 * @since 3.5.0
 */

if ( ! marketify()->get( 'edd' ) ) {
	return;
}

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_panel( 'downloads', array(
	'title' => get_theme_mod( 'download-label-plural', 'Downloads' ),
	'priority' => 1,
) );
