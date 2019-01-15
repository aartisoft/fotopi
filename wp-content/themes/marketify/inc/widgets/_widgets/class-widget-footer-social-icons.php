<?php
/**
 * Widget: Footer social icons.
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
 * Widget: Footer social icons.
 *
 * @since 2.12.0
 */
class Marketify_Widget_Footer_Social_Icons extends Marketify_Widget {

	/**
	 * Register widget.
	 *
	 * @since 2.12.0
	 */
	public function __construct() {
		$this->widget_name        = __( 'Marketify - Footer: Social Icons', 'marketify' );
		$this->widget_description = __( 'Display the social profile menu with icons.', 'marketify' );

		$this->widget_cssclass    = 'marketify-widget-footer-social-icons';
		$this->widget_id          = 'marketify-widget-footer-social-icons';

		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => marketify()->template->navigation->get_theme_menu_name( 'social' ),
				'label' => __( 'Title:', 'marketify' ),
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

		if ( $title ) {
			echo $args['before_title'] . esc_attr( $title ) . $args['after_title']; // WPCS: XSS ok.
		}

		$social = wp_nav_menu( array(
			'theme_location'  => 'social',
			'container_class' => 'footer-social',
			'items_wrap'      => '%3$s',
			'depth'           => 1,
			'echo'            => false,
			'link_before'     => '<span class="screen-reader-text">',
			'link_after'      => '</span>',
		) );

		echo '<div class="footer-social">'; // WPCS: XSS ok.

		echo wp_kses( strip_tags( $social, '<a><div><span>' ), array(
			'a' => array(
				'href' => array(),
				'class' => array(),
			),
			'span' => array(
				'class' => array(),
			),
		) );

		echo '</div>'; // WPCS: XSS ok.

		echo $args['after_widget']; // WPCS: XSS ok.

	}

}
