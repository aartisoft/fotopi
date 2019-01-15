<?php
/**
 * Scripts
 *
 * @package     EDD\Message\Scripts
 * @since       0.1.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Load admin scripts
 *
 * @since       0.1.0
 * @global      array $edd_settings_page The slug for the EDD settings page
 * @global      string $post_type The type of post that we are editing
 * @return      void
 */
function edd_message_admin_scripts( $hook ) {

	if ( $hook == 'download_page_edd-customers' ||
	     $hook == 'edd-fes_page_fes-vendors'
	) {

		wp_enqueue_style( 'edd_message_admin_css', EDD_MESSAGE_URL . '/assets/css/edd-message-admin.css' );
		wp_enqueue_script( 'edd-admin-scripts' );
	}

	if ( $hook == 'download_page_edd-reports' ) {

		wp_enqueue_style( 'edd_message_logs_css', EDD_MESSAGE_URL . '/assets/css/edd-message-logs.css' );
		wp_enqueue_script( 'edd_message_logs_js', EDD_MESSAGE_URL . '/assets/js/edd-message-logs.js' );

		wp_localize_script( 'edd_message_logs_js', 'EDD_Message_Data_Logs', array(
			'l10n' => array(
				'could_not_delete' => __( 'Could not delete message log.', 'edd-message' ),
			),
		));
	}
}

add_action( 'admin_enqueue_scripts', 'edd_message_admin_scripts', 200 );

/**
 * Load frontend scripts
 *
 * @since       0.1.0
 * @return      void
 */
function edd_message_scripts() {

	$task = ( isset( $_GET['task'] ) ) ? $_GET['task'] : null;

	if ( $task == 'message' ) {
		wp_enqueue_style( 'edd_message_css', EDD_MESSAGE_URL . 'assets/css/edd-message-styles.css' );
		$fes = new FES_Setup();
		$fes->enqueue_scripts( true );
	}
}

add_action( 'wp_enqueue_scripts', 'edd_message_scripts' );