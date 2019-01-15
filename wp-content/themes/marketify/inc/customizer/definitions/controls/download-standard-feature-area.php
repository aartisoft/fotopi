<?php
/**
 * Downloads: Standard Feature Area
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'download-standard-feature-area', array(
	'default' => 'top',
	'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'download-standard-feature-area', array(
	'label' => __( 'Image Gallery Location', 'marketify' ),
	'type' => 'select',
	'choices' => array(
		'top' => __( 'Page Header', 'marketify' ),
		'inline' => __( 'Page Content', 'marketify' ),
	),
	'section' => 'download-standard',
	'priority' => 10,
) );
