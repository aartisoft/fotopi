<?php 
/**
 * Form fields generation
 *
 * @package         EDD\EDD_Advanced_Reports
 * @author          Manuel Vicedo
 * @copyright       Copyright (c) Manuel Vicedo
 * @since     		1.0
 *
 */
 
 
// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


/**
 * Standard text field
 *
 * @since 1.0
 * @return $output
 */
if(!function_exists('edd_advanced_reports_form_text')){
	function edd_advanced_reports_form_text($name, $value, $args = null){
		if(isset($args['width'])) $field_width = ' style="width:'.$args['width'].';"'; else $field_width = '';
		if(isset($args['placeholder'])) $field_placeholder = ' placeholder="'.$args['placeholder'].'"'; else $field_placeholder = '';
		$output = '<input type="text" value="'.htmlentities(stripslashes($value), ENT_QUOTES, 'UTF-8').'" name="'.$name.'" id="'.$name.'"'.$field_width.$field_placeholder.'/>';
		return $output;
	}
}
	
	
/**
 * Textarea field
 *
 * @since 1.0
 * @return $output
 */
if(!function_exists('edd_advanced_reports_form_textarea')){
	function edd_advanced_reports_form_textarea($name, $value, $args = null){	
		if(isset($args['placeholder'])) $field_placeholder = ' placeholder="'.$args['placeholder'].'"'; else $field_placeholder = '';		
		$output = '<textarea name="'.$name.'" id="'.$name.'"'.$field_placeholder.'>'.(stripslashes($value)).'</textarea>';
		return $output;
	}
}


/**
 * Yes/no radio buttons
 *
 * @since 1.0
 * @return $output
 */
if(!function_exists('edd_advanced_reports_form_yesno')){
	function edd_advanced_reports_form_yesno($name, $value, $args = null){
		$checked_yes = '';
		$checked_no = ' checked';
		if($value == '1'){
			$checked_yes = ' checked';
			$checked_no = '';
		}
		$output = '';
		$output .= '<label for="'.$name.'_yes">';
		$output .= '<input type="radio" name="'.$name.'" id="'.$name.'_yes" value="1" '.$checked_yes.'/>'; 
		$output .= __('Yes', 'eddar').'</label>';
		$output .= '&nbsp;&nbsp;&nbsp;&nbsp;';
		
		$output .= '<label for="'.$name.'_no">';
		$output .= '<input type="radio" name="'.$name.'" id="'.$name.'_no" value="0" '.$checked_no.'/>'; 
		$output .= __('No', 'eddar').'</label>';
		return $output;
	}
}


/**
 * Dropdown list field
 *
 * @since 1.0
 * @return $output
 */
if(!function_exists('edd_advanced_reports_form_select')){
	function edd_advanced_reports_form_select($name, $value, $list, $args = null){
		if(isset($args['width'])) $field_width = ' style="width:'.$args['width'].';"'; else $field_width = '';
		if(isset($args['multiple'])) $field_multiple = ' multiple="multiple"'; else $field_multiple = '';
		$field_class = (isset($args['class']) ? $args['class'] : '');
		$output = '<select class="eddar-metabox_field_select '.$field_class.'" name="'.$name.'" id="'.$name.'"'.$field_width.$field_multiple.'>';
		if(sizeof($list) > 0)
			foreach($list as $list_key => $list_value){
				if(is_array($list_value)){
					$disabled = '';
					if(isset($list_value['type']) && $list_value['type'] == 'separator')
						$disabled = ' disabled';
					$output .= '<option value="'.htmlentities(stripslashes($list_key)).'"'.$disabled;
					$output .= '>'.str_replace('&amp;', '&', htmlentities(stripslashes($list_value['name']), ENT_QUOTES, "UTF-8")).'</option>';
				}else{
					$output .= '<option value="'.htmlentities(stripslashes($list_key)).'" ';
					$output .= selected($value, $list_key, false);
					$output .= '>'.str_replace('&amp;', '&', htmlentities(stripslashes($list_value), ENT_QUOTES, "UTF-8")).'</option>';
				}
			}
		$output .= '</select>';
		return $output;
	}
}


/**
 * Expandable list of field elements-- can contain other fields
 *
 * @since 1.0
 * @return $output
 */
if(!function_exists('edd_advanced_reports_form_collection')){
	function edd_advanced_reports_form_collection($name, $value, $list, $args = null){
		$field_class = (isset($args['class']) ? $args['class'] : '');
		$output = '<div class="eddar-metabox-field-collection '.$field_class.'">';
		//Check that given value is an array. If empty, add a single row
		if(empty($value) || $value == '')
			$value = array('');
		
		$output .= '<table>';
		
		//Table header
		$output .= '<thead>';
		foreach($list as $list_key => $list_value){
			$field_title = isset($list_value['label']) ? $list_value['label'] : $list_value;
			$output .= '<th>'.$field_title.'</th>';
		}
		$output .= '</thead>';
		
		//Table contents
		
		$counter = -1;
		foreach($value as $current_key => $current_value){
			$counter++;
			$output .= '<tr class="eddar-collection-row" data-index="'.$current_key.'">';
			foreach($list as $list_key => $list_value){
				$output .= '<td>';
				
				//Save field data-- collections can be of any field type
				$field_name = $name.'['.$current_key.']['.$list_key.']';
				$field_type = isset($list_value['type']) ? $list_value['type'] : 'text';
				$field_args = isset($list_value['args']) ? $list_value['args'] : null;
				$field_options = isset($list_value['option']) ? $list_value['option'] : null;
				$field_value = isset($current_value[$list_key]) ? $current_value[$list_key] : '';				
				
				//Display corresponding type of field
				if($field_type == 'text')
					$output .= edd_advanced_reports_form_text($field_name, $field_value, $field_args);
				
				elseif($field_type == 'textarea')
					$output .= edd_advanced_reports_form_textarea($field_name, $field_value, $field_args);
				
				elseif($field_type == 'select')
					$output .= edd_advanced_reports_form_select($field_name, $field_value, $field_options, $field_args);
				
				elseif($field_type == 'yesno')
					$output .= edd_advanced_reports_form_yesno($field_name, $field_value, $field_args);
				
				elseif($field_type == 'color')
					$output .= edd_advanced_reports_form_color($field_name, $field_value);
				
				elseif($field_type == 'date') 
					$output .= edd_advanced_reports_form_date($field_name, $field_value, null);
				
				$output .= '</td>';
			}
			$output .= '<td>';
			$output .= '<a href="#" tabindex="-1" class="eddar-collection-remove-row">'.__('Remove', 'eddar').'</a>';
			$output .= '</td>';
			$output .= '</tr>';
		}
		$output .= '<tr>';
		$output .= '<td>';
		$output .= '<a href="#" class="button eddar-collection-add-row">'.__('Add Row', 'eddar').'</a>';
		$output .= '</td>';
		$output .= '</tr>';
		$output .= '</table>';
			
		$output .= '</div>';
		return $output;
	}
}
	

/**
 * Color picker field
 *
 * @since 1.0
 * @return $output
 */
if(!function_exists('edd_advanced_reports_form_color')){
	function edd_advanced_reports_form_color($name, $value, $args = null){
		if(isset($args['placeholder'])) $field_placeholder = ' placeholder="'.$args['placeholder'].'"'; else $field_placeholder = '';		
		$output = '<div id="'.$name.'_wrap">';
		$output .= '<input type="text" class="color" value="'.esc_attr($value).'" name="'.$name.'" id="'.$name.'"'.$field_placeholder.' maxlength="7"/>';
		//$output .= '<div class="colorselector" id="'.$name.'_sample"></div>';
		$output .= '</div>';	
		return $output;
	}
}


/**
 * Date picker field
 *
 * @since 1.0
 * @return $output
 */
if(!function_exists('edd_advanced_reports_form_date')){
	function edd_advanced_reports_form_date($name, $value, $args = null){
		if(isset($args['placeholder'])) $field_placeholder = ' placeholder="'.$args['placeholder'].'"'; else $field_placeholder = '';
		if(isset($args['autocomplete'])) $field_autocomplete = ' autocomplete="'.$args['placeholder'].'"'; else $field_autocomplete = ' autocomplete="off"';
		$output = '<input type="text" class="eddar-dateselector" value="'.stripslashes($value).'" name="'.$name.'" id="'.$name.'"'.$field_placeholder.$field_autocomplete.'/>';
		return $output;
	}
}