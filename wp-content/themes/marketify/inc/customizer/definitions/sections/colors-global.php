<?php
/**
 * Global Colors
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_section( 'colors-global', array(
	'title' => _x( 'Global', 'customizer section title (colors)', 'marketify' ),
	'panel' => 'colors',
	'priority' => 10,
) );
