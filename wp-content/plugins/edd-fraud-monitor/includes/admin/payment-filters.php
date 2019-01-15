<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Add the 'Fraud Status' column to the payment and customer list tables
 *
 * @since 1.1
 * @param $columns
 *
 * @return array
 */
function edd_fm_payment_list_table_column( $columns ) {
	$columns['fraud-status'] = __( 'Fraud Status', 'edd-fm' );

	return $columns;
}
add_filter( 'edd_payments_table_columns', 'edd_fm_payment_list_table_column', 10, 1 );
add_filter( 'edd_report_customer_columns', 'edd_fm_payment_list_table_column', 10, 1) ;

/**
 * Add any fraud labels to the row in the payment list table
 *
 * @since 1.1
 * @param $value
 * @param $payment_id
 * @param $column_name
 *
 * @return string
 */
function edd_fm_payment_status_fraud_label( $value, $payment_id, $column_name ) {

	if ( $column_name !== 'fraud-status' ) {
		return $value;
	}

	$payment            = edd_get_payment( $payment_id );
	$maybe_is_fraud     = $payment->get_meta( '_edd_maybe_is_fraud' );
	$confirmed_as_fraud = $payment->get_meta( '_edd_confirmed_as_fraud' );
	$approved_not_fraud = $payment->get_meta( '_edd_not_fraud' );

	if ( $maybe_is_fraud ) {

		$value = '<a href="' . admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $payment_id ) . '"><span class="edd-fm status review">' . __( 'Review', 'edd-fm' ) . '</span></a>';

	} elseif ( $confirmed_as_fraud ) {

		$value = '<span class="edd-fm status fraud">' . __( 'Fraud', 'edd-fm' ) . '</span>';

	} elseif ( $approved_not_fraud ) {

		$value = '<span class="edd-fm status approved">' . __( 'Approved', 'edd-fm' ) . '</span>';

	}

	return $value;

}
add_filter( 'edd_payments_table_column', 'edd_fm_payment_status_fraud_label', 10, 3 );

/**
 * Add the fraud status label to the customer list table
 *
 * @since 1.1
 * @param $value
 * @param $customer_id
 *
 * @return string
 */
function edd_fm_customer_status_fraud_label( $value, $customer_id ) {

	$args = array(
		'customer' => $customer_id,
		'meta_key' => '_edd_confirmed_as_fraud',
	);
	$fraud_payments = edd_get_payments( $args );

	$args = array(
		'customer' => $customer_id,
		'meta_key' => '_edd_maybe_is_fraud',
	);
	$review_payments = edd_get_payments( $args );

	if ( $review_payments ) {

		$value = '<span class="edd-fm status review">' . __( 'Review', 'edd-fm' ) . '</span>';

	} elseif ( $fraud_payments ) {

		$value = '<span class="edd-fm status fraud">' . __( 'Fraud', 'edd-fm' ) . '</span>';

	}

	return $value;

}
add_filter( 'edd_customers_column_fraud-status', 'edd_fm_customer_status_fraud_label', 10, 3 );

/**
 * Show the Approved and Fraud labels on the view order details screen
 *
 * @since 1.1
 * @param $payment_id
 */
function edd_fm_view_order_details_label( $payment_id ) {

	$payment            = edd_get_payment( $payment_id );
	$confirmed_as_fraud = $payment->get_meta( '_edd_confirmed_as_fraud' );
	$approved_not_fraud = $payment->get_meta( '_edd_not_fraud' );

	if ( empty( $confirmed_as_fraud ) && empty( $approved_not_fraud ) ) {
		return;
	}

	if ( $confirmed_as_fraud ) {

		$label = __( 'fraud', 'edd-fm' );

	} elseif ( $approved_not_fraud ) {
		$label = __( 'approved', 'edd-fm' );

	}


	?>
	<div class="edd-order-fraud-status edd-admin-box-inside">
		<p>
			<span class="label"><?php _e( 'Fraud Status:', 'edd-fm' ); ?></span>&nbsp;
			<span class="edd-fm status <?php echo $label; ?>"><?php echo ucfirst( $label ) ?></span>
		</p>
	</div>
	<?php

}
add_action( 'edd_view_order_details_payment_meta_before', 'edd_fm_view_order_details_label', 10, 1 );

/**
 * Show the fraud status of the customer under their avatar on the customer card
 *
 * @since 1.1
 * @param $customer
 */
function edd_fm_customer_card( $customer ) {
	$args = array(
		'customer' => $customer->id,
		'meta_key' => '_edd_confirmed_as_fraud',
	);
	$fraud_payments = edd_get_payments( $args );

	$args = array(
		'customer' => $customer->id,
		'meta_key' => '_edd_maybe_is_fraud',
	);
	$review_payments = edd_get_payments( $args );

	if ( ! empty( $fraud_payments ) ) {
		?><span class="edd-fm status fraud"><?php _e( 'Fraud', 'edd-fm' ); ?></span><?php
	}

	if ( ! empty( $review_payments ) ) {
		?><span class="edd-fm status review"><?php _e( 'Review', 'edd-fm' ); ?></span><?php
	}
}
add_action( 'edd_after_customer_edit_link', 'edd_fm_customer_card', 10, 1 );
