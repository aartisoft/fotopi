<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class EDD_Message_FES_Backend
 *
 * Adds a new view to the single vendor interface in the admin
 */
class EDD_Message_FES_Backend {

	/**
	 * Initialize all the things
	 */
	public function __construct() {
		add_filter( 'fes_vendor_views', array( $this, 'add_view' ) );
		add_filter( 'fes_vendor_tabs', array( $this, 'add_tab' ) );
		add_filter( 'edd_is_admin_page', array( $this, 'make_edd_admin_page' ), 11, 3 );
		add_filter( 'edd_message_settings', array( $this, 'add_settings' ) );
	}

	/**
	 * Registers the FES view
	 */
	public function add_view( $views ) {
		$views['message'] = 'edd_message_fes_message_vendor_view';

		return $views;
	}

	/**
	 * Creates a new tab on the vendor view page
	 *
	 * @param $tabs
	 *
	 * @return mixed
	 */
	public function add_tab( $tabs ) {
		$tabs['message'] = array(
			'dashicon' => 'dashicons-email-alt',
			'title'    => __( 'Message Vendor', 'edd-message' ),
		);

		return $tabs;
	}

	/**
	 * Makes the FES message vendor view an EDD admin page
	 *
	 * @param $found
	 * @param $page
	 * @param $view
	 *
	 * @return bool
	 */
	public function make_edd_admin_page( $found, $page, $view ) {
		if ( $page == 'fes-vendors' && $view == 'message' ) {
			$found = true;
		}

		return $found;
	}

	public function view_content( $vendor ) {
		edd_message_customer_messages_view( $vendor );
	}

	public function add_settings( $settings ) {

		$settings['edd-message-settings'][] = array(
			'id'   => 'edd_message_disable_vendor_messaging',
			'name' => __( 'Disable vendor messaging', 'edd-message' ),
			'desc' => __( 'Check this to remove the messaging functionality from the vendor dashboard.', 'edd-message' ),
			'type' => 'checkbox',
		);

		$settings['edd-message-settings'][] = array(
			'id'      => 'edd_message_disabled_vendor_fields',
			'name'    => __( 'Disable vendor messaging fields', 'edd-message' ),
			'type'    => 'multicheck',
			'options' => apply_filters( 'edd_message_vendor_fields', array(
					'from-email'  => __( 'From Email', 'edd-message' ),
					'from-name'   => __( 'From Name', 'edd-message' ),
					'reply'    => __( 'Reply To', 'edd-message' ),
					'cc'          => __( 'CC', 'edd-message' ),
					'bcc'         => __( 'BCC', 'edd-message' ),
					'attachments' => __( 'Attachments', 'edd-message' ),
				)
			),
		);

		return $settings;
	}

}

new EDD_Message_FES_Backend();

/**
 * Callback for FES vendor view content
 *
 * @param $vendor
 */
function edd_message_fes_message_vendor_view( $vendor ) {
	$view = new EDD_Message_FES_Backend();
	$view->view_content( $vendor );
}