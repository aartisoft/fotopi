<?php
/**
 * Downloads: Excerpt
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'downloads-archives-excerpt', array(
	'default' => '',
	'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'downloads-archives-excerpt', array(
	'label' => __( 'Display excerpt below title', 'marketify' ),
	'type' => 'checkbox',
	'section' => 'download-archives',
	'priority' => 40,
) );
