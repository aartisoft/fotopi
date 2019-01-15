<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Public Class
 *
 * Handles generic Public functionality and AJAX requests.
 *
 * @package Easy Digital Downloads - Image Watermark
 * @since 1.0.0
 */
class EDD_Img_Wtm_Public {
	
	var $model;
	
	public function __construct() {	
	
		global $edd_img_wtm_model;
		
		$this->model = $edd_img_wtm_model;
		
	}
	
	/**
	 * Get Main Souce
	 * 
	 * Handles to get main source when download source
	 *
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	function edd_img_wtm_requested_file( $download_file ) {
		
		//Get Upload directory path
		$upload_dir = wp_upload_dir();
		
		$filepath = $download_file;
		$filepath = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $filepath ); // replace url to absulate path
		
		if( file_exists( $filepath ) ) { // Check file is exist
			
			// Get backup file( orginal file without watermarked )  from upload directory
			$backup_file = $this->model->edd_img_wtm_backup_image_file_name( $filepath );
			
			if( file_exists( $backup_file ) && is_file( $backup_file ) ) { // Check backup file exist
				
				$download_file = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $backup_file ); // replace absulate path to url
			}
		}
		return $download_file;
	}
	
	/**
	 * Display Original Image Name
	 * 
	 * Handles to display original image name
	 *
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	function edd_img_wtm_requested_file_name( $download_file ) {
		
		$download_file = str_replace( EDD_IMG_WTM_BACKUP_PREFIX, '', $download_file );
		return $download_file;
	}
	
	/**
	 * Disable srcset attribute
	 * 
	 * Handles to disable srcset attribute
	 * Refer: https://make.wordpress.org/core/2015/11/10/responsive-images-in-wordpress-4-4/
	 *
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	function edd_img_wtm_disable_srcset( $sources ) {				
		  return false;		
	}
	
	/**
	 * Adding Hooks
	 *
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	public function add_hooks() {
		
		//add filter to get main source
		add_filter( 'edd_requested_file', array( $this, 'edd_img_wtm_requested_file' ) );
		add_filter( 'edd_requested_file_name', array( $this, 'edd_img_wtm_requested_file_name' ) );
		
		// add filter to disable srcset attribute on img tag
		add_filter( 'wp_calculate_image_srcset', array( $this, 'edd_img_wtm_disable_srcset' ) );
	}
}