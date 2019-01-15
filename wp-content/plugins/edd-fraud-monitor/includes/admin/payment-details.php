<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds a metabox into the Payment Details of a fraudulent payment
 *
 * @since  1.0
 *
 * @param  integer $payment_id The Payment ID being viewed
 * @return void
 */
function eddfm_order_details( $payment_id ) {
	$maybe_fraud = edd_get_payment_meta( $payment_id, '_edd_maybe_is_fraud', true );

	if ( ! empty( $maybe_fraud ) ) {
		?>
		<div id="edd-payment-fraud" class="postbox">
			<h3 class="hndle">
				<span><?php _e( 'Fraud Monitor', 'edd-fm' ); ?></span>
			</h3>
			<div class="inside edd-clearfix">
				<p>
					<?php
					printf(
						__( 'This payment has been marked as potential fraud. <a href="%s">Click here</a> to remove this flag with no further action.', 'edd-fm' ),
						wp_nonce_url( add_query_arg( array( 'edd_action' => 'clear_fraud_flag' ) ), 'eddfm_fraud_nonce' )
					); ?>
				</p>

				<div class="fraud-reasons">
					<strong><?php _e( 'Reasons', 'edd-fm' ); ?></strong>
					<ul>
						<?php
						$reasons = edd_fm_fraud_reasons_callback( $payment_id );
						$reasons = explode( "\n", $reasons );

						foreach ( $reasons as $reason ) {
							echo '<li>' . $reason . '</li>';
						}
						?>
					</ul>
				</div>

			</div><!-- /.inside -->
			<div class="fraud-actions">
				<a class="complete-valid button" id="accept-as-valid" href="<?php echo wp_nonce_url ( add_query_arg( 'edd_action', 'not_fraud' ), 'eddfm_accept_nonce' ); ?>"><?php _e( 'Accept as Valid', 'edd-fm' ); ?></a>&nbsp;
				<a class="button-secondary" id="confirm-as-fraud" href="#"><?php _e( 'Confirm as Fraud', 'edd-fm' ); ?></a>
				<div id="eddfm-fraud-actions-complete" style="display: none">
					<p class="eddfm-actions">
						<strong><?php _e( 'Mark payment as', 'edd-fm' ); ?>:</strong>
						<select name="eddfm_actions" id="fraud-actions">
							<option value="refunded"><?php _e( 'Refunded', 'edd-fm' ); ?></option>
							<option value="revoked"><?php _e( 'Revoked', 'edd-fm' ); ?></option>
							<option value="delete"><?php _e( 'Deleted', 'edd-fm' ); ?></option>
						</select>
					</p>
					<p class="revoke-delete-warning" style="display: none;">
						<?php _e( 'Revoking or Deleting a payment will still require you to submit any necessary refunds from within your merchant account', 'edd-fm' ); ?>
					</p>
					<p class="refund-warning" style="display: none;">
						<?php _e( 'If the gateway used supports refunds, it will be submitted, otherwise you may still need to refund this payment via your merchant account', 'edd-fm' ); ?>
					</p>
					<input type="hidden" name="eddfm_action" value="is_fraud" />
					<input type="hidden" name="eddfm_payment_id" value="<?php echo $payment_id; ?>" />
					<?php wp_nonce_field( 'eddfm-is-fraud', 'eddfm_fraud_nonce', false ); ?>
					<a href="#" id="eddfm-confirm" class="complete-fraud button"><?php _e( 'Confirm', 'edd-fm' ); ?></a>
					<a href="#" class="button-secondary" id="eddfm-cancel-is-fraud"><?php _e( 'Cancel', 'edd-fm' ); ?></a>
					<span class="spinner" style="display:none;"></span>
					<p class="eddfm-message" style="display:none;"></p>
				</div>
			</div>
		</div><!-- /#edd-customer-details -->
		<?php
	}
}
add_action( 'edd_view_order_details_sidebar_before', 'eddfm_order_details', 10, 1 );

/**
 * Add the metabox to non-fraud payments so they can be marked as fraud manually
 *
 * @since 1.1
 * @param $payment_id
 */
function eddfm_mark_as_fraud_metabox( $payment_id ) {
	$payment         = edd_get_payment( $payment_id );
	$pending_review  = $payment->get_meta( '_edd_maybe_is_fraud' );
	$confirmed_fraud = $payment->get_meta( '_edd_confirmed_as_fraud' );

	// If the item is already pending review or is confirmed as fraud, don't show this box.
	if ( ! empty( $pending_review ) || ! empty( $confirmed_fraud ) ) {
		return;
	}
	?>
	<div id="edd-payment-mark-as-fraud" class="postbox">
		<h3 class="hndle"><span><?php _e( 'Fraud Monitor', 'edd-fm' ); ?></span></h3>
		<div class="inside">

			<p>
				<select name="edd-fm-action" id="edd-fm-action">
					<option value="review"><?php _e( 'Flag for Review', 'edd-fm' ); ?></option>
					<option value="fraud"><?php _e( 'Mark as Fraud', 'edd-fm' ); ?></option>
				</select>
			</p>

			<p style="display:none;" class="edd-fm-toggle">
				<label><?php _e( 'Mark payment as', 'edd-fm' ); ?></label>:
				<input type="radio" name="edd-fm-status" value="refunded" id="edd-fm-status-refund" /><label for="edd-fm-status-refund" ><?php _e( 'Refunded', 'edd-fm' ); ?></label>
				<input type="radio" name="edd-fm-status" value="revoked" id="edd-fm-status-revoke" /><label for="edd-fm-status-revoke" ><?php _e( 'Revoked', 'edd-fm' ); ?></label>
			</p>

			<p>
				<label for="edd-payment-note"><?php _e( 'Reason for fraud status', 'edd-fm' ); ?>&nbsp;(<?php _e( 'optional', 'edd-fm' ); ?>):</label>
				<textarea name="edd-payment-fraud-reason" id="edd-payment-fraud-reason" class="large-text"></textarea>
			</p>

			<p>
				<button id="edd-fm-flag-payment" class="button button-secondary right" data-payment-id="<?php echo absint( $payment_id ); ?>"><?php _e( 'Flag Payment', 'easy-digital-downloads' ); ?></button>
				<?php wp_nonce_field( 'edd-fm-flag-payment', 'edd-fm-flag-payment' ); ?>
				<span class="spinner" style="display:none;"></span><br />
				<span class="eddfm-message" style="display:none;"></span>
			</p>

			<div class="clear"></div>
		</div><!-- /.inside -->
	</div><!-- /#edd-payment-notes -->
	<?php
}
add_action( 'edd_view_order_details_main_after', 'eddfm_mark_as_fraud_metabox', 10, 1 );