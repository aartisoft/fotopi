<?php
/**
 * Helper Functions
 *
 * @package     EDD\Message\Functions
 * @since       0.1.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Gets message fields and filters them.
 *
 * @since 1.1
 * @return array
 * @param array $data Possible defaults.
 */
function edd_message_get_message_field_values( $data = array() ) {

	$available_fields = array(
		'emails',
		'from-name',
		'from-email',
		'reply',
		'cc',
		'bcc',
		'subject',
		'message',
	);

	$fields = array_fill_keys( $available_fields, '' );

	foreach ( $available_fields as $field ) {

		if ( isset( $data["edd-message-$field"] ) ) {

			$fields[ $field ] = $data["edd-message-$field"];
		}
	}

	$fields['message'] = stripslashes( $fields['message'] );

	/**
	 * Filters the fields before being used in a message.
	 *
	 * @since 1.1
	 */
	$fields = apply_filters( 'edd_message_fields', $fields );

	return $fields;
}

/**
 * Outputs the message from a log.
 *
 * @since 1.1
 * @param mixed $log Log ID or post object.
 */
function edd_message_show_message( $log ) {

	$log_ID      = $log->ID;
	$to          = get_post_meta( $log_ID, '_edd_log_to' );
	$from        = get_post_meta( $log_ID, '_edd_log_from', true );
	$reply       = get_post_meta( $log_ID, '_edd_log_reply_to', true );
	$cc          = get_post_meta( $log_ID, '_edd_log_cc', true );
	$bcc         = get_post_meta( $log_ID, '_edd_log_bcc', true );
	$attachments = get_post_meta( $log_ID, '_edd_log_attachments' );

	if ( ! $log instanceof WP_Post ) {

		$log = get_post( $log );
	}

	if ( ! $log || is_wp_error( $log ) ) {

		return;
	}

	$log_ID      = $log->ID;
	$to          = get_post_meta( $log_ID, '_edd_log_to' );
	$from        = get_post_meta( $log_ID, '_edd_log_from', true );
	$reply       = get_post_meta( $log_ID, '_edd_log_reply_to', true );
	$cc          = get_post_meta( $log_ID, '_edd_log_cc', true );
	$bcc         = get_post_meta( $log_ID, '_edd_log_bcc', true );
	$attachments = get_post_meta( $log_ID, '_edd_log_attachments' );

	if ( ! empty( $to ) ) : ?>
		<div class="edd-message-log-to">
			<strong><?php _e( 'To:', 'edd-message' ); ?></strong>

			<ul>
				<?php foreach ( $to[0] as $recipient ) : ?>
					<li><?php echo esc_attr( $recipient ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $from ) ) : ?>
		<div class="edd-message-log-from">
			<strong><?php _e( 'From:', 'edd-message' ); ?></strong>
			<?php echo esc_attr( $from ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $reply ) ) : ?>
		<div class="edd-message-log-reply">
			<strong><?php _e( 'Reply to:', 'edd-message' ); ?></strong>
			<?php echo esc_attr( $reply ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $cc ) ) : ?>
		<div class="edd-message-log-cc">
			<strong><?php _e( 'CC:', 'edd-message' ); ?></strong>
			<?php echo esc_attr( $cc ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $bcc ) ) : ?>
		<div class="edd-message-log-bcc">
			<strong><?php _e( 'BCC:', 'edd-message' ); ?></strong>
			<?php echo esc_attr( $bcc ); ?>
		</div>
	<?php endif; ?>

	<div class="edd-message-log-body">
		<strong><?php _e( 'Message body:', 'edd-message' ); ?></strong>

		<?php echo apply_filters( 'the_content', $log->post_content ); ?>
	</div>

	<?php if ( ! empty( $attachments ) ) : ?>
		<div class="edd-message-log-attachments">
			<strong><?php _e( 'Attachments:', 'edd-message' ); ?></strong>
			<ul>
				<?php foreach ( $attachments[0] as $attachment ) : ?>
					<li><?php echo basename( $attachment ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif;
}

/**
 * Send a customer message
 *
 * @since  0.1.0
 * @param  array $args The $_POST array being passed
 * @return int         The Message ID that was saved, or 0 if nothing was saved
 */
function edd_message_send_message( $args ) {

	if ( empty( $args ) ) {
		return;
	}

	// The select field seems to only want to pass indexes so...
	$addresses = explode( ',', $args['edd-message-emails'] );
	$selected  = $args['edd-message-selected-emails'];
	// Let's get all the addresses and remove the ones that weren't selected
	foreach ( $addresses as $index => $address ) {
		if ( ! in_array( $index, $selected ) ) {
			unset( $addresses[ $index ] );
		}
	}
	$to          = $addresses;
	$message     = stripslashes( do_shortcode( $args['edd-message-message'] ) );
	$subject     = trim( sanitize_text_field( $args['edd-message-subject'] ) );
	$from_name   = trim( sanitize_text_field( $args['edd-message-from-name'] ) );
	$from_email  = sanitize_email( $args['edd-message-from-email'] );
	$reply_to    = sanitize_email( $args['edd-message-reply'] );
	$cc          = trim( sanitize_text_field( $args['edd-message-cc'] ) );
	$bcc         = trim( sanitize_text_field( $args['edd-message-bcc'] ) );
	$customer_id = trim( sanitize_text_field( $args['edd-message-customer-id'] ) );

	if ( ! empty( $args['edd_download_files'][1]['file'] ) ) {

		$attachments = wp_list_pluck( $args['edd_download_files'], 'attachment_id' );
		$attachments = array_map( 'get_attached_file', $attachments );
		/**
		 * Allows attachments to be modified before sending. S3 integration is an example.
		 *
		 * @since 1.1
		 */
		$attachments = apply_filters( 'edd_message_attachments', $attachments, $args['edd_download_files'] );
	} else {
		$attachments = null;
	}

	$nonce = $args['add_customer_message_nonce'];

	if ( ! wp_verify_nonce( $nonce, 'add-customer-message' ) ) {
		wp_die( __( 'Cheatin\' eh?!', 'edd-message' ) );
	}

	if ( empty( $message ) ) {
		edd_set_error( 'empty-customer-message', __( 'A message is required', 'edd-message' ) );
	}

	if ( edd_get_errors() ) {
		return;
	}

	// Send the message
	$email = new EDD_Emails();
	$email->send( $to, $subject, $message, $attachments );

	if ( edd_message_maybe_log( 'admin' ) ) {
		// Log the message in edd_logs
		$log_args = array(
			'message'     => $message,
			'subject'     => $subject,
			'to'          => $to,
			'customer_id' => $customer_id,
			'type'        => 'admin',
		);
		if ( ! empty( $from_email ) ) {
			$log_args['from'] = $from_name . ' <' . $from_email . '>';
		}
		if ( ! empty( $reply_to ) ) {
			$log_args['reply_to'] = $reply_to;
		}
		if ( ! empty( $cc ) ) {
			$log_args['cc'] = $cc;
		}
		if ( ! empty( $bcc ) ) {
			$log_args['bcc'] = $bcc;
		}
		if ( $attachments !== null ) {
			$log_args['attachments'] = $attachments;
		}

		edd_message_log( $log_args );
	}

	do_action( 'edd_message_after', $to, $subject, $message, $attachments );

	return false;

}

add_action( 'edd_add-customer-message', 'edd_message_send_message', 10, 1 );

/**
 * Define "From" name on email
 *
 * @param $name
 * @since 1.0
 * @return string|void
 */
function edd_message_from_name( $name ) {

	if ( empty( $_POST['edd-message-from-name'] ) ) {
		return $name;
	}

	$from = trim( sanitize_text_field( $_POST['edd-message-from-name'] ) );

	return $from;
}

add_filter( 'edd_email_from_name', 'edd_message_from_name' );

/**
 * Define "From" email address on message
 *
 * @param $email
 * @since 1.0
 * @return string|void
 */
function edd_message_from_email( $email ) {

	if ( empty( $_POST['edd-message-from-email'] ) || ! is_email( $_POST['edd-message-from-email'] ) ) {
		return $email;
	}

	$email = $_POST['edd-message-from-email'];

	return $email;
}

add_filter( 'edd_email_from_address', 'edd_message_from_email' );

/**
 * Set reply to header on email
 *
 * @param $headers
 * @since 1.0
 * @return string|void
 */
function edd_message_reply_to( $headers ) {

	if ( empty( $_POST['edd-message-reply'] ) || ! is_email( $_POST['edd-message-from-email'] ) ) {
		return $headers;
	}

	$reply_to = trim( sanitize_text_field( $_POST['edd-message-reply'] ) );
	$headers  = $headers . "Reply-To: $reply_to\r\n";

	return $headers;
}

add_filter( 'edd_email_headers', 'edd_message_reply_to' );

/**
 * Add CC to message
 *
 * @param $headers
 * @since 1.0
 * @return string|void
 */
function edd_message_cc( $headers ) {

	if ( empty( $_POST['edd-message-cc'] ) ) {
		return $headers;
	}

	$cc      = trim( sanitize_text_field( $_POST['edd-message-cc'] ) );
	$headers = $headers . "Cc: $cc\r\n";

	return $headers;
}

add_filter( 'edd_email_headers', 'edd_message_cc' );

/**
 * Add BCC to message
 *
 * @param $headers
 * @since 1.0
 * @return string|void
 */
function edd_message_bcc( $headers ) {

	if ( empty( $_POST['edd-message-bcc'] ) ) {
		return $headers;
	}

	$bcc     = trim( sanitize_text_field( $_POST['edd-message-bcc'] ) );
	$headers = $headers . "Bcc: $bcc\r\n";

	return $headers;
}

add_filter( 'edd_email_headers', 'edd_message_bcc' );