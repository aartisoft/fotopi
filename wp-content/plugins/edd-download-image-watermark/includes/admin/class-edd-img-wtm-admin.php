<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Admin Class
 *
 * Handles generic Admin functionality and AJAX requests.
 *
 * @package Easy Digital Downloads - Image Watermark
 * @since 1.0.0
 */
class EDD_Img_Wtm_Admin {
	
	var $model, $scripts;
	
	public function __construct() {	
	
		global $edd_img_wtm_model,$edd_img_wtm_scripts;
		
		$this->model 	= $edd_img_wtm_model;
		$this->scripts 	= $edd_img_wtm_scripts;
		
	}
	
	/**
	 * Code to apply watermark on images
	 *
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	function edd_img_wtm_apply_watermark( $filepath, $type, $source_image_to_use ) {
	
		$save_as_file = $filepath;
		
		global $edd_options;

		if ( is_file( $filepath ) ) { //Check valid path or not

			// Get overlay image size
			$original_image_details = getimagesize( $filepath );
		
			$func_type = preg_replace( '#image/#i', '', $original_image_details['mime'] );
		
			// List of allow image formats
			$acceptable_formats = array( 'jpeg', 'gif', 'png' );
		
			if ( ! in_array( $func_type, $acceptable_formats ) ) {
				return false;
			}
		
			$funcName = 'imagecreatefrom' . $func_type;
			
			ob_start();
			
			$original_image = $funcName( $filepath );
			
			$error = ob_get_clean();

			if ( !$original_image ) {
				return false;
			}
		} else {
			return false;
		}
		
		
		
		// find the location of our PNG image to do the watermarking.
		$watermark_position = isset( $edd_options['edd_img_wtm_'.$type.'_align'] ) ? $edd_options['edd_img_wtm_'.$type.'_align'] : '';

		// Get is watermark repeted on whole image

		$is_watermark_repeated = isset( $edd_options['edd_img_wtm_'.$type.'_repeated_on_image'] ) ? $edd_options['edd_img_wtm_'.$type.'_repeated_on_image'] : '';
        
		if ( $watermark_position || $is_watermark_repeated  ) { // Chck the watermark image position is set or not

			$watermark_image = isset( $edd_options['edd_img_wtm_'.$type.'_img'] ) ? $edd_options['edd_img_wtm_'.$type.'_img'] : '';
			if ( $watermark_image ) { // check that watermark image is set or not.
				// check if this image exists.
				$upload_dir  = wp_upload_dir();
				$watermark_image_path = $upload_dir['basedir'] . $watermark_image;
				
				if ( is_file( $watermark_image_path ) ) { // Check overlay image is set and valid path or not
					
					$overlay = imagecreatefrompng( $watermark_image_path );
					
					if ( $original_image && $overlay ) {
						imagealphablending( $overlay, false );
						imagesavealpha( $overlay, true );
						// where do we place this on the image?
						$original_image_width 	= imagesx( $original_image );
						$original_image_height 	= imagesy( $original_image );
						$watermark_image_width 	= imagesx( $overlay );
						$watermark_image_height = imagesy( $overlay );
						
						if( $watermark_position && $watermark_position != 'none' && !$is_watermark_repeated ) {

							switch ( $watermark_position ) { // Check that overlay position is set or not.
								
								//top left
								case 'tl':
									$watermark_start_x = 0;
									$watermark_start_y = 0;
									break;
									
								//top center
								case 'tc':
									$watermark_start_x = ( $original_image_width/2 ) - ( $watermark_image_width/2 );
									$watermark_start_y = 0;
									break;
									
								//top right
								case 'tr':
									$watermark_start_x = $original_image_width - $watermark_image_width;
									$watermark_start_y = 0;
									break;
									
								// middle left
								case 'ml':
									$watermark_start_x = 0;
									$watermark_start_y = ( $original_image_height/2 ) - ( $watermark_image_height/2 );
									break;
									
								//middle center
								case 'mc':
									$watermark_start_x = ( $original_image_width/2 ) - ( $watermark_image_width/2 );
									$watermark_start_y = ( $original_image_height/2 ) - ( $watermark_image_height/2 );
									break;
									
								// middle right
								case 'mr':
									$watermark_start_x = $original_image_width - $watermark_image_width;
									$watermark_start_y = ( $original_image_height/2 ) - ( $watermark_image_height/2 );
									break;
									
								// bottom left
								case 'bl':
									$watermark_start_x = 0;
									$watermark_start_y = $original_image_height - $watermark_image_height;
									break;
								
								//bottom center
								case 'bc':
									$watermark_start_x = ( $original_image_width/2 ) - ( $watermark_image_width/2 );
									$watermark_start_y = $original_image_height - $watermark_image_height;
									break;
									
								//bottom right
								case 'br':
								default:
									$watermark_start_x = $original_image_width - $watermark_image_width;
									$watermark_start_y = $original_image_height - $watermark_image_height;
									break;
							}
							
							// Copy another image from main image and overlay it.
							imagecopy( $original_image, $overlay, $watermark_start_x, $watermark_start_y, 0, 0, $watermark_image_width, $watermark_image_height );
						}
						elseif ( $is_watermark_repeated ) { // if repeated waterark enabled
						
							// code to apply repeated waterark on whole images
							$img_paste_x = 0;
							
							while( $img_paste_x <  $original_image_width ){
								$img_paste_y = 0;

								while( $img_paste_y < $original_image_height ){
									imagecopy( $original_image, $overlay, $img_paste_x, $img_paste_y, 0, 0, $watermark_image_width, $watermark_image_height );
									$img_paste_y += $watermark_image_height;
								}
								$img_paste_x += $watermark_image_width;
							}
						}

						$funcname_generate = 'image' . $func_type;
						
						if ( $func_type == 'jpeg' ) {
							
							$jpeg_quality = apply_filters( 'edd_img_wtm_jpeg_quality', 100 );
							$jpeg_quality = ( isset($jpeg_quality) && trim($jpeg_quality) != '' ) ? intval( $jpeg_quality ) : 75;
							
							$funcname_generate( $original_image, $save_as_file, $jpeg_quality );
							
						} elseif ( $func_type == 'png' ) {
							
							//Creating the transparent background for png image
							//imagealphablending($original_image, false);
							imagesavealpha($original_image, true);
							$transparent = imagecolorallocatealpha($original_image, 0, 0, 0, 127);
						    imagefill($original_image, 0, 0, $transparent);
						    
						    $png_quality = apply_filters( 'edd_img_wtm_png_quality', 6 );
							$png_quality = ( isset($png_quality) && trim($png_quality) != '' ) ? intval( $png_quality ) : 6;
						    
						    $funcname_generate( $original_image, $save_as_file, $png_quality );
						    
						} else {
							$funcname_generate( $original_image, $save_as_file );
						}
						return true;
	
					}
				}
			}
		}
		return false; // failed somehow.
	}
	
	/**
	 * Apply image watermark to all sized images when uploading a image
	 * 
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	function edd_generate_image_watermark( $data ) {
		
		global $post;
		
		$is_download	= false;
		$post_id		= false;

		/*// Get FES parameter
		$task = !empty( $_GET['task'] ) ? $_GET['task'] : '';
		
		if( $task == '' ) { // Get fes postid from session
			$edd_fes_post_id	= EDD()->session->get( 'fes_dashboard_post_id' );
		} else {
			$edd_fes_post_id	= EDD()->session->get( 'edd_fes_post_id' );
		}*/

		if ( isset( $_REQUEST['post_id'] ) && ( int ) $_REQUEST['post_id'] > 0 ) {
			
			$post_id = ( int ) $_REQUEST['post_id'];
			
		} elseif ( isset( $_REQUEST['id'] ) && ( int ) $_REQUEST['id'] > 0 ) {
			
			$post_id = ( int ) $_REQUEST['id'];
			
		} elseif ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'upload-attachment' ) { //Condition for FES extension to add support of image watermarking

			$http_referrer 	= $_SERVER['HTTP_REFERER'];

			$queries_str 	= parse_url($http_referrer, PHP_URL_QUERY);
			parse_str($queries_str, $queries);

			if( !empty( $queries ) && is_array( $queries ) && array_key_exists( 'task', $queries ) ) {

				if( $queries['task'] == 'edit-product' && array_key_exists( 'post_id', $queries )
					&& !empty( $queries['post_id'] ) ) {

					$post_id = $queries['post_id'];
				} else if ( $queries['task'] == 'new-product' ) {

					$reffer_url = remove_query_arg( 'task', $http_referrer );
					$post_id = url_to_postid( $reffer_url );
				}
			}
		}

		if ( $post_id > 0 ) { // check post id
			
			// check download
			$post = get_post( $post_id );
			$http_referrer 	= $_SERVER['HTTP_REFERER'];

			if ( $post && $post->post_type == 'attachment' && ( int ) $post->post_parent > 0 ) {
				// get the real post that this attachment is for.
				// this happens when we call "Regnerate Thumbs"" and some other times.
				$post = get_post( $post->post_parent );
			}
			if ( $post && $post->post_type == EDD_IMG_WTM_MAIN_POSTTYPE ) { // checked if post_type is download or not?
				$is_download = true;
			}

			// condtion for FES extension to add support of image watermarking
			if(isset($post->post_content) && strpos($http_referrer, admin_url()) === false
				&& (has_shortcode($post->post_content,'fes_submission_form') || has_shortcode($post->post_content,'fes_vendor_dashboard')) )
			{
				$is_download = true;
			}
		
		}
		
		// added a filter to allow watermark for other post types
		$is_download = apply_filters( 'edd_img_wtm_is_download', $is_download );

		if ( !$is_download ) return $data;

	    ob_start();
	    // get settings for watermarking
		$upload_dir  = wp_upload_dir();
		
		// If File type is not image then return
		if( !isset($data['file']) ) {
			return $data;
		}
		
		// path to fully uploaded image is:
		$filepath = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $data['file'];
		
		if ( !is_file( $filepath ) ) return $data; // should never happen, but just to be sure.
	
		// check our settings to see what images we are applying the watermark to.
		// for now the only settings are "Apply to Big Image" and "Apply to Thumbnail" .
		// each can have different watermarks.
		// later on we can have individual watermark settings per download ( in a tab ) .
		
		$backup_file = $this->model->edd_img_wtm_backup_image_file_name( $filepath );
		
		if ( is_file( $backup_file ) ) { 
			copy( $backup_file, $filepath );
			touch( $filepath );
		} else {
			copy( $filepath, $backup_file );
			touch( $backup_file );
		}
		
		$info = getimagesize($filepath);
		$img_size = array(
                'file' 		=> wp_basename( $filepath ),
                'width' 	=> $info[0],
                'height' 	=> $info[1],
                'mime-type' => $info['mime'],
            );
		
        // $data dont contains full image so we need manuall merge it with custom code
        $full = array('full'=>$img_size);
		
        $data['sizes'] = array_merge($data['sizes'],$full);
		
        // end full image merge code
		// Check that watermark position is set or not.
		if ( count( $data['sizes'] ) ) { // Count all data sizes

			foreach ( $data['sizes'] as $sizename => $size_data ) {
				
				// modify this file as well.
				$thumb_filepath = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . dirname( $data['file'] ) . DIRECTORY_SEPARATOR . $size_data['file'];

				if ( function_exists( 'wp_get_image_editor' ) && isset( $size_data['width'] ) && isset( $size_data['height'] ) && $size_data['width'] && $size_data['height'] && is_file( $backup_file ) && $sizename != 'full' ) {

					//image_resize( $filepath, $size_data['width'], $size_data['height'], isset( $size_data['crop'] ) ? $size_data['crop'] : true );
					$image = wp_get_image_editor( $filepath );

					if ( ! is_wp_error( $image ) ) {
					    $image->resize( $size_data['width'], $size_data['height'], true );
					}
				}

				$this->edd_img_wtm_apply_watermark( $thumb_filepath, $sizename, $backup_file );
			}
		}
			
	    $output = ob_get_clean();
		return $data;
	}
	
	/**
	 * Validate Settings
	 *
	 * Handles to validate settings
	 * 
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	function edd_img_wtm_settings_validate( $input ) {
	
		$img_types = edd_img_wtm_get_types();
		
		foreach( $img_types as $img_type ) {
			$input['edd_img_wtm_'.$img_type.'_img']		=  $this->model->edd_img_wtm_escape_slashes_deep( $input['edd_img_wtm_'.$img_type.'_img'] );
			$input['edd_img_wtm_'.$img_type.'_align']	=  $this->model->edd_img_wtm_escape_slashes_deep( $input['edd_img_wtm_'.$img_type.'_align'] );
		}
		
		return $input;
	}
	
	/**
	 * Delete Backup Image
	 *
	 * Handles to delete backup image from
	 * media folder when media is deleted
	 * 
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	function edd_img_wtm_delete_backup_image( $postid ) {
		
		//get attachment data
		$attachment_data = get_post( $postid );
		
		if( isset( $attachment_data->guid ) && !empty( $attachment_data->guid ) ) { // Check file link is not empty
			
			//get upload file path
			$upload_dir = wp_upload_dir();
			$file_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $attachment_data->guid );
			
			//get backup file path
			$backup_file_path = $this->model->edd_img_wtm_backup_image_file_name( $file_path );
			
			 //Check file is exist
			if( !empty( $backup_file_path ) && file_exists( $backup_file_path ) ) {
				
				unlink( $backup_file_path ); //delete file from server
			}
		}
	}
	
	/**
	 * Save Customer Image Watermark Meta Fields
	 * 
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.1.4
	 */
	public function edd_img_wtm_save_customer_meta_fields( $user_id ) {
		
		if ( !current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		 
		$user_image_watermark = isset($_POST['edd_img_wtm_enable']) ? $_POST['edd_img_wtm_enable'] : '';		
		update_user_meta( $user_id, 'edd_img_wtm_enable', $user_image_watermark ); 
		
	}
	
	/**
	 * Add Custom Fields For Image Watermark Per User
	 * 
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.1.4
	 */
	public function edd_img_wtm_add_customer_meta_fields( $user ) { 
		
		if ( !current_user_can( 'edit_user' ) ) {
			return false;
		} 
		
		$edd_img_wtm_enable = get_the_author_meta( 'edd_img_wtm_enable', $user->ID );
		 
		?>
		
		<h3><?php echo __( 'Image Watermark Settings', 'eddimgwtm' ); ?></h3>
		
		<table class="form-table">
			<tr>
				<th><label><?php echo __( 'Disable Image Watermark:', 'eddimgwtm' ); ?></label></th>
				<td>
					<input class="edd_img_wtm_on_off_btn" id="edd_img_wtm_enable" value="1" type="checkbox" name="edd_img_wtm_enable" <?php if( $edd_img_wtm_enable =='1' ) { echo 'checked="checked"'; } ?> />
					<span class="description"><?php echo __( 'Check this box to disable image watermark for images uploaded by this user.', 'eddimgwtm' ); ?></span>	
				</td>
			</tr>
		</table><?php
	}

	/**
	 * Add New menu
	 *
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	public function edd_img_wtm_add_admin_menu() {

		//Get speaker event page slug
		$edd_pageslug	= 'edit.php?post_type=download';

		//Register submenu for regenerate thumbails
		add_submenu_page( $edd_pageslug, __( 'Regenerate Thumb', 'eddimgwtm' ), __( 'Regenerate Thumb', 'eddimgwtm' ), "manage_options", 'edd_img_wtm_regenerate_thumb', array( $this, 'edd_img_wtm_regenerate_thumb_page' ) );
	}

	/**
	 * Includes regenerate thumb functionality
	 * 
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	public function edd_img_wtm_regenerate_thumb_page(){

		include_once( EDD_IMG_WTM_ADMIN.'/forms/edd-img-wtm-regenerate-img.php');
	}

	/**
	 * Ajax process
	 * 
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	public function edd_img_wtm_regenerate_thumb_process() {

		//@error_reporting( 0 ); // Don't break the JSON result
		$result = array();

		header( 'Content-type: application/json' );

		$id = (int) $_REQUEST['id'];
		$image = get_post( $id );

		if ( ! $image || 'attachment' != $image->post_type || 'image/' != substr( $image->post_mime_type, 0, 6 ) ) {
			$result['error'] = sprintf( __( 'Failed resize: %s is an invalid image ID.', 'eddimgwtm'), esc_html( $_REQUEST['id'] ) );
			echo json_encode( $result );
			exit;
		}

		$fullsizepath = get_attached_file( $image->ID );

		if ( false === $fullsizepath || ! file_exists( $fullsizepath ) ){

			$result['error'] = sprintf( __( 'The originally uploaded image file cannot be found at %s', 'eddimgwtm' ), '<code>' . esc_html( $fullsizepath ) . '</code>' );
			echo json_encode( $result );
			exit;
		}

		@set_time_limit( 900 ); // 5 minutes per image should be PLENTY

		$metadata = wp_generate_attachment_metadata( $image->ID, $fullsizepath );

		if ( is_wp_error( $metadata ) ) {

			$result['error'] = $metadata->get_error_message();
			echo json_encode( $result );
			exit;
		}

		if ( empty( $metadata ) ) {

			$result['error'] =  __( 'Unknown failure reason.', 'eddimgwtm' );
			echo json_encode( $result );
			exit;
		}

		// If this fails, then it just means that nothing was changed (old value == new value)
		wp_update_attachment_metadata( $image->ID, $metadata );

		$result['success'] =  sprintf( __( '&quot;%1$s&quot; (ID %2$s) was successfully resized in %3$s seconds.', 'eddimgwtm' ), esc_html( get_the_title( $image->ID ) ), $image->ID, timer_stop() );
		echo json_encode( $result );
		exit;
	}

	/**
	 * Adding Hooks
	 *
	 * @package Easy Digital Downloads - Image Watermark
	 * @since 1.0.0
	 */
	public function add_hooks() {
		
		//add filter to add settings
		add_filter( 'edd_settings_extensions', array( $this->model, 'edd_img_wtm_settings') );
		
		//add filter to section setting 
		add_filter( 'edd_settings_sections_extensions', array( $this->model, 'edd_img_wtm_settings_section' ) );
		
		//add filter to extension settings field
		add_filter( 'edd_settings_extensions-eddimgwtm_sanitize', array( $this, 'edd_img_wtm_settings_validate') );
		
		//delete backup image from media folder when media is deleted
  		add_action( 'delete_attachment', array( $this, 'edd_img_wtm_delete_backup_image' ) );
  		
  		// Add custom fields for image watermark per user
		add_action( 'show_user_profile', array($this, 'edd_img_wtm_add_customer_meta_fields'), 20 );
		add_action( 'edit_user_profile', array($this, 'edd_img_wtm_add_customer_meta_fields'), 20 );
		
		// Save customer image watermark meta fields
		add_action( 'personal_options_update', array($this, 'edd_img_wtm_save_customer_meta_fields') );
		add_action( 'edit_user_profile_update', array($this, 'edd_img_wtm_save_customer_meta_fields') );

		add_action( 'admin_menu', array( $this, 'edd_img_wtm_add_admin_menu' ) );

		add_action( 'wp_ajax_edd_img_wtm_regenerate_thumb_process', array( $this, 'edd_img_wtm_regenerate_thumb_process' ) );
	}
}