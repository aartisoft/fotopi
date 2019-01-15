<?php
/**
 * Downloads: Per Page
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'downloads-archives-per-page', array(
	'default' => 12,
	'sanitize_callback' => 'absint',
) );

$wp_customize->add_control( 'downloads-archives-per-page', array(
	'label' => sprintf( __( '%s Per Page', 'marketify' ), edd_get_label_plural() ),
	'type' => 'number',
	'description' => __( 'Can be overwritten by passing <code>number</code> to the <code>[downloads]</code> shortcode', 'marketify' ),
	'section' => 'download-archives',
	'priority' => 10,
) );
