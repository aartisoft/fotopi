<?php
/**
 * Downloads: Popular Items
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'downloads-archives-popular', array(
	'default' => 'on',
	'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'downloads-archives-popular', array(
	'label' => __( 'Display "Popular Items" above results', 'marketify' ),
	'type' => 'checkbox',
	'section' => 'download-archives',
	'priority' => 30,
) );
