<?php
/**
 * Download: Generate Permalinks
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'download-label-generate', array(
	'default' => 'on',
) );

$wp_customize->add_control( 'download-label-generate', array(
	'label' => __( 'Generate Permalinks', 'marketify' ),
	'type' => 'checkbox',
	'description' => sprintf( __( 'Use these labels to create updated permalinks. Visit <a href="%s">Settings ▸ Permalinks</a> once saved.', 'marketify' ), admin_url( 'options-permalink.php' ) ),
	'section' => 'download-labels',
	'priority' => 30,
) );
