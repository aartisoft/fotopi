<?php
/**
 * Header Text Color
 *
 * @since 2.11.0
 */

if ( ! defined( 'ABSPATH' ) || ! $wp_customize instanceof WP_Customize_Manager ) {
	exit; // Exit if accessed directly.
}

$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

$wp_customize->get_control( 'header_textcolor' )->label = __( 'Header Text', 'marketify' );
$wp_customize->get_control( 'header_textcolor' )->section = 'colors-header';
