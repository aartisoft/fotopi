<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds FES related emails to the EDD Message settings for logging
 *
 * @param $options
 * @since 1.1
 * @return mixed
 */
function edd_message_add_fes_logging( $options ) {
	$options['vendor_customer'] = __( 'Vendor messages to customers', 'edd-message' );
	$options['customer_vendor'] = __( 'Customer messages to vendors', 'edd-message' );
	return $options;
}
add_filter( 'edd_message_logging_options', 'edd_message_add_fes_logging' );

/**
 * Log messages sent by customers to vendors through vendor contact form
 *
 * @param $from
 * @param $save_id
 * @param $values
 * @param $user_id
 * @since 1.1
 * @return mixed
 */
function edd_message_log_vendor_contact_form( $from, $save_id, $values, $user_id ) {

	$log_type = 'customer_vendor';
	if ( edd_message_maybe_log( $log_type ) ) {
		$vendor = new FES_DB_Vendors();
		$vendor = $vendor->get_vendor_by( 'id', $save_id );
		$args = array(
			'type' => $log_type,
			'message' => $values['message'],
			'subject' => __( 'Message from ', 'edd-message' ) . $values['name'],
			'post_author' => $user_id,
			'customer_id' => $user_id,
			'to' => array( $vendor->email ),
			'from_name' => $values['name'],
			'from_email' => $values['email'],
		);
		edd_message_log( $args );
	}

	return $from;
}
add_filter( 'fes_vendor_contact_form_message_opener', 'edd_message_log_vendor_contact_form', 10, 4 );

/**
 * Log messages sent by vendors to customers from frontend dashboard
 *
 * @param $args
 * @since 1.1
 */
function edd_message_log_vendor_messages( $args ) {

	$log_type = 'vendor_customer';
	if ( edd_message_maybe_log( $log_type ) ) {

		$to = $args['to'];
		$customer = new EDD_Customer( $to, false );

		$log_args = array(
			'type' => $log_type,
			'message' => $args['message'],
			'subject' => $args['subject'],
			'post_author' => $args['vendor'],
			'customer_id' => $customer->id,
			'to' => array( $to ),
		);
		if ( isset( $args['from-name'] ) ) {
			$log_args['from_name'] = $args['from-name'];
		}
		if ( isset( $args['from-email'] ) ) {
			$log_args['from_email'] = $args['from-email'];
		}
		if ( isset( $args['reply'] ) ) {
			$log_args['reply_to'] = $args['reply'];
		}
		if ( isset( $args['cc'] ) ) {
			$log_args['cc'] = $args['cc'];
		}
		if ( isset( $args['bcc'] ) ) {
			$log_args['bcc'] = $args['bcc'];
		}
		edd_message_log( $log_args );
	}
	return;
}