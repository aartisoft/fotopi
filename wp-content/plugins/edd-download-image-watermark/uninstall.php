<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Uninstall
 *
 * Does delete the created tables and all the plugin options
 * when uninstalling the plugin
 *
 * @package Easy Digital Downloads - Image Watermark
 * @since 1.0.0
 */

// check if the plugin really gets uninstalled 
if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
	exit();

global $edd_options;

// check remove data on uninstall is checked, if yes then delete plugin data
if( edd_get_option( 'uninstall_on_delete' ) ) {
	
	// get all image types
	$img_types = edd_img_wtm_get_types();
	foreach ( $img_types as $img_type ) {
		
		//unset watermark image
		unset( $edd_options['edd_img_wtm_'.$img_type.'_img'] );
		
		//unset watermark image alignment not
		unset( $edd_options['edd_img_wtm_'.$img_type.'_align'] );
	}
	
	// update edd_settings option
	update_option( 'edd_settings', $edd_options );
}
?>