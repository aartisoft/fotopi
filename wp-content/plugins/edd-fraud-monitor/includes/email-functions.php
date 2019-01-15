<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Email tag callback for {fraud_reasons}
 *
 * @since  1.0
 *
 * @param  integer $payment_id The Payment ID to retrieve the reasons for and replace the tag
 * @return string              The HTML of the reasons
 */
function edd_fm_fraud_reasons_callback( $payment_id ) {
	$reasons = edd_get_payment_meta( $payment_id, '_edd_maybe_is_fraud_reason', true );

	return html_entity_decode( $reasons, ENT_COMPAT, 'UTF-8' );
}

/**
 * Email tag callback for {fraud_link}
 *
 * @since  1.0
 *
 * @param  integer $payment_id The Payment ID to get the link for
 * @return string              The HTML for the link
 */
function edd_fm_fraud_link_callback( $payment_id ) {
	$url = admin_url( esc_url( add_query_arg( array( 'id' => $payment_id ), 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details' ) ) );

	return '<a href="' . $url . '">' . $url . '</a>';
}

