<?php
/**
 * Widget: Footer copyright.
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
 * Widget: Footer copyright.
 *
 * @since 2.12.0
 */
class Marketify_Widget_Footer_Copyright extends Marketify_Widget {

	/**
	 * Register widget.
	 *
	 * @since 2.12.0
	 */
	public function __construct() {
		$this->widget_name        = __( 'Marketify - Footer: Copyright', 'marketify' );
		$this->widget_description = __( 'Display copyright information.', 'marketify' );

		$this->widget_cssclass    = 'marketify-widget-footer-copyright';
		$this->widget_id          = 'marketify-widget-footer-copyright';

		$this->settings           = array(
			'logo' => array(
				'type'  => 'text',
				'std'   => get_theme_mod( 'footer-copyright-logo', '' ),
				'label' => __( 'Logo:', 'marketify' ),
			),

			'copyright' => array(
				'type'  => 'textarea',
				'std'   => get_theme_mod( 'footer-copyright-text', sprintf( 'Copyright &copy; %s %s', date( 'Y' ), get_bloginfo( 'name' ) ) ),
				'label' => __( 'Copyright:', 'marketify' ),
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

		$logo = isset( $instance['logo'] ) ? $instance['logo'] : $this->settings['logo']['std'];
		$copyright = isset( $instance['copyright'] ) ? $instance['copyright'] : $this->settings['copyright']['std'];

		echo '<h3 class="site-title site-title--footer"><a href="' . esc_url( home_url( '/' ) ) . '">'; // WPCS: XSS ok.

		if ( '' !== $logo ) {
			echo '<img src="' . esc_url( get_theme_mod( 'footer-copyright-logo', '' ) ) . '" />'; // WPCS: XSS ok.
		} else {
			bloginfo( 'name' );
		}

		echo '</a></h3>'; // WPCS: XSS ok.

		echo wp_kses_post( $copyright );

		echo $args['after_widget']; // WPCS: XSS ok.

	}

}
