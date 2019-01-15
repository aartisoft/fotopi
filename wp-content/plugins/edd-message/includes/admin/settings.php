<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implements the EDD 2.5 settings section
 *
 * @param $sections
 *
 * @return mixed
 * @since 1.1
 */
function edd_message_settings_section( $sections ) {
	$sections['edd-message-settings'] = __( 'EDD Message', 'edd-message' );

	return $sections;
}

/**
 * Add settings
 *
 * @access      public
 * @since       1.1
 *
 * @param       array $settings The existing EDD settings array
 *
 * @return      array The modified EDD settings array
 */
function edd_message_settings( $settings ) {
	$new_settings = array(
		array(
			'id'   => 'edd_message',
			'name' => '<strong>' . __( 'Message Settings', 'edd-message' ) . '</strong>',
			'type' => 'header',
		),
		array(
			'id'   => 'edd-message-logging',
			'name' => __( 'Logging', 'edd-message' ),
			'desc' => __( 'Select the types of messages you wish to save a record of.', 'edd-message' ),
			'type' => 'multicheck',
			/**
			 * Array of log types. Allows integrations to add new ones.
			 */
			'options' => apply_filters( 'edd_message_logging_options', array(
					'admin' =>  __( 'Administrator direct to customers', 'edd-message' ),
					'purchase_receipt' =>  __( 'Purchase receipts', 'edd-message' ),
				)
			)
		),
	);

	// If EDD is at version 2.5 or later...
	if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
		// Use the previously noted array key as an array key again and next your settings
		$new_settings = array( 'edd-message-settings' => $new_settings );
	}

	$new_settings = apply_filters( 'edd_message_settings', $new_settings );

	return array_merge( $settings, $new_settings );
}