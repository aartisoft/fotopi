<?php
/**
 * Downloads: Columns
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'downloads-archives-columns', array(
	'default' => 3,
	'sanitize_callback' => 'absint',
) );

$wp_customize->add_control( 'downloads-archives-columns', array(
	'label' => __( 'Number of Columns', 'marketify' ),
	'type' => 'number',
	'description' => __( 'Can be overwritten by passing <code>columns</code> to the <code>[downloads]</code> shortcode. Max 4', 'marketify' ),
	'section' => 'download-archives',
	'priority' => 20,
) );
