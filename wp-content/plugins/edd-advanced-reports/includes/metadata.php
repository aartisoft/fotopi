<?php

/**
 * Store metadata for report posts
 *
 * @access      public
 * @since       1.0.0
 */
function edd_advanced_reports_metadata_report(){

	$metadata = array();
	
	$metadata['edd_report_date'] = array(
	'name' => 'edd_report_date',
	'label' => __('Date Range', 'eddar'),
	'desc' => __('Enter the number of days that the report should display by default, starting from the current day and going back in time. When viewing the report, you will be able to choose a custom time period.', 'eddar'),
	'std' => '30',
	'type' => 'text',
	'placeholder' => 'e.g. 30',
	'width' => '120px');
	
	/*$metadata['edd_report_format'] = array(
	'name' => 'edd_report_format',
	'std'  => '',
	'label' => __('Report Format', 'eddar'),
	'desc' => __('Specifies the format of the footer for this page.', 'eddar'),
	'option' => eddar_metadata_report_series_format(),
	'type' => 'select');*/
	
	$metadata['edd_report_widget'] = array(
	'name' => 'edd_report_widget',
	'std'  => '',
	'label' => __('Show In Dashboard', 'eddar'),
	'desc' => __('Displays this report on the WordPress dashboard.', 'eddar'),
	'type' => 'yesno');

	$metadata['edd_report_series'] = array(
	'name' => 'edd_report_series',
	'std'  => '',
	'label' => __('Report Series', 'eddar'),
	'desc' => __('Specifies the data that will be displayed on the report. You can add as many different value points as you like.', 'eddar'),
	'option' => edd_advanced_reports_metadata_series(),
	'type' => 'collection');
	
	return apply_filters('eddar_metadata_report_details', $metadata);
}


//Create meta fields for pages and taxonomies alike
function edd_advanced_reports_metadata_series(){
	$metadata = array(
	'context' => array('label' => 'Context', 'type' => 'select', 'option' => edd_advanced_reports_metadata_context()),
	'download' => array('label' => 'Filter By Download', 'type' => 'select', 'option' => edd_advanced_reports_metadata_downloads()),
	'status' => array('label' => 'Filter By Status', 'type' => 'select', 'option' => edd_advanced_reports_metadata_statuses()),
	);
	return $metadata;
}

//Create meta fields for pages and taxonomies alike
function edd_advanced_reports_metadata_statuses($key = null){
	$metadata = array('all' => 'All');
	$metadata = array_merge($metadata, edd_get_payment_statuses());
	return $key != null && isset($metadata[$key]) ? $metadata[$key] : $metadata;
}

function edd_advanced_reports_metadata_report_intervals($key = null){
	$metadata = array(
	'day' => 'Days',
	'week' => 'Weeks',
	'month' => 'Months',
	'year' => 'Years');
	return $key != null && isset($metadata[$key]) ? $metadata[$key] : $metadata;
}

//Metadata for dataset contexts
function edd_advanced_reports_metadata_context($key = null){
	$metadata = array(
	'earnings_gross' => 'Gross Earnings',
	'earnings_net' => 'Net Earnings',
	'earnings_tax' => 'Taxes',
	'sales' => 'Sales');
	return $key != null && isset($metadata[$key]) ? $metadata[$key] : $metadata;
}


//Metadata for report formats
function edd_advanced_reports_metadata_format($key = null){
	$metadata = array(
	'graph' => __('Graph', 'eddar'),
	'table' => __('Table', 'eddar'),
	);
	return $key != null && isset($metadata[$key]) ? $metadata[$key] : $metadata;
}


//Metadata for download list field
function edd_advanced_reports_metadata_downloads(){
	$metadata = array();	
	$page_list = get_posts('post_type=download&orderby=title&posts_per_page=-1');    
	$metadata[0] = 'All Products';
	foreach ($page_list as $current_page)
		$metadata[$current_page->ID] = $current_page->post_title;
	return $metadata;
}