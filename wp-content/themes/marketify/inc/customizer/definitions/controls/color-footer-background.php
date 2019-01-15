<?php
/**
 * Color: Footer Background
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'color-footer-dark-background', array(
	'default' => astoundify_themecustomizer_get_colorscheme_mod_default( 'color-footer-dark-background' ),
	'transport' => 'postMessage',
	'sanitize_callback' => 'sanitize_hex_color',
) );

$wp_customize->add_control( new WP_Customize_Color_Control(
	$wp_customize,
	'color-footer-dark-background',
	array(
		'label'   => __( 'Footer Background Color', 'marketify' ),
		'section' => 'colors-footer',
		'priority' => 10,
	)
) );
