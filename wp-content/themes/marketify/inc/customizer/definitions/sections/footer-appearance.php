<?php
/**
 * Footer: Appearance
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_section( 'footer-appearance', array(
	'title' => _x( 'Appearance', 'customizer section title (colors)', 'marketify' ),
	'panel' => 'footer',
	'priority' => 5,
) );
