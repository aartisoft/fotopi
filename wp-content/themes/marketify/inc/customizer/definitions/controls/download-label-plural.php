<?php
/**
 * Download: Plural Label
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'download-label-plural', array(
	'default' => 'Downloads',
	'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'download-label-plural', array(
	'label'   => __( 'Plural Label', 'marketify' ),
	'section' => 'download-labels',
	'priority' => 20,
) );
