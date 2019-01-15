<?php
/**
 * Footer: Appearance
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'footer-style', array(
	'default' => 'light',
	'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'footer-style', array(
	'label'   => __( 'Display Style', 'marketify' ),
	'type' => 'select',
	'choices' => array(
		'light' => __( 'Transparent', 'marketify' ),
		'dark' => __( 'Dark', 'marketify' ),
	),
	'section' => 'footer-appearance',
	'priority' => 10,
) );
