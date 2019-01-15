<?php 
/**
 * Dashboard Widgets
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
 * Create dashboard widgets for all pinned reports
 *
 * @access      public
 * @since       1.0.0
 */
add_action( 'wp_dashboard_setup', 'edd_advanced_reports_dashboard_init');
function edd_advanced_reports_dashboard_init(){
	if(!current_user_can('view_shop_reports')) return;
	$advanced_reports = new WP_Query('post_type=edd_advanced_report&posts_per_page=-1&order=ASC&orderby=menu_order');	
	foreach($advanced_reports->posts as $current_report){
		if(get_post_meta($current_report->ID, 'edd_report_widget', true) == 1){
			wp_add_dashboard_widget('eddar_advanced_report_'.$current_report->ID, $current_report->post_title, 'edd_advanced_reports_dashboard_widget', null, array('id' => $current_report->ID));
		}
	}
}


/**
 * Generate dashboard widget contents
 *
 * @access      public
 * @since       1.0.0
 */
function edd_advanced_reports_dashboard_widget($post, $args){
    $report_id = $args['args']['id'];
	$data = edd_advanced_reports_generate($report_id);
	edd_advanced_reports_graph($data);
	edd_advanced_reports_total($data);
	echo '<strong style="margin:20px 0 0; text-align:right;">';
	echo '<a href="edit.php?post_type=download&page=edd-reports&tab=advanced_reports&view='.$report_id.'">View Report Details</a> | ';
	echo '<a href="post.php?action=edit&post='.$report_id.'">Edit Report</a>';
	echo '</strong>';
}