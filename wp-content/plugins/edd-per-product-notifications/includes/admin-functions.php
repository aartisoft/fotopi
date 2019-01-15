<?php
/**
 * Admin Functions
 *
 * @package     Per Product Notifications for Easy Digital Downloads
 * @subpackage  Admin Logic
 * @copyright   Copyright (c) 2013, Markus Drubba (dev@markusdrubba.de)
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

/**
 * Sends the PPN Sale Notification Emails
 *
 * @param $payment_id   Payment ID
 * @param $payment_data Payment Meta + Data
 *
 * @return void
 */
function drubba_ppn_edd_admin_sale_notice( $payment_id, $payment_data ) {
	global $edd_options;

	if ( isset( $edd_options['disable_admin_notices'] ) )
		return;

	$downloads = maybe_unserialize( $payment_data['downloads'] );

	$all_recipients = array();

	if ( is_array( $downloads ) ) {
		foreach ( $downloads as $download ) {
			$id                          = isset( $payment_data['cart_details'] ) ? $download['id'] : $download;
			$email                       = get_post_meta( $id, '_drubba_ppn_notification_emails', true );
			$crowd_notification_disabled = get_post_meta( $id, '_drubba_ppn_disable_crowdfunding_author_notification', true );

			if ( $email ) {
				$email = array_map( 'trim', explode( "\n", $email ) );
			}

			// add crowdfunding author email
			if ( ! $crowd_notification_disabled ) {
				if ( function_exists( 'atcf_get_campaign' ) && isset( $edd_options['drubba_ppn_send_crowdfunding_notices'] ) && ! $crowd_notification_disabled ) :
					$campaign = atcf_get_campaign( get_post( $id ) );
					$email[]  = $campaign->contact_email();
				endif;
			}

			// eliminate duplicate emails
			$all_recipients = array_merge( (array) $all_recipients, (array) $email );
			$all_recipients = array_unique( (array) $all_recipients );
		}

	}

	add_filter( 'edd_admin_notice_emails', function ( $emails ) use ( $all_recipients ) {
		if ( $all_recipients && $emails )
			$emails = array_merge( (array) $emails, (array) $all_recipients );

		return $emails;
	} );

	edd_admin_email_notice( $payment_id, $payment_data );
}

remove_action( 'edd_admin_sale_notice', 'edd_admin_email_notice', 10 );
add_action( 'edd_admin_sale_notice', 'drubba_ppn_edd_admin_sale_notice', 10, 2 );

/**
 * Send notifications for preapproved wepay campaign payments
 *
 * @param $payment_id
 * @param $new_status
 * @param $old_status
 *
 * @since 1.2.2
 */
function drubba_ppn_send_preapproved_sale_notice( $payment_id, $new_status, $old_status ) {
	if ( $new_status == 'preapproved' ) {
		$payment_data = edd_get_payment_meta( $payment_id );
		drubba_ppn_edd_admin_sale_notice( $payment_id, $payment_data );
	}
}

add_action( 'edd_update_payment_status', 'drubba_ppn_send_preapproved_sale_notice', 10, 3 );