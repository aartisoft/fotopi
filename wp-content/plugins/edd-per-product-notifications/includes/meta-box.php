<?php
/**
 * Add Meta Box
 *
 * @package     Per Product Notifications for Easy Digital Downloads
 * @subpackage  Add Meta Box
 * @copyright   Copyright (c) 2013, Markus Drubba (dev@markusdrubba.de)
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

/**
 * Add E-Mail Meta Box
 *
 * @since 1.0.0
 * @return void;
 */
function drubba_ppn_add_email_meta_box() {

	add_meta_box( 'drubba_ppn_box', __( 'Email Notifications', 'edd' ), 'drubba_ppn_render_email_meta_box', 'download', 'side', 'core' );

}

add_action( 'add_meta_boxes', 'drubba_ppn_add_email_meta_box', 100 );


/**
 * Render the download information meta box
 *
 * @since 1.0.0
 * @return void
 */
function drubba_ppn_render_email_meta_box() {

	global $post, $edd_options;
	// Use nonce for verification
	echo '<input type="hidden" name="drubba_ppn_meta_box_nonce" value="', wp_create_nonce( basename( __FILE__ ) ), '" />';

	echo '<table class="form-table">';

	$emails             = get_post_meta( $post->ID, '_drubba_ppn_notification_emails', true );
	$crowd_notification = get_post_meta( $post->ID, '_drubba_ppn_disable_crowdfunding_author_notification', true );


	if ( class_exists( 'ATCF_CrowdFunding' ) && isset( $edd_options['drubba_ppn_send_crowdfunding_notices'] ) ) {
		echo '<tr class="drubba_ppn_toggled_row">';
		echo '<td class="edd_field_type_checkbox">';
		echo '<label><input type="checkbox" name="drubba_ppn_disable_crowdfunding_author_notification" id="drubba_ppn_disable_crowdfunding_author_notification" value="1" ' . checked( $crowd_notification, 1, false ) . '>&nbsp;' . __( 'Disable Campaign Author Notification' ) . '</label>';
		echo '</td>';
		echo '</tr>';
	}

	echo '<tr class="drubba_ppn_toggled_row">';
	echo '<td class="edd_field_type_textarea">';
	echo '<textarea name="drubba_ppn_notification_emails" id="drubba_ppn_notification_emails" rows="5" style="width: 96%;">' . esc_textarea( stripslashes( $emails ) ) . '</textarea>';
	echo '<div class="description">' . __( 'Enter the email address(es) that should receive a notification anytime a sale is made, one per line', 'edd' ) . '</div>';
	echo '</td>';
	echo '</tr>';

	echo '</table>';
}


/**
 * Save data from meta box
 *
 * @since 1.0.0
 */
function drubba_ppn_download_meta_box_save( $post_id ) {

	// verify nonce
	if ( ! isset( $_POST['drubba_ppn_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['drubba_ppn_meta_box_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}

	// check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	if ( ! isset( $_POST['post_type'] ) || 'download' != $_POST['post_type'] ) {
		return $post_id;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	if ( isset( $_POST['drubba_ppn_notification_emails'] ) ) {
		update_post_meta( $post_id, '_drubba_ppn_notification_emails', addslashes( $_POST['drubba_ppn_notification_emails'] ) );
	} else {
		delete_post_meta( $post_id, '_drubba_ppn_notification_emails' );
	}

	if ( isset( $_POST['drubba_ppn_disable_crowdfunding_author_notification'] ) ) {
		update_post_meta( $post_id, '_drubba_ppn_disable_crowdfunding_author_notification', addslashes( $_POST['drubba_ppn_disable_crowdfunding_author_notification'] ) );
	} else {
		delete_post_meta( $post_id, '_drubba_ppn_disable_crowdfunding_author_notification' );
	}

}

add_action( 'save_post', 'drubba_ppn_download_meta_box_save' );