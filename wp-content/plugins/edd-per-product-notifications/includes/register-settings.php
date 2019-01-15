<?php
/**
 * Register Settings
 *
 * @package     Per Product Notifications for Easy Digital Downloads
 * @subpackage  Register Settings
 * @copyright   Copyright (c) 2013, Markus Drubba (dev@markusdrubba.de)
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

/**
 * Register Settings
 *
 * Registers the required settings for the plugin and adds them to the 'Emails' tab.
 *
 * @access      private
 * @since       1.0.0
 * @return      void
 */
function drubba_ppn_register_settings( $settings ) {

	if ( class_exists( 'ATCF_CrowdFunding' ) ) {
		$settings[] = array(
			'id'   => 'drubba_ppn',
			'name' => '<strong>' . __( 'Per Product Notification', 'edd' ) . '</strong>',
			'desc' => '',
			'type' => 'header',
		);


		$settings[] = array(
			'id'   => 'drubba_ppn_send_crowdfunding_notices',
			'name' => __( 'Activate Notifications for Crowdfunding Campaign Authors', 'edd' ),
			'desc' => __( 'Check this box to enable PPN Notifications for Crowdfunding Campaign Authors. You can disable on each Campaign.', 'edd' ),
			'type' => 'checkbox',
		);
	}

	return $settings;

}

add_filter( 'edd_settings_extensions', 'drubba_ppn_register_settings', 20, 1 );