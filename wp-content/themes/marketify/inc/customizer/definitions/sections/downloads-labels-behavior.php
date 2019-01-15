<?php
/**
 * Downloads: Labels & Behavior
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_section( 'download-labels', array(
	'title' => _x( 'Labels &amp; Behavior', 'customizer section title (colors)', 'marketify' ),
	'panel' => 'downloads',
	'priority' => 10,
) );
