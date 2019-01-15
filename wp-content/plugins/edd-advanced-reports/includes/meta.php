<?php
/**
 * Metabox field generation
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
 * Generate meta fields based on settings array
 *
 * @access      public
 * @since       1.0.0
 */
if(!function_exists('edd_advanced_reports_meta_fields')){
	function edd_advanced_reports_meta_fields($post, $cpo_metadata = null){
		if($cpo_metadata == null || sizeof($cpo_metadata) == 0) return;
		$output = '';
		
		wp_nonce_field('eddar_savemeta', 'eddar_nonce');
		
		foreach($cpo_metadata as $current_meta){
			$field_name = $current_meta["name"];
			$field_title = $current_meta['label'];
			$field_desc = $current_meta['desc'];
			$field_type = $current_meta['type'];
			$field_value = '';
			$field_value = get_post_meta($post->ID, $field_name, true);
			
			//Additional CSS classes depending on field type
			$field_classes = '';
			if($field_type == 'collection') $field_classes = ' eddar-metabox-wide';
			
			$output .= '<div class="eddar-metabox '.$field_classes.'"><div class="name">'.$field_title.'</div>';
			$output .= '<div class="field">';
			
			// Print metaboxes here. Develop different cases for each type of field.
			if($field_type == 'text')
				$output .= edd_advanced_reports_form_text($field_name, $field_value, $current_meta);
			
			elseif($field_type == 'textarea')
				$output .= edd_advanced_reports_form_textarea($field_name, $field_value, $current_meta);
			
			elseif($field_type == 'select')
				$output .= edd_advanced_reports_form_select($field_name, $field_value, $current_meta['option'], $current_meta);
			
			elseif($field_type == 'collection')
				$output .= edd_advanced_reports_form_collection($field_name, $field_value, $current_meta['option'], $current_meta);
			
			elseif($field_type == 'yesno')
				$output .= edd_advanced_reports_form_yesno($field_name, $field_value, $current_meta);
			
			elseif($field_type == 'color')
				$output .= edd_advanced_reports_form_color($field_name, $field_value);
					
			elseif($field_type == 'date') 
				$output .= edd_advanced_reports_form_date($field_name, $field_value, null);
				
			$output .= '</div>';
			$output .= '<div class="desc">'.$field_desc.'</div></div>';
		}
		echo $output;
	}
}
	
/**
 * Saves meta field data into database
 *
 * @access      public
 * @since       1.0.0
 */
if(!function_exists('edd_advanced_reports_meta_save')){
	function edd_advanced_reports_meta_save($option){

		if(!isset($_POST['post_ID']) || !isset($_POST['eddar_nonce'])) return;
		if(!wp_verify_nonce($_POST['eddar_nonce'], 'eddar_savemeta')) return;
		
		$cpo_metaboxes = $option;
		$post_id = $_POST['post_ID'];
			
		//Check if we're editing a post
		if(isset($_POST['action']) && $_POST['action'] == 'editpost'){                                   
			
			//Check every option, and process the ones there's an update for.
			if(sizeof($cpo_metaboxes) > 0)
			foreach ($cpo_metaboxes as $current_meta){
			   
				$field_name = $current_meta['name'];
				
				//If the field has an update, process it.
				if(isset($_POST[$field_name])){
					$field_value = $_POST[$field_name];
					
					// Delete unused metadata
					if(empty($field_value) || $field_value == ''){ 
						delete_post_meta($post_id, $field_name, get_post_meta($post_id, $field_name, true));
					}
					// Update metadata
					else{ 
						update_post_meta($post_id, $field_name, $field_value);
					}
				}
			}
		}
	}
}