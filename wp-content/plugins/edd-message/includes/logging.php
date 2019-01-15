<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determines whether a message type should be logged
 *
 * @param $log
 *
 * @since 1.1
 * @return bool
 */
function edd_message_maybe_log( $log ) {
	global $edd_options;

	$output = ( isset( $edd_options['edd-message-logging'][ $log ] ) ) ? true : false;

	return $output;
}

/**
 * Records the log if the message type is set to log in the settings
 *
 * @param $args
 *
 * @since 1.1
 */
function edd_message_log( $args ) {

	$log_data = array(
		'log_type'     => 'email',
		'post_content' => $args['message'],
		'post_title'   => $args['subject'],
	);
	$log_meta = array(
		'to'           => $args['to'],
		'message_type' => $args['type'],
	);
	if ( ! empty( $args['customer_id'] ) ) {
		$log_meta['customer_id'] = $args['customer_id'];
	}
	if ( ! empty( $args['from_email'] ) ) {
		$log_meta['from'] = $args['from_name'] . ' <' . $args['from_email'] . '>';
		if ( ! isset( $args['post_author'] ) ) {
			$user                    = get_user_by( 'email', $args['from_email'] );
			$log_data['post_author'] = $user->ID;
		}
	}
	if ( ! empty( $args['reply_to'] ) ) {
		$log_meta['reply_to'] = $args['reply_to'];
	}
	if ( ! empty( $args['cc'] ) ) {
		$log_meta['cc'] = $args['cc'];
	}
	if ( ! empty( $args['bcc'] ) ) {
		$log_meta['bcc'] = $args['bcc'];
	}
	if ( $args['attachments'] !== null ) {
		$log_meta['attachments'] = $args['attachments'];
	}

	$log = new EDD_Logging();
	$log->insert_log( $log_data, $log_meta );
}

/**
 * Sets up the email logs view output
 */
function edd_message_email_logs_view() {

	if ( ! current_user_can( 'view_shop_reports' ) ) {
		return;
	}

	include( dirname( __FILE__ ) . '/admin/class-email-logs-list-table.php' );

	$logs_table = new EDD_Message_Log_Table();
	$logs_table->prepare_items();
	$logs_table->display();
	?>
	<div id="edd-message-log-message-modal" style="display: none;">
		<div class="edd-message-log-message-modal-container">
			<a href="#" class="edd-message-log-message-modal-close" data-log-modal-close
			   title="<?php _e( 'Close Message Window', 'edd-message' ); ?>">
				<span class="screen-reader-text">
					<?php _e( 'Close Message Window', 'edd-message' ); ?>
				</span>

				<span class="dashicons dashicons-no"></span>
			</a>

			<div class="edd-message-log-message-modal-loading">
				<span class="spinner is-active"></span>
			</div>

			<div class="edd-message-log-message-modal-content"></div>
		</div>
	</div>
	<?php
}

add_action( 'edd_logs_view_email', 'edd_message_email_logs_view' );

/**
 * Includes the email log view in view selector
 *
 * @param $views
 *
 * @return mixed
 */
function edd_message_add_email_log_view( $views ) {
	$views['email'] = __( 'Messages', 'edd-message' );

	return $views;
}

add_filter( 'edd_log_views', 'edd_message_add_email_log_view' );

/**
 * Registers the email log view
 *
 * @param $terms
 *
 * @return array
 */
function edd_message_add_email_logging( $terms ) {
	$terms[] = 'email';

	return $terms;
}

add_filter( 'edd_log_types', 'edd_message_add_email_logging' );

/**
 * Gets a list of all logged messages to a customer
 *
 * @param $id
 */
function edd_message_get_logged_emails( $id ) {
	$args = array(
		'post_type'   => 'edd_log',
		'post_status' => 'publish',
		'tax_query'   => array(
			'taxonomy' => 'edd_log_type',
			'field'    => 'slug',
			'terms'    => 'email',
		),
		'meta_key'    => '_edd_log_customer_id',
		'meta_value'  => $id
	);

	$logs = get_posts( $args );

	if ( ! empty( $logs ) && ! is_wp_error( $logs ) ) : ?>
		<div class="edd-message-logs">

			<h2><?php _e( 'Message history', 'edd-message' ); ?></h2>

			<div id="postbox-container-2" class="postbox-container">

				<?php foreach ( $logs as $log ) : ?>

					<div id="edd-message-log-<?php echo $log->ID; ?>" class="meta-box-sortables ui-sortable-disabled">
						<div class="postbox closed">
							<button type="button" class="handlediv button-link" aria-expanded="false">
								<span class="screen-reader-text">
									<?php
									_e( 'Toggle panel:', 'edd-message' );
									echo get_the_title( $log->ID );
									?>
								</span>
								<span class="toggle-indicator" aria-hidden="true"></span>
							</button>

							<h3 class="hndle ui-sortable-handle">
								<?php echo get_the_title( $log->ID ); ?>
							</h3>

							<em>
								<?php echo get_the_date( '', $log->ID ); ?>
							</em>

							<div class="inside">
								<?php edd_message_show_message( $log ); ?>
							</div>
						</div>
					</div>

				<?php endforeach; ?>

			</div>
		</div>
	<?php endif;
}

/**
 * Log EDD purchase receipts
 *
 * @param $check
 * @param $to
 * @param $subject
 * @param $message
 *
 * @since 1.1
 * @return mixed
 */
function edd_message_log_purchase_receipts( $check, $to, $subject, $message ) {

	if ( edd_message_maybe_log( 'purchase_receipt' ) && edd_get_option( 'purchase_subject' ) === $subject ) {

		$customer = new EDD_DB_Customers();
		$customer = $customer->get_customer_by( 'email', $to );

		$args = array(
			'to'          => array( $to ),
			'subject'     => $subject,
			'message'     => $message,
			'from_email'  => edd_get_option( 'from_email' ),
			'type'        => 'purchase_receipt',
			'customer_id' => $customer->id,
		);

		edd_message_log( $args );
	}

	return $check;
}

add_filter( 'edd_log_email_errors', 'edd_message_log_purchase_receipts', 10, 4 );

/**
 * Retrieves a log message via AJAX.
 *
 * @since {{VERSION}}
 * @access private
 */
function edd_message_log_get_message_ajax() {

	$log_ID = $_POST['log_ID'];

	if ( ! ( $log_post = get_post( $log_ID ) ) ) {

		wp_send_json_error( array(
			'error' => __( 'Could not get message.', 'edd-message' ),
		) );
	}

	wp_send_json_success( array(
		'message' => apply_filters( 'the_content', $log_post->post_content ),
	) );
}

add_action( 'wp_ajax_edd_message_log_get_message', 'edd_message_log_get_message_ajax' );

/**
 * Deletes a log via AJAX.
 *
 * @since {{VERSION}}
 * @access private
 */
function edd_message_log_delete_ajax() {

	$log_ID = $_POST['log_ID'];

	if ( ! wp_delete_post( $log_ID, true ) ) {

		wp_send_json_error( array(
			'error' => __( 'Could not delete message log.', 'edd-message' ),
		) );
	}

	wp_send_json_success();
}

add_action( 'wp_ajax_edd_message_delete_message', 'edd_message_log_delete_ajax' );