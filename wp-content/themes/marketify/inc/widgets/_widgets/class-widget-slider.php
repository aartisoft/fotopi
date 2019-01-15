<?php

class Marketify_Widget_Slider extends Marketify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'marketify_widget_slider';
		$this->widget_description = __( 'Display any slider that supports shortcodes.', 'marketify' );
		$this->widget_id          = 'marketify_widget_slider';
		$this->widget_name        = __( 'Marketify - Page: Slider', 'marketify' );
		$this->settings           = array(
			'shortcode' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Slider Shortcode', 'marketify' ),
			),
		);
		parent::__construct();
	}

	function widget( $args, $instance ) {
		extract( $args );

		if ( ! isset( $instance['shortcode'] ) ) {
			return;
		}

		echo str_replace( 'container', '', $before_widget );
		echo do_shortcode( $instance['shortcode'] );
		echo $after_widget;
	}
}
