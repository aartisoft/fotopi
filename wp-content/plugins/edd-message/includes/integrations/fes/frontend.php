<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EDD_Message_FES_Frontend {

	public function __construct() {
		add_filter( 'fes_signal_custom_task', array( $this, 'add_task' ), 10, 2 );
		add_action( 'fes_custom_task_message', array( $this, 'process_form' ) );
		add_action( 'fes_custom_task_message', array( $this, 'show_form' ) );
		add_action( 'fes-order-table-column-title', array( $this, 'orders_column_name' ) );
		add_action( 'fes-order-table-column-value', array( $this, 'orders_column_data' ) );
	}

	/**
	 * Checks to see if vendor messaging has been disabled
	 *
	 * @return bool
	 * @since 1.1
	 */
	public function disabled() {
		$disabled = ( edd_get_option( 'edd_message_disable_vendor_messaging' ) ) ? true : false;

		return $disabled;
	}

	/**
	 * Adds a new column to the orders table in the vendor dashboard
	 *
	 * @since 1.0
	 */
	public function orders_column_name() {
		if ( $this->disabled() ) {
			return;
		}
		echo '<th>' . __( 'Message', 'edd-message' ) . '</th>';
	}

	/**
	 * Adds a button in the Message column for each order which goes to the message form
	 *
	 * @since 1.0
	 * @param $order
	 */
	public function orders_column_data( $order ) {

		if ( $this->disabled() ) {
			return;
		}

		// Get customer's email address
		$payment = edd_get_payment_by( 'id', $order->ID );
		$email   = $payment->email;
		?>
		<td class="fes-order-list-td">
			<?php if ( ! empty ( $email ) ) : ?>
				<form method="post" action="<?php echo esc_url( add_query_arg( array( 'task' => 'message' ) ) ); ?>">
					<input type="hidden" name="edd-message-emails" value="<?php echo esc_attr( $email ); ?>"/>
					<?php wp_nonce_field( 'add-customer-message', 'add_customer_message_nonce', true, true ); ?>
					<input id="add-customer-message" class="right button-primary" type="submit"
					       value="<?php _e( 'Send message', 'edd-message' ); ?>"/>
				</form>
			<?php endif; ?>
		</td>
		<?php
	}

	/**
	 * Registers the message section in the vendor dashboard
	 *
	 * @param $show
	 * @param $task
	 * @since 1.0
	 * @return bool
	 */
	public function add_task( $show, $task ) {
		if ( $this->disabled() ) {
			return false;
		}

		if ( $task == 'message' ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Outputs the actual form on the message section of the vendor dashboard
	 *
	 * @since 1.0
	 */
	public function show_form() {

		if ( $this->disabled() ) {
			return;
		}

		// Disabled fields
		$disabled = (array) edd_get_option( 'edd_message_disabled_vendor_fields' );

		// Attachments configuration
		if ( ! isset( $disabled['attachments'] ) ) {

			$attachments                            = new FES_File_Upload_Field();
			$attachments->characteristics['name']   = 'edd-message-files';
			$attachments->characteristics['single'] = true;
			$attachments->characteristics['label']  = __( 'Attachments', 'edd-message' );
			$attachments                            = $attachments->render_field_frontend( 3 );
		}

		// All other fields
		$fields      = new EDD_HTML_Elements();
		$current_uri = home_url( add_query_arg( null, null ) );

		$field_values = edd_message_get_message_field_values( $_POST );

		if ( is_array( $field_values['emails'] ) ) {

			$field_values['emails'] = implode( ', ', $field_values['emails'] );
		}
		?>
		<form id="edd-add-customer-message" method="post" action="<?php echo esc_url( $current_uri ); ?>">
			<div class="edd-message-fields">
				<?php echo $fields->text( array(
					'id'    => 'edd-message-emails',
					'name'  => 'edd-message-emails',
					'label' => __( 'To: ', 'edd-message' ),
					'value' => esc_attr( $field_values['emails'] ),
				) ); ?>
				<br/>

				<?php if ( ! isset( $disabled['from-name'] ) ) : ?>
					<?php echo $fields->text( array(
						'id'    => 'edd-message-from-name',
						'name'  => 'edd-message-from-name',
						'label' => __( 'From name: ', 'edd-message' ),
						'value' => esc_attr( $field_values['from-name'] ),
					) ); ?>
					<br/>
				<?php endif; ?>

				<?php if ( ! isset( $disabled['from-email'] ) ) : ?>
					<?php echo $fields->text( array(
						'id'    => 'edd-message-from-email',
						'name'  => 'edd-message-from-email',
						'label' => __( 'From email: ', 'edd-message' ),
						'value' => esc_attr( $field_values['from-email'] ),
					) ); ?>
					<br/>
				<?php endif; ?>

				<?php if ( ! isset( $disabled['reply'] ) ) : ?>
					<?php echo $fields->text( array(
						'id'    => 'edd-message-reply',
						'name'  => 'edd-message-reply',
						'label' => __( 'Reply to: ', 'edd-message' ),
						'value' => esc_attr( $field_values['reply'] ),
					) ); ?>
					<br/>
				<?php endif; ?>

				<?php if ( ! isset( $disabled['cc'] ) ) : ?>
					<?php echo $fields->text( array(
						'id'    => 'edd-message-cc',
						'name'  => 'edd-message-cc',
						'label' => __( 'CC: ', 'edd-message' ),
						'value' => esc_attr( $field_values['cc'] ),
					) ); ?>
					<br/>
				<?php endif; ?>

				<?php if ( ! isset( $disabled['bcc'] ) ) : ?>
					<?php echo $fields->text( array(
						'id'    => 'edd-message-bcc',
						'name'  => 'edd-message-bcc',
						'label' => __( 'BCC: ', 'edd-message' ),
						'value' => esc_attr( $field_values['bcc'] ),
					) ); ?>
					<br/>
				<?php endif; ?>

				<?php echo $fields->text( array(
					'id'    => 'edd-message-subject',
					'name'  => 'edd-message-subject',
					'label' => __( 'Subject: ', 'edd-message' ),
					'value' => esc_attr( $field_values['subject'] ),
				) ); ?>
			</div>
			<br/>

			<label>
				<?php _e( 'Message:', 'edd-message' ); ?>
			</label>

			<?php
			wp_editor( $field_values['message'], 'edd-message-message', array(
				'teeny'         => true,
				'editor_height' => 140,
			) );

			if ( ! isset( $disabled['attachments'] ) ) {

				// @todo figure out why multiple attachments aren't working
				echo $attachments;
			}
			?>
			<br/>
			<?php wp_nonce_field( 'add-customer-message', 'add_customer_message_nonce', true, true ); ?>
			<span class="edd-message-fields">
				<input id="add-customer-message" class="right button-primary" type="submit"
				       value="<?php _e( 'Send message', 'edd-message' ); ?>"/>
			</span>
		</form>
		<?php
	}

	/**
	 * Processes the message form when submitted by a vendor
	 *
	 * @since 1.0
	 */
	public function process_form() {
		$args = $_POST;
		if ( empty( $args ) ) {
			return;
		}

		if ( array_key_exists( 'edd-message-emails', $args ) ) {
			if ( empty( $args['edd-message-emails'] ) ) {
				echo '<div class="error">' . __( 'A recipient is required.', 'edd-message' ) . '</div>';

				return;
			} else {
				$to = trim( sanitize_text_field( $args['edd-message-emails'] ) );
			}
		} else {
			return;
		}
		if ( array_key_exists( 'edd-message-message', $args ) ) {
			if ( empty( $args['edd-message-message'] ) ) {
				echo '<div class="error">' . __( 'A message is required.', 'edd-message' ) . '</div>';

				return;
			} else {
				$message = wp_kses_post( do_shortcode( $args['edd-message-message'] ) );
			}
		} else {
			return;
		}
		if ( array_key_exists( 'edd-message-subject', $args ) ) {
			if ( empty( $args['edd-message-subject'] ) ) {
				echo '<div class="error">' . __( 'A subject is required.', 'edd-message' ) . '</div>';

				return;
			} else {
				$subject = trim( sanitize_text_field( $args['edd-message-subject'] ) );
			}
		} else {
			return;
		}
		if ( ! empty( $args['edd-message-files'][0] ) ) {
			$files           = $args['edd-message-files'];
			$wp_upload_url   = wp_upload_dir();
			$edd_upload_path = edd_get_upload_dir();
			$attachments     = array();
			foreach ( $files as $file ) {
				$file          = str_replace( $wp_upload_url['baseurl'] . '/edd', '', $file );
				$attachments[] = $edd_upload_path . $file;
			}
		} else {
			$attachments = null;
		}

		$nonce = $args['add_customer_message_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'add-customer-message' ) ) {
			wp_die( __( 'Cheatin\' eh?!', 'edd-message' ) );
		}

		// Send the message
		$email = new EDD_Emails();
		$email->send( $to, $subject, $message, $attachments );

		// Logging data
		$log_args = array(
			'message'     => $message,
			'subject'     => $subject,
			'to'          => $to,
			'attachments' => $attachments,
			'vendor'      => get_current_user_id(),
		);
		$optional = array(
			'from-name',
			'from-email',
			'reply',
			'cc',
			'bcc',
		);
		foreach ( $optional as $field ) {
			if ( ! empty( $args[ 'edd-message-' . $field ] ) ) {
				$log_args[ $field ] = $args[ 'edd-message-' . $field ];
			}
		}
		edd_message_log_vendor_messages( $log_args );

		echo '<div class="success">' . __( 'Message sent successfully.', 'edd-message' ) . '</div>';
	}
}

new EDD_Message_FES_Frontend();