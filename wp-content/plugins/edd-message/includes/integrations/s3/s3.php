<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds Amazon S3 tabs to media uploader if on EDD Message pages
 *
 * @param $tabs
 * @since 1.1
 * @return mixed
 */
function edd_message_add_s3_media_tabs( $tabs ) {

	$view = ( isset( $_GET['view'] ) ) ? $_GET['view'] : null;

	if ( $view == 'messages' || $view == 'message' ) {

		$tabs['s3']         = __( 'Upload to Amazon S3', 'edd_s3' );
		$tabs['s3_library'] = __( 'Amazon S3 Library', 'edd_s3' );
	}

	return $tabs;
}
add_filter( 'media_upload_tabs', 'edd_message_add_s3_media_tabs', 11 );

/**
 * Checks to see whether an attachment is an S3 file and if so, copies it locally and attaches it
 *
 * @param $attachments
 * @param $files
 * @since 1.1
 * @return mixed
 */
function edd_message_s3_attachments( $attachments, $files ) {
	global $edd_s3;

	foreach ( $files as $key => $file ) {
		if ( $edd_s3->is_s3_file( $file['file'] ) ) {

			if ( ( $temp_dir = edd_message_s3_temp_directory() ) === false ) {

				edd_set_error( 'empty-customer-message', __( 'Could not create temporary directory for attachment.', 'edd-message' ) );
				return false;
			}

			$new_file = $temp_dir . $file['attachment_id'];

			$s3_file = file_get_contents( $edd_s3->get_s3_url( $file['file'] ) );

			if ( file_put_contents( $new_file, $s3_file ) === FALSE ) {

				edd_set_error( 'empty-customer-message', __( 'The attachment from Amazon S3 could be not saved.', 'edd-message' ) );
				return false;
			}

			$attachments[$key] = $new_file;

		}
	}
	return $attachments;
}
add_filter( 'edd_message_attachments', 'edd_message_s3_attachments', 10, 2 );

/**
 * Retrieves the temp directory, creating it if it does not exist.
 *
 * @since 1.1
 */
function edd_message_s3_temp_directory() {

	/**
	 * Allow modifying of the temp directory.
	 *
	 * @since 1.1
	 */
	$temp_dir = apply_filters( 'edd_message_s3_temp_dir', EDD_MESSAGE_DIR . 'temp/' );

	// Make sure directory exists first
	if ( ! is_dir( EDD_MESSAGE_DIR . 'temp/' ) ) {

		if ( mkdir( EDD_MESSAGE_DIR . 'temp/' ) === false ) {

			return false;
		}
	}

	return $temp_dir;
}

/**
 * Deletes S3 files from temp directory
 *
 * @param $to
 * @param $subject
 * @param $message
 * @param $attachments
 * @since 1.1
 */
function edd_message_s3_delete_files( $to, $subject, $message, $attachments ) {

	if ( ! empty( $attachments ) ) {
		foreach ( $attachments as $attachment ) {
			if ( stripos( $attachment, 'edd-message/temp' ) ) {
				unlink( $attachment );
			}
		}
	}

}
add_action( 'edd_message_after', 'edd_message_s3_delete_files', 10, 4 );