<?php

/**
 * Remove downloads that are hidden per the EDD Hide Download
 *
 * @since  1.2.9
 * @param  bool $hide_download If this download should be hidden
 * @param  int  $download_id   The Download ID to check
 * @param  int  $user_id       The User Id being shown the recommendations
 * @return bool                If the download should be hidden
 */
function edd_rp_remove_hidden_download( $hide_download, $download_id, $user_id ) {

	$is_hidden = get_post_meta( $download_id, '_edd_hide_download', true );

	if ( ! empty( $is_hidden ) ) {
		$hide_download = true;
	}

	return $hide_download;

}
add_filter( 'edd_rp_hide_product_suggestion', 'edd_rp_remove_hidden_download', 10, 3 );