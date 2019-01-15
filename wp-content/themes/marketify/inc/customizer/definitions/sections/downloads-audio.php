<?php
/**
 * Downloads: Audio
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_section( 'download-audio', array(
	'title' => sprintf( _x( 'Audio %s', 'customizer section title (colors)', 'marketify' ), edd_get_label_singular() ),
	'panel' => 'downloads',
	'priority' => 40,
) );
