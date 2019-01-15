<?php
/**
 * Database Queries
 *
 * @package         EDD\EDD_Advanced_Reports
 * @author          Manuel Vicedo
 * @copyright       Copyright (c) Manuel Vicedo
 * @since     		1.0
 *
 */
 

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


//Retrieve earnings for the given product and date range
//Return an array of dates and values for each day
function edd_advanced_reports_get_earnings_gross($download = 0, $status = 'all', $start, $end){
	global $wpdb;

	$args = "SELECT 
	$wpdb->posts.ID as post_id, 
	$wpdb->posts.post_date as post_date, 
	$wpdb->postmeta.meta_value as post_value
	FROM $wpdb->posts
	INNER JOIN $wpdb->postmeta
	ON $wpdb->posts.ID = $wpdb->postmeta.post_id
	AND $wpdb->posts.post_date >= '%s'
	AND $wpdb->posts.post_date <= '%s'
	AND $wpdb->posts.post_type = 'edd_payment'
	AND $wpdb->postmeta.meta_key = '_edd_payment_total'";
	if($status != 'all' && $status != '')
		$args .= "AND $wpdb->posts.post_status = '%s'";
	
	$args = $wpdb->prepare($args, $start, $end.' 23:59:59', $status);
	$post_list = $wpdb->get_results($args);
	
	$data = array();
	$data_ids = array();
	foreach($post_list as $current_post){
		$data_ids[] = $current_post->post_id;
		$data[] = array('id' => $current_post->post_id, 'date' => $current_post->post_date, 'value' => $current_post->post_value);
	}
	
	//If filtering by download, get payment meta
	if($download != 0){
		$args = "SELECT 
		$wpdb->postmeta.post_id as post_id, 
		$wpdb->postmeta.meta_value as post_value
		FROM $wpdb->postmeta
		WHERE $wpdb->postmeta.meta_key = '_edd_payment_meta'
		AND $wpdb->postmeta.post_id IN (".implode(',', $data_ids).");";
		$meta_list = $wpdb->get_results($args);
		
		//Find matching metadata with payment
		foreach($meta_list as $current_meta){
			foreach($data as $current_data_key => $current_data){
				if($current_data['id'] == $current_meta->post_id){
					//Look for the correct cart items, and add totals
					$metadata = unserialize($current_meta->post_value);
					$added_value = 0;
					if(isset($metadata['cart_details']) && is_array($metadata['cart_details'])){
						foreach($metadata['cart_details'] as $current_item){
							if($download == $current_item['id']){
								$added_value += $current_item['price'];
							}
						}
					}
					$data[$current_data_key]['value'] = $added_value;
				}
			}
		}
	}
	return $data;
}


//Retrieve net earnings for the given product and date range (gross minus tax)
function edd_advanced_reports_get_earnings_net($download = 0, $status = 'all', $start, $end){
	global $wpdb;

	$args = "SELECT 
	$wpdb->posts.ID as post_id, 
	$wpdb->posts.post_date as post_date, 
	$wpdb->postmeta.meta_value as post_value,
	meta_tax.meta_value as tax_value
	FROM $wpdb->posts
	INNER JOIN $wpdb->postmeta
	ON $wpdb->posts.ID = $wpdb->postmeta.post_id
	AND $wpdb->posts.post_date >= '%s'
	AND $wpdb->posts.post_date <= '%s'
	AND $wpdb->posts.post_type = 'edd_payment'
	AND $wpdb->postmeta.meta_key = '_edd_payment_total'
	INNER JOIN $wpdb->postmeta as meta_tax
	ON $wpdb->posts.ID = meta_tax.post_id
	AND meta_tax.meta_key = '_edd_payment_tax'";
	if($status != 'all' && $status != '')
		$args .= "AND $wpdb->posts.post_status = '%s'";
	
	$args = $wpdb->prepare($args, $start, $end.' 23:59:59', $status);
	$post_list = $wpdb->get_results($args);
	
	$data = array();
	$data_ids = array();
	foreach($post_list as $current_post){
		$data_ids[] = $current_post->post_id;
		$data[] = array('id' => $current_post->post_id, 'date' => $current_post->post_date, 'value' => $current_post->post_value - $current_post->tax_value);
	}
	
	//If filtering by download, get payment meta
	if($download != 0){
		$args = "SELECT 
		$wpdb->postmeta.post_id as post_id, 
		$wpdb->postmeta.meta_value as post_value
		FROM $wpdb->postmeta
		WHERE $wpdb->postmeta.meta_key = '_edd_payment_meta'
		AND $wpdb->postmeta.post_id IN (".implode(',', $data_ids).");";
		$meta_list = $wpdb->get_results($args);
		
		//Find matching metadata with payment
		foreach($meta_list as $current_meta){
			foreach($data as $current_data_key => $current_data){
				if($current_data['id'] == $current_meta->post_id){
					//Look for the correct cart items, and add totals
					$metadata = unserialize($current_meta->post_value);
					$added_value = 0;
					if(isset($metadata['cart_details']) && is_array($metadata['cart_details'])){
						foreach($metadata['cart_details'] as $current_item){
							if($download == $current_item['id']){
								$added_value += $current_item['subtotal'];
							}
						}
					}
					$data[$current_data_key]['value'] = $added_value;
				}
			}
		}
	}
	return $data;
}


//Retrieve taxes for the given product and date range
function edd_advanced_reports_get_earnings_tax($download = 0, $status = 'all', $start = false, $end = false ){
	global $wpdb;

	$args = "SELECT 
	$wpdb->posts.ID as post_id, 
	$wpdb->posts.post_date as post_date, 
	$wpdb->postmeta.meta_value as post_value
	FROM $wpdb->posts
	INNER JOIN $wpdb->postmeta
	ON $wpdb->posts.ID = $wpdb->postmeta.post_id
	AND $wpdb->posts.post_status >= 'publish'
	AND $wpdb->posts.post_date >= '%s'
	AND $wpdb->posts.post_date <= '%s'
	AND $wpdb->posts.post_type = 'edd_payment'
	AND $wpdb->postmeta.meta_key = '_edd_payment_tax'";
	if($status != 'all' && $status != '')
		$args .= "AND $wpdb->posts.post_status = '%s'";
	$args = $wpdb->prepare($args, $start, $end.' 23:59:59', $status);
	$post_list = $wpdb->get_results($args);
	
	$data = array();
	$data_ids = array();
	foreach($post_list as $current_post){
		$data_ids[] = $current_post->post_id;
		$data[] = array('id' => $current_post->post_id, 'date' => $current_post->post_date, 'value' => $current_post->post_value);
	}
	
	//If filtering by download, get payment meta
	if($download != 0){
		$args = "SELECT 
		$wpdb->postmeta.post_id as post_id, 
		$wpdb->postmeta.meta_value as post_value
		FROM $wpdb->postmeta
		WHERE $wpdb->postmeta.meta_key = '_edd_payment_meta'
		AND $wpdb->postmeta.post_id IN (".implode(',', $data_ids).");";
		$meta_list = $wpdb->get_results($args);
		
		//Find matching metadata with payment
		foreach($meta_list as $current_meta){
			foreach($data as $current_data_key => $current_data){
				if($current_data['id'] == $current_meta->post_id){
					//Look for the correct cart items, and add totals
					$metadata = unserialize($current_meta->post_value);
					$added_value = 0;
					if(isset($metadata['cart_details']) && is_array($metadata['cart_details'])){
						foreach($metadata['cart_details'] as $current_item){
							if($download == $current_item['id']){
								$added_value += $current_item['tax'];
							}
						}
					}
					$data[$current_data_key]['value'] = $added_value;
				}
			}
		}
	}
	return $data;
}


//Retrieve number of completed payments
function edd_advanced_reports_get_sales($download = 0, $status = 'all', $start = false, $end = false ){
	global $wpdb;

	$args = "SELECT 
	$wpdb->posts.ID as post_id, 
	$wpdb->posts.post_date as post_date
	FROM $wpdb->posts
	WHERE $wpdb->posts.post_type = 'edd_payment'
	AND $wpdb->posts.post_date >= '%s'
	AND $wpdb->posts.post_date <= '%s'";
	if($status != 'all' && $status != '')
		$args .= "AND $wpdb->posts.post_status = '%s'";
	
	$args = $wpdb->prepare($args, $start, $end.' 23:59:59', $status);
	$post_list = $wpdb->get_results($args);
	
	$data = array();
	$data_ids = array();
	foreach($post_list as $current_post){
		$data_ids[] = $current_post->post_id;
		$data[] = array('id' => $current_post->post_id, 'date' => $current_post->post_date, 'value' => 1);
	}
	
	//If filtering by download, get payment meta
	if($download != 0){
		$args = "SELECT 
		$wpdb->postmeta.post_id as post_id, 
		$wpdb->postmeta.meta_value as post_value
		FROM $wpdb->postmeta
		WHERE $wpdb->postmeta.meta_key = '_edd_payment_meta'
		AND $wpdb->postmeta.post_id IN (".implode(',', $data_ids).");";
		$meta_list = $wpdb->get_results($args);
		
		//Find matching metadata with payment
		foreach($meta_list as $current_meta){
			foreach($data as $current_data_key => $current_data){
				if($current_data['id'] == $current_meta->post_id){
					//Look for the correct cart items, and add totals
					$metadata = unserialize($current_meta->post_value);
					$added_value = 0;
					if(isset($metadata['cart_details']) && is_array($metadata['cart_details'])){
						foreach($metadata['cart_details'] as $current_item){
							if($download == $current_item['id']){
								$added_value += 1;
							}
						}
					}
					$data[$current_data_key]['value'] = $added_value;
				}
			}
		}
	}
	return $data;
}