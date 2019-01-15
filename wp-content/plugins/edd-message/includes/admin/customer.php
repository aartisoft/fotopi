<?php

/**
 * Add Messages to the EDD Customer Interface
 *
 * @since       0.1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the messages tab to the customer interface
 *
 * @since  0.1.0
 * @param  array $tabs The tabs currently added to the customer view
 * @return array       Updated tabs array
 */
function edd_message_customer_tab( $tabs ) {

	$tabs['messages'] = array( 'dashicon' => 'dashicons-email-alt', 'title' => __( 'Messages', 'edd-message' ) );

	return $tabs;
}

add_filter( 'edd_customer_tabs', 'edd_message_customer_tab', 10, 1 );

/**
 * Register the messages view for the customer interface
 *
 * @since  0.1.0
 * @param  array $views The tabs currently added to the customer views
 * @return array       Updated tabs array
 */
function edd_message_customer_view( $views ) {
	$views['messages'] = 'edd_message_customer_messages_view';

	return $views;
}

add_filter( 'edd_customer_views', 'edd_message_customer_view', 10, 1 );

/**
 * Display the messages area for the customer view
 *
 * @since  0.1.0
 * @param  object $customer The Customer being displayed
 * @return void
 */
function edd_message_customer_messages_view( $customer ) {

	$customer_emails = ( isset( $customer->emails ) ) ? $customer->emails : array( $customer->email );
	$customer_id = $customer->id;

	$field_values = edd_message_get_message_field_values( $_POST );
	?>
	<div id="poststuff" class="edd-message">
		<div class="edd-item-message-header">
			<?php echo get_avatar( $customer->email, 30 ); ?> <span><?php echo $customer->name; ?></span>
		</div>
		<h3><?php _e( 'Messages', 'edd-message' ); ?></h3>

		<div id="post-body" class="metabox-holder columns-1" style="display: block; margin-bottom: 35px;">
			<form id="edd-add-customer-message" method="post">
				<div class="edd-message-fields">
					<?php $fields = new EDD_HTML_Elements(); ?>
					<label class="edd-label" for="edd-message-selected-emails[]">To: </label>
					<?php echo $fields->select( array(
						'id'               => 'edd-message-selected-emails',
						'name'             => 'edd-message-selected-emails[]',
						'options'          => $customer_emails,
						'multiple'         => true,
						'selected'         => $field_values['emails'],
						'chosen'           => true,
						'show_option_none' => false,
						'show_option_all'  => false,
						'placeholder'      => __( 'Choose one or more email addresses', 'edd-message' ),
						'data'             => array(
							'search-placeholder' => __( 'Type to search all email addresses', 'edd-message' ),
						),
					) ); ?>
					<input type="hidden" name="edd-message-emails" value="<?php echo implode( ',', $customer_emails ); ?>"/>
					<br/>
					<script type="text/javascript">
						jQuery(document).ready(function($)
						{$('#edd_message_show_more_fields').on('click',function()
						{$('#edd_message_more_fields').toggle( 'display' );});
						});
					</script>
					<label for="edd_message_show_more_fields" class="alignright">
						<?php _e( 'More fields', 'edd-message' ); ?>
						<input type="checkbox" name="_edd_message_show_more_fields" id="edd_message_show_more_fields" value="1">
					</label>
					<div id="edd_message_more_fields" style="display: none;">
						<?php echo $fields->text( array(
							'id'    => 'edd-message-from-name',
							'name'  => 'edd-message-from-name',
							'label' => __( 'From name: ', 'edd-message' ),
							'value' => esc_attr( $field_values['from-name'] ),
						) ); ?>
						<br/>
						<?php echo $fields->text( array(
							'id'    => 'edd-message-from-email',
							'name'  => 'edd-message-from-email',
							'label' => __( 'From email: ', 'edd-message' ),
							'value' => esc_attr( $field_values['from-email'] ),
						) ); ?>
						<br/>
						<?php echo $fields->text( array(
							'id'    => 'edd-message-reply',
							'name'  => 'edd-message-reply',
							'label' => __( 'Reply to: ', 'edd-message' ),
							'value' => esc_attr( $field_values['reply'] ),
						) ); ?>
						<br/>
						<?php echo $fields->text( array(
							'id'    => 'edd-message-cc',
							'name'  => 'edd-message-cc',
							'label' => __( 'CC: ', 'edd-message' ),
							'value' => esc_attr( $field_values['cc'] ),
						) ); ?>
						<br/>
						<?php echo $fields->text( array(
							'id'    => 'edd-message-bcc',
							'name'  => 'edd-message-bcc',
							'label' => __( 'BCC: ', 'edd-message' ),
							'value' => esc_attr( $field_values['bcc'] ),
						) ); ?>
					</div>
					<br/>
					<?php echo $fields->text( array(
						'id'    => 'edd-message-subject',
						'name'  => 'edd-message-subject',
						'label' => __( 'Subject: ', 'edd-message' ),
						'value' => esc_attr( $field_values['subject'] ),
					) ); ?>
				</div>
				<br/>
				<?php
				_e( 'Message:', 'edd-message' );

				wp_editor( $field_values['message'], 'edd-message-message', array(
					'teeny' => true,
					'editor_height' => 140
				) );
				?>
				<br/>
				<div class="edd-message-fields">
					<?php _e( 'Attachments:', 'edd-message' ); ?>
					<table>
						<tbody class="ui-sortable">
						<tr class="edd_repeatable_upload_wrapper edd_repeatable_row">

							<td>
								<input type="hidden" name="edd_download_files[1][index]" class="edd_repeatable_index"
								       value="1">
							</td>
							<td>
								<input type="hidden" name="edd_download_files[1][attachment_id]"
								       class="edd_repeatable_attachment_id_field" value="1">
							<span id="edd-edd_download_files1file-wrap"><label class="edd-label" for=""></label>
								<input type="text" name="edd_download_files[1][file]" id="" autocomplete="" value=""
								       placeholder="File Name" class="edd_repeatable_name_field large-text">
							</span>
							</td>
							<td>
								<div class="edd_repeatable_upload_field_container">
									<?php echo EDD()->html->text( array(
										'name'        => 'edd_download_files[1][file]',
										'value'       => '',
										'placeholder' => __( 'Upload or enter the file URL', 'edd-message' ),
										'class'       => 'edd_repeatable_upload_field edd_upload_field large-text'
									) ); ?>

									<span class="edd_upload_file">
								<a href="#" data-uploader-title="<?php _e( 'Insert File', 'edd-message' ); ?>"
								   data-uploader-button-text="<?php _e( 'Insert', 'edd-message' ); ?>"
								   class="edd_upload_file_button" onclick="return false;">
									<?php _e( 'Upload a File', 'edd-message' ); ?>
								</a>
							</span>
								</div>
							</td>

							<td>
								<button class="edd_remove_repeatable" data-type="file"
								        style="background: url(<?php echo admin_url( '/images/xit.gif' ); ?>) no-repeat;"><span
										class="screen-reader-text"><?php _e( 'Remove file option 1', 'edd-message' ); ?></span><span
										aria-hidden="true">×</span></button>
							</td>
						</tr>
						<tr>
							<td class="submit" colspan="4" style="float: none; clear:both; background: #fff;">
								<button class="button-secondary edd_add_repeatable" style="margin: 6px 0 10px;">
									<?php _e( 'Add New File', 'edd-message' ); ?>
								</button>
							</td>
						</tr>
						</tbody>
					</table>

					<input type="hidden" id="customer-id" name="edd-message-customer-id" value="<?php echo $customer->id; ?>"/>
					<input type="hidden" name="edd_action" value="add-customer-message"/>
					<?php wp_nonce_field( 'add-customer-message', 'add_customer_message_nonce', true, true ); ?>
					<input id="add-customer-message" class="right button-primary" type="submit" value="<?php _e( 'Send message', 'edd-message' ); ?>"/>
				</div>
			</form>
		</div>
		<?php edd_message_get_logged_emails( $customer_id ); ?>
	</div>

	<?php
}