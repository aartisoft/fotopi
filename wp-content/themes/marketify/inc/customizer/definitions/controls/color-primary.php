<?php
/**
 * Color: Primary
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'color-primary', array(
	'default' => astoundify_themecustomizer_get_colorscheme_mod_default( 'color-primary' ),
	'transport' => 'postMessage',
	'sanitize_callback' => 'sanitize_hex_color',
) );

$wp_customize->add_control( new WP_Customize_Color_Control(
	$wp_customize,
	'color-primary',
	array(
		'label'   => __( 'Primary', 'marketify' ),
		'section' => 'colors-global',
		'priority' => 20,
	)
) );
