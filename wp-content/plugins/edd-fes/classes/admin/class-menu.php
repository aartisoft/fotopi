<?php
/**
 * FES Menu
 *
 * This file deals with FES's menu items.
 *
 * @package FES
 * @subpackage Administration
 * @since 2.3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * FES Menu.
 *
 * Creates all of the menu and submenu
 * items FES adds to the backend.
 *
 * @since 2.3.0
 * @access public
 */
class FES_Menu {

	/**
	 * FES Menu Actions.
	 *
	 * Runs actions required to add
	 * menus and submenus.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus' ), 9 );
	}

	/**
	 * FES Menu Items.
	 *
	 * Adds the menu and submenu pages.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return void
	 */
	public function admin_menus() {
		if ( EDD_FES()->vendors->user_is_admin() ) {
			$tools      = new FES_Tools();

			/**
			 * Filter the minimum capability to view FES admin pages.
			 *
			 * @since 2.6
			 */
			$minimum_capability = apply_filters( 'edd_fes_admin_menu_minimum_capability', 'manage_shop_settings' );

			add_menu_page( __( 'EDD FES', 'edd_fes' ), __( 'EDD FES', 'edd_fes' ), $minimum_capability, 'fes-vendors', 'fes_vendors_page', '', '25.01' );
			add_submenu_page( 'fes-vendors', EDD_FES()->helper->get_vendor_constant_name( true, true ), EDD_FES()->helper->get_vendor_constant_name( true, true ), $minimum_capability, 'fes-vendors', 'fes_vendors_page' );

			foreach ( EDD_FES()->load_forms as $name => $class ) {
				$form = new $class( $name, 'name' );
				if ( $form->has_formbuilder() && ! empty( $form->id ) ) {
					add_submenu_page( 'fes-vendors', $form->title( true ), $form->title( true ), $minimum_capability, 'post.php?post=' . $form->id . '&action=edit' );
				}
			}

			add_submenu_page( 'fes-vendors', __( 'Tools', 'edd_fes' ), __( 'Tools', 'edd_fes' ), $minimum_capability, 'fes-tools', array( $tools, 'fes_tools_page' ) );
			add_submenu_page( 'fes-vendors', __( 'Settings', 'edd_fes' ), __( 'Settings', 'edd_fes' ), $minimum_capability, 'edit.php?post_type=download&page=edd-settings&tab=fes', 'edd_options_page' );
		}
	}
}
