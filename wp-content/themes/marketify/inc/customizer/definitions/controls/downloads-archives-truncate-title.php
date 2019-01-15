<?php
/**
 * Downloads: Truncate Title
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_setting( 'downloads-archives-truncate-title', array(
	'default' => 'on',
	'sanitize_callback' => 'esc_attr',
) );

$wp_customize->add_control( 'downloads-archives-truncate-title', array(
	'label' => __( 'Truncate item titles', 'marketify' ),
	'type' => 'checkbox',
	'section' => 'download-archives',
	'priority' => 50,
) );
