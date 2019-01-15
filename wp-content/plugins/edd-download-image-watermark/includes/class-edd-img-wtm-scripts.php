<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Scripts Class
 *
 * Handles adding scripts functionality to the admin pages
 * as well as the front pages.
 *
 * @package Easy Digital Downloads - Image Watermark
 * @since 1.0.0
 */
class EDD_Img_Wtm_Scripts{
	
	public function __construct() {
		
	}
	
	/**
	 * Enqueue Admin Scripts
	 * 
	 * Handles to enqueue scripts for admin
	 * 
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	public function edd_img_wtm_admin_scripts( $hook_suffix ) {
		
		global $wp_version;
		
		//Check post type and also check extention tab
		if( (isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == EDD_IMG_WTM_MAIN_POSTTYPE
			&& isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'edd-settings'
			&& isset( $_REQUEST['tab'] ) && $_REQUEST['tab'] == 'extensions' ) || $hook_suffix == 'download_page_edd_img_wtm_regenerate_thumb' ) {
			
			$upload_dir   = wp_upload_dir();
			
			wp_register_script( 'edd-img-wtm-admin-scripts', EDD_IMG_WTM_URL.'includes/js/edd-img-wtm-admin.js' , array( 'jquery', 'thickbox' ), EDD_IMG_WTM_VERSION, true );
			wp_enqueue_script( 'edd-img-wtm-admin-scripts' );
			
			//localize script
			$newui = $wp_version >= '3.5' ? '1' : '0'; //check wp version for showing media uploader
			wp_localize_script( 'edd-img-wtm-admin-scripts', 'EddImgWtm', array( 
																						'new_media_ui'		=>	$newui,
																						'upload_base_url'	=>	$upload_dir['baseurl'],
																					));
																					
			wp_enqueue_media();

			//Enque regenerate thumb
			wp_enqueue_script( 'jquery-ui-progressbar' );
		}
	}

	/**
	 * Enqueue Admin style
	 * 
	 * Handles to enqueue style for admin
	 * 
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	public function edd_img_wtm_admin_styles() {
		
		//Check post type and also check extention tab
		if( isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == EDD_IMG_WTM_MAIN_POSTTYPE
			&& isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'edd-settings'
			&& isset( $_REQUEST['tab'] ) && $_REQUEST['tab'] == 'extensions' ) {
				
			wp_register_style( 'edd-img-wtm-admin-style', EDD_IMG_WTM_URL.'includes/css/edd-img-wtm-admin.css', array(), EDD_IMG_WTM_VERSION );
			wp_enqueue_style( 'edd-img-wtm-admin-style' );
		}
	}
	
	/**
	 * Adding Hooks
	 *
	 * Adding proper hooks for the scripts.
	 *
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	public function add_hooks() {
		
		//add style to back side for image watermark settings
		add_action( 'admin_enqueue_scripts', array( $this, 'edd_img_wtm_admin_styles' ) );
		
		//add script to back side for image watermark settings
		add_action( 'admin_enqueue_scripts', array( $this, 'edd_img_wtm_admin_scripts' ) );
				
	}
}
?>
