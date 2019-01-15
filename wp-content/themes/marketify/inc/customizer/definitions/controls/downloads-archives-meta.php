<?php
/**
 * Downloads: Meta
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'downloads-archives-meta', array(
	'default' => 'auto',
	'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'downloads-archives-meta', array(
	'label' => __( 'Display Titles & Meta', 'marketify' ),
	'type' => 'radio',
	'description' => __( '<strong>Always</strong> will display on featured and popular sliders.', 'marketify' ),
	'choices' => array(
		'auto' => __( 'Auto', 'marketify' ),
		'always' => __( 'Always', 'marketify' ),
		'never' => __( 'Never', 'marketify' ),
	),
	'section' => 'download-archives',
	'priority' => 60,
) );
