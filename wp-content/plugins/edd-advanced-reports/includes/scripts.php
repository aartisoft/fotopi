<?php
/**
 * Scripts
 *
 * @package         EDD\EDD_Advanced_Reports
 * @author          Manuel Vicedo
 * @copyright       Copyright (c) Manuel Vicedo
 * @since     		1.0
 *
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Load Scripts
 *
 * Enqueues the required scripts.
 *
 * @since 1.0
 * @global $post
 * @return void
 */
function edd_advanced_reports_load_scripts() {
	global $post;
	$scripts_path = EDD_ADVANCED_REPORTS_URL.'assets/js/';
	wp_enqueue_script('eddar_script_admin', $scripts_path.'admin.js', array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), false, true);
}
add_action( 'admin_enqueue_scripts', 'edd_advanced_reports_load_scripts' );


/**
 * Load Styles
 *
 * Enqueues the required admin CSS styles.
 *
 * @since 1.0
 * @global $post
 * @return void
 */
function edd_advanced_reports_load_styles() {
	global $post;
	$stylesheets_path = EDD_ADVANCED_REPORTS_URL.'assets/css/';
	wp_enqueue_style('eddar-admin', $stylesheets_path.'admin.css');
}
add_action( 'admin_print_styles', 'edd_advanced_reports_load_styles');