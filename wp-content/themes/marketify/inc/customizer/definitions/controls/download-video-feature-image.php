<?php
/**
 * Downloads: Video Feature Image
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'downloads-video-feature-image', array(
	'default' => 'background',
	'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'downloads-video-feature-image', array(
	'label' => __( 'Featured Image Display', 'marketify' ),
	'type' => 'select',
	'choices' => array(
		'background' => __( 'Header Background', 'marketify' ),
		'none' => __( 'None', 'marketify' ),
	),
	'section' => 'download-video',
	'priority' => 20,
) );
