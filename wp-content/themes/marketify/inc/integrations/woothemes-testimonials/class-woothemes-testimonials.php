<?php

class Marketify_WooThemes_Testimonials extends Marketify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	public function setup_actions() {
		add_action( 'dynamic_sidebar', array( $this, 'choose_template' ) );
	}

	public function choose_template( $widget ) {
		if ( 'widget_woothemes_testimonials' != $widget['classname'] ) {
			return $widget;
		}

		$options = get_option( $widget['classname'] );
		$options = $options[ $widget['params'][0]['number'] ];

		if ( ! isset( $options['display_avatar'] ) ) {
			return $widget;
		}

		if ( 1 == $options['display_avatar'] && null == $options['display_author'] ) {
			add_filter( 'woothemes_testimonials_item_template', array( $this, 'template_company' ), 10, 2 );
		} else {
			add_filter( 'woothemes_testimonials_item_template', array( $this, 'template_individual' ), 10, 2 );
		}

		return $widget;
	}

	public function template_company( $template, $args ) {
		return '<div class="%%CLASS%% company-testimonial">%%AVATAR%%</div>';
	}

	public function template_individual( $template, $args ) {
		return '<div id="quote-%%ID%%" class="%%CLASS%% individual-testimonial"><blockquote class="testimonials-text">%%TEXT%%</blockquote>%%AVATAR%% %%AUTHOR%%<div class="fix"></div></div>';
	}

}
