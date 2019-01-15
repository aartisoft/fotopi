<?php

/**
 * Identify if the recommendation sessoin is set, and if so, remove it so we can set it later if need be.
 *
 * @since  1.2.6
 * @return void
 */
function edd_rp_handle_session_data() {
	$session_value = EDD()->session->get( 'edd_has_recommendations' );
	if ( ! empty( $session_value ) ) {
		EDD()->session->set( 'edd_has_recommendations', NULL );
	}
}
add_action( 'template_redirect', 'edd_rp_handle_session_data' );

/**
 * Store extra meta information against the download at the time it is added to the cart
 * if it's a recommendation
 *
 * @since  1.2.6
 * @param $info the default array of meta information stored with the download
 * @return $info the new array of meta information
 *
*/
function edd_rp_add_to_cart_item( $info ) {

	$recommendation_page = EDD()->session->get( 'edd_has_recommendations' );

	if ( is_numeric( $recommendation_page ) && $recommendation_page != $info['id'] ) {
		$info['recommendation_source'] = $recommendation_page;
	}

	return $info;
}
add_filter( 'edd_add_to_cart_item', 'edd_rp_add_to_cart_item', 10, 1 );

/**
 * Register the recommendation_sale log type
 *
 * @since  1.2.6
 * @param  array $log_types Log types
 * @return array            Log types
 */
function edd_rp_register_log_type( $log_types ) {
	$log_types[] = 'recommendation_sale';

	return $log_types;
}
add_filter( 'edd_log_types', 'edd_rp_register_log_type', 10, 1 );

/**
 * Maybe log a recommendation sale
 * Iterates through items in the payment and if one is a recommendation logs it
 *
 * @since  1.2.6
 * @param  int $payment_id The Payment ID being completed
 * @return void
 */
function edd_rp_log_recommendation_sale( $payment_id ) {
	$payment_items = edd_get_payment_meta_cart_details( $payment_id, true );

	foreach ( $payment_items as $item ) {

		if ( ! empty( $item['item_number']['recommendation_source'] ) ) {
			$edd_log = new EDD_Logging();

			$log_data = array(
				'post_parent' => $item['item_number']['recommendation_source'],
				'post_date'   => edd_get_payment_completed_date( $payment_id ),
				'log_type'    => 'recommendation_sale'
			);

			$log_meta = array(
				'payment_id'  => $payment_id,
				'download_id' => $item['id'],
				'price'       => $item['price'],
				'quantity'    => $item['quantity'],
				'item_price'  => $item['item_price'],
			);

			$log_entry = $edd_log->insert_log( $log_data, $log_meta );

		}
	}

}
add_action( 'edd_complete_purchase', 'edd_rp_log_recommendation_sale', 10, 1 );


