<?php
/**
 * Widget: Footer contact.
 *
 * @since 2.12.0
 *
 * @package Marketify
 * @category Widget
 * @author Astoundify
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Footer contact.
 *
 * @since 2.12.0
 */
class Marketify_Widget_Footer_Contact extends Marketify_Widget {

	/**
	 * Register widget.
	 *
	 * @since 2.12.0
	 */
	public function __construct() {
		$this->widget_name        = __( 'Marketify - Footer: Contact', 'marketify' );
		$this->widget_description = __( 'Display contact/address information.', 'marketify' );

		$this->widget_cssclass    = 'marketify-widget-footer-contact';
		$this->widget_id          = 'marketify-widget-footer-contact';

		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => get_theme_mod( 'footer-contact-us-title', 'Contact Us' ),
				'label' => __( 'Title:', 'marketify' ),
			),
			'info' => array(
				'type'  => 'textarea',
				'std'   => get_theme_mod( 'footer-contact-us-address', '393 Bay Street, 2nd Floor Toronto, Ontario, Canada, L9T8S2' ),
				'label' => __( 'Information:', 'marketify' ),
				'rows'  => 2,
			),
		);

		parent::__construct();
	}

	/**
	 * Output the widget.
	 *
	 * @since 2.12.0
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Widget instance.
	 */
	function widget( $args, $instance ) {
		echo $args['before_widget']; // WPCS: XSS ok.

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : $this->settings['title']['std'], $instance, $this->id_base );
		$info = isset( $instance['info'] ) ? $instance['info'] : $this->settings['info']['std'];

		if ( $title ) {
			echo $args['before_title'] . esc_attr( $title ) . $args['after_title']; // WPCS: XSS ok.
		}

		echo do_shortcode( wp_kses_post( $info ) ); // WPCS: XSS ok.

		echo $args['after_widget']; // WPCS: XSS ok.

	}

}
