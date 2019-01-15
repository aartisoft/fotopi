<?php
/**
 * Downloads: Audio Feature Image
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'downloads-audio-feature-image', array(
	'default' => 'background',
	'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'downloads-audio-feature-image', array(
	'label' => __( 'Featured Image Display', 'marketify' ),
	'type' => 'select',
	'choices' => array(
		'background' => __( 'Header Background', 'marketify' ),
		'none' => __( 'None', 'marketify' ),
	),
	'section' => 'download-audio',
	'priority' => 20,
) );
