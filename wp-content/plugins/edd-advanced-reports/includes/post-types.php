<?php
/**
 * Post Types
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
 * Register advanced report post type
 *
 * @since 1.0
 * @global $post
 * @return void
 */
function edd_advanced_reports_post_types(){
	//Add portfolio
	$labels = array('name' => __('Manage Advanced Reports', 'eddar'),
	'singular_name' => __('Report', 'eddar'),
	'add_new' => __('Add Report', 'eddar'),
	'add_new_item' => __('Add New Report', 'eddar'),
	'edit_item' => __('Edit Report', 'eddar'),
	'new_item' => __('New Report', 'eddar'),
	'view_item' => __('View Reports', 'eddar'),
	'search_items' => __('Search Reports', 'eddar'),
	'not_found' =>  __('No reports items found.', 'eddar'),
	'not_found_in_trash' => __('No reports found in the trash.', 'eddar'), 
	'parent_item_colon' => '');
	
	$fields = array('labels' => $labels,
	'public' => false,
	'publicly_queryable' => false,
	'show_ui' => true, 
	'query_var' => true,
	'capability_type' => 'post',
	'hierarchical' => false,
	'show_in_nav_menus' => true,
	'menu_icon' => 'dashicons-portfolio',
	'show_in_menu' => 'edit.php?post_type=download&page=edd-reports',
	'menu_position' => null,
	'supports' => array('title')); 
	register_post_type('edd_advanced_report', $fields);
}
add_action('init', 'edd_advanced_reports_post_types');


/**
 * Post type messages
 *
 * Change post updated messsage
 *
 * @since 1.0
 * @global $post
 * @return void
 */
function edd_advanced_reports_post_messages($messages){
	global $post, $post_ID;
	
	$link = '<a href="edit.php?post_type=download&page=edd-reports&tab=advanced_reports&view='.$post_ID.'">'.__('View report.', 'eddar').'</a>';
	
	$messages['edd_advanced_report'] = array(
	0 => '',
	1 => sprintf(__('Report updated. %s', 'eddar'), $link),
	2 => sprintf(__('Report updated. %s', 'eddar'), $link),
	3 => sprintf(__('Report updated. %s', 'eddar'), $link),
	4 => sprintf(__('Report updated. %s', 'eddar'), $link),
	5 => sprintf(__('Report updated. %s', 'eddar'), $link),
	6 => sprintf(__('Report created. %s', 'eddar'), $link),
	7 => sprintf(__('Report updated. %s', 'eddar'), $link),
	8 => sprintf(__('Report updated. %s', 'eddar'), $link),
	9 => sprintf(__('Report updated. %s', 'eddar'), $link),
	10 => sprintf(__('Report updated. %s', 'eddar'), $link),
	);

	return $messages;
}
add_filter('post_updated_messages', 'edd_advanced_reports_post_messages');


/**
 * Post type columns
 *
 * Registers the admin table columns for advanced reports
 *
 * @since 1.0
 * @global $post
 * @return void
 */
function edd_advanced_reports_post_columns($columns){
	$columns = array(
	'cb' => '<input type="checkbox" />',
	'title' => __('Title', 'eddar'),
	'eddar-date' => __('Timespan', 'eddar'),
	'eddar-widget' => __('On Dashboard', 'eddar'),
	'eddar-view' => __('View', 'eddar'),
	'date' => __('Date'),
	);
	return $columns;
}
add_filter('manage_edit-edd_advanced_report_columns', 'edd_advanced_reports_post_columns') ;


/**
 * Post type columns content
 *
 * Registers the admin table columns for advanced reports
 *
 * @since 1.0
 * @global $post
 * @return void
 */
function edd_advanced_reports_post_columns_content($column){
	global $post;
	switch($column){
		case 'eddar-date': 
			printf(__('%s Days', 'eddar'), get_post_meta($post->ID, 'edd_report_date', true)); 
		break;	
		case 'eddar-widget': 
			if(get_post_meta($post->ID, 'edd_report_widget', true) == 1) echo __('Yes', 'eddar'); else echo __('No', 'eddar'); 
		break;	
		case 'eddar-view': 
			echo '<a href="edit.php?post_type=download&page=edd-reports&tab=advanced_reports&view='.$post->ID.'">'.__('View report', 'eddar').'</a>';
		break;	
		default:break;
	}
}
add_action('manage_posts_custom_column', 'edd_advanced_reports_post_columns_content', 2);


/**
 * Add metaboxes to advanced reports posts
 *
 * @since 1.0
 * @global $post
 * @return void
 */
function edd_advanced_reports_metaboxes(){
	add_meta_box('edd_advanced_report_fields', __('Report Details', 'eddar'), 'edd_advanced_reports_metabox', 'edd_advanced_report', 'normal', 'high');
}
add_action('add_meta_boxes', 'edd_advanced_reports_metaboxes');


/**
 * Display and save post metaboxes
 *
 * @since 1.0
 * @global $post
 * @return void
 */
function edd_advanced_reports_metabox($post){ 
	echo '<style> #minor-publishing { display:none; } </style>';
	edd_advanced_reports_meta_fields($post, edd_advanced_reports_metadata_report());
}


/**
 * Save metaboxes on post update
 *
 * @since 1.0
 * @global $post
 * @return void
 */
function edd_advanced_reports_metabox_save($post){
	edd_advanced_reports_meta_save(edd_advanced_reports_metadata_report());
}
add_action('save_post_edd_advanced_report', 'edd_advanced_reports_metabox_save');


/**
 * Add a link back to the reports page on Manage Reports
 *
 * @since 1.0
 * @global $post
 * @return void
 */
add_action('admin_notices', 'edd_advanced_reports_post_backlink');
function edd_advanced_reports_post_backlink(){
	$screen = get_current_screen();
    if((isset($_GET['post_type']) && $_GET['post_type'] == 'edd_advanced_report') || $screen->post_type == 'edd_advanced_report'){
		echo '<a class="eddar-backlink" href="edit.php?post_type=download&page=edd-reports&tab=advanced_reports">&larr; '.__('Back To Advanced Reports', 'eddar').'</a>';
    }    
}