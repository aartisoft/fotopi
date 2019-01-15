<?php
/**
 * Colors
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->add_panel( 'colors', array(
	'title' => _x( 'Colors', 'customizer panel title', 'marketify' ),
	'priority' => 20,
) );
