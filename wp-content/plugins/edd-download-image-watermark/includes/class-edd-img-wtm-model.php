<?php 

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Model Class
 *
 * Handles generic plugin functionality.
 *
 * @package Easy Digital Downloads - Image Watermark
 * @since 1.0.0
 */
class EDD_Img_Wtm_Model {
	
	public function __construct() {
	
	}
	
	/**
	 * Escape Tags & Slashes
	 *
	 * Handles escapping the slashes and tags
	 *
	 * @package  Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	public function edd_img_wtm_escape_attr($data){
		
		return esc_attr(stripslashes($data));
	}
	
	/**
	 * Strip Slashes From Array
	 * 
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	public function edd_img_wtm_escape_slashes_deep( $data = array(), $flag = false ) {
		
		if( $flag != true ) {
			
			$data = $this->edd_img_wtm_nohtml_kses($data);
			
		}
		$data = stripslashes_deep($data);
		return $data;
	}
	
	/**
	 * Strip Html Tags 
	 * 
	 * It will sanitize text input (strip html tags, and escape characters)
	 * 
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	public function edd_img_wtm_nohtml_kses( $data = array() ) {
		
		if ( is_array($data) ) {
			
			$data = array_map( array( $this,'edd_img_wtm_nohtml_kses' ), $data );
			
		} elseif ( is_string( $data ) ) {
			
			$data = wp_filter_nohtml_kses($data);
		}
		
		return $data;
	}
	
	/**
	 * Add plugin section in extension settings
	 *
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.1.5
	 */
	 public function edd_img_wtm_settings_section( $sections ) {
	    $sections['eddimgwtm'] = __( 'Download Image Watermark', 'eddimgwtm' );
 	 	return $sections;
	 }
	 
	/**
	 * Register Settings
	 * 
	 * Handels to add settings in settings page
	 * 
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	public function edd_img_wtm_settings( $settings ) {
		
		$img_types_settings = array(
		
				array(
					'id'	=> 'edd_img_wtm_main',
					'name'	=> '<strong>' . __( 'Image Watermark Options', 'eddimgwtm' ) . '</strong>',
					'desc'	=> __( 'Image Watermark Settings', 'eddimgwtm' ),
					'type'	=> 'header'
				)
		);
		
		$img_types = edd_img_wtm_get_types();
		
		foreach ( $img_types as $img_type ) {
			
			$img_types_settings[] = array(
										'id'		=> 'edd_img_wtm_'.$img_type.'_img',
										'name'		=> sprintf(__('%s Watermark Image','eddimgwtm'),ucwords($img_type)),
										'desc'		=> sprintf(__( 'Select watermark image. This watermark image is applied to the %s image on a Downloads page.%sNote%s: Please use a PNG image for the watermark.', 'eddimgwtm' ), $img_type, '<br /><strong>', '</strong>'),
										'type'		=> 'imgwtmimg',
										'std'		=> ''
									);

			// option to apply repeted watermark on whole image
			$img_types_settings[] = array(
										'id'		=> 'edd_img_wtm_'.$img_type.'_repeated_on_image',
										'name'		=> sprintf(__('Repeat %s Watermark Image','eddimgwtm'),ucwords($img_type)),
										'desc'		=> sprintf(__('Check this box to repeat watermark on %s image','eddimgwtm'),$img_type).'<br><p><strong>'. __('Note: ', 'eddimgwtm') .'</strong>'.__('If you enable this option, watermark image alignment setting will not work.','eddimgwtm').'</p>',
										'type'		=> 'checkbox',
										'std'		=> ''
									);

			$img_types_settings[] = array(
										'id'		=> 'edd_img_wtm_'.$img_type.'_align',
										'name'		=> sprintf(__('%s Watermark Image Alignment','eddimgwtm'),ucwords($img_type)),
										'desc'		=> __('Choose watermark alignment.','eddimgwtm'),
										'type'		=> 'imgwtmalign',
										'std'		=> ''
									);
		}
		
		// If EDD is at version 2.5 or later
	    if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
	    	$img_types_settings = array( 'eddimgwtm' => $img_types_settings );
	    }
		
		return array_merge( $settings, $img_types_settings );
	}
	
	/**
	 * Set backup file name
	 * 
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	function edd_img_wtm_backup_image_file_name( $filepath ) {
		
		$filepath = str_replace( 'jpeg', 'jpg', $filepath );
		$filepath = strtolower( $filepath );
		
		return dirname( $filepath ).DIRECTORY_SEPARATOR . EDD_IMG_WTM_BACKUP_PREFIX . basename( $filepath );
	}
}