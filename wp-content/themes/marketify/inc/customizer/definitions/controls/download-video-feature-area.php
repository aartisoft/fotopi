<?php
/**
 * Downloads: Video Feature Area
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'download-video-feature-area', array(
	'default' => 'top',
	'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'download-video-feature-area', array(
	'label' => __( 'Image Gallery Location', 'marketify' ),
	'type' => 'select',
	'choices' => array(
		'top' => __( 'Page Header', 'marketify' ),
		'inline' => __( 'Page Content', 'marketify' ),
	),
	'section' => 'download-video',
	'priority' => 10,
) );
