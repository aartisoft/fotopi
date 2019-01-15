<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Misc Functions
 * 
 * All misc functions handles to 
 * different functions 
 * 
 * @package Easy Digital Downloads - Image Watermark
 * @since 1.0.0
 *
 */
	
	/**
	 * Get Image Types
	 * 
	 * Handels to get image types
	 * 
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	function edd_img_wtm_get_types() {

		//$img_types = array( 'full', 'large', 'medium', 'post-thumbnail', 'thumbnail' );
		$full_img_types = array( 'full' );
		$img_types = get_intermediate_image_sizes();
		
		$img_types = array_merge( $full_img_types, $img_types );
		
		return $img_types;
	}
	
	/**
	 * Add Custom Image Watermark Settings
	 * 
	 * Handle to add custom image watermark settings
	 *
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	function edd_imgwtmimg_callback( $args ) {
		
		global $edd_options;
		
		$show_img 	= $html = $value = '';
		$upload_dir	= wp_upload_dir();

		if ( isset( $edd_options[ $args['id'] ] ) ) {
			$value = $edd_options[ $args['id'] ];
		}
		
    	if( !empty( $value ) ) { //check connect button image
			$watermark_img = $upload_dir['baseurl'].$value;
			$show_img = ' <img src="'.$watermark_img.'" alt="'.__('Watermark Image','eddimgwtm').'" />';
		}
		
		$html .= '<input class="regular-text" id="' . $args['id'] . '" type="text" name="edd_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" />';
        $html .='<input class="edd-img-uploader button" id="' . $args['id'] . '_btn" type="button" value="Choose" /><br />';
        $html .='<div id="' . $args['id'] . '_view" class="edd-img-wtm-img-view">'.$show_img.'</div>';
        $html .='<label id="' . $args['id'] . '">' . $args['desc'] . '</label>';
            
		echo $html;
	}
	
	/**
	 * Add Custom Image Watermark Alignment Settings
	 * 
	 * Handle to add custom image watermark alignment settings
	 *
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	function edd_imgwtmalign_callback( $args ) {
		
		global $edd_options;
		
		$show_img 	= $html = $value = '';
		$upload_dir	= wp_upload_dir();

		if ( isset( $edd_options[ $args['id'] ] ) ) {
			$value = $edd_options[ $args['id'] ];
		}
		
    	if( !empty( $value ) ) { //check connect button image
			$watermark_img = $upload_dir['baseurl'].$value;
			$show_img = ' <img src="'.$watermark_img.'" alt="'.__('Watermark Image','eddimgwtm').'" />';
		}
		
		$html .='<table id="watermark_position" border="1">';
            
        foreach( array('t','m','b') as $y ) {
        	$html .= '<tr>';
        	foreach( array('l','c','r') as $x ){
        		$edd_img_wtm_pos_val = $y . $x;
        		$html .='<td><input name="edd_settings[' . $args['id'] . ']" type="radio" value="'.$edd_img_wtm_pos_val.'" '.checked( $value, $edd_img_wtm_pos_val, false ).'/></td>';
        	}
        	$html .= '</tr>';
        }
        $checked_pos = ( !$value || $value == '' ) ? ' checked="checked"' : '';
        $html .='<tr><td colspan="3"><input name="edd_settings[' . $args['id'] . ']" type="radio" value="" '.$checked_pos.' />'.__('No watermark','eddimgwtm').'</td></tr>';
        $html .= '</table>';
        $html .= '<label id="' . $args['id'] . '">' . $args['desc'] . '</label>';
        
		echo $html;
	}
	
?>