<?php

class Marketify_Widget_Download_Share extends Marketify_Widget {

	public function __construct() {
		$name = function_exists( 'edd_get_label_singular' ) ? edd_get_label_singular() : __( 'Download', 'marketify' );

		$this->widget_cssclass    = 'marketify-widget--download-single-share';
		$this->widget_description = __( 'Display sharing options for this product.', 'marketify' );
		$this->widget_id          = 'marketify_widget_download_share';
		$this->widget_name        = sprintf( __( 'Marketify - %s Sidebar: Sharing', 'marketify' ), $name );
		$this->settings           = array(
			'sidebar-download-single' => array(
				'type' => 'widget-area',
				'std'  => sprintf( __( '%s Sidebar', 'marketify' ), $name ),
			),
			'title' => array(
				'type'  => 'text',
				'std'   => 'Sharing Options',
				'label' => __( 'Title:', 'marketify' ),
			),
			'description' => array(
				'type'  => 'text',
				'std'   => 'Like this item? Why not share it with your friends?',
				'label' => __( 'Description:', 'marketify' ),
			),
		);
		parent::__construct();

		add_action( 'wp_head', array( $this, 'loop_start' ) );
	}

	public function loop_start() {
		remove_filter( 'the_content', 'sharing_display', 19 );
		remove_filter( 'the_excerpt', 'sharing_display', 19 );

		if ( class_exists( 'Jetpack_Likes' ) ) {
			remove_filter( 'the_content', array( Jetpack_Likes::init(), 'post_likes' ), 30, 1 );
		}
	}

	public function widget( $args, $instance ) {
		global $post;

		extract( $args );

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		$description = isset( $instance['description'] ) ? esc_attr( $instance['description'] ) : null;

		echo $before_widget;

		if ( $title ) {
			echo $before_title . esc_attr( $title ) . $after_title;
		}

		if ( $description ) {
			echo '<span class="widget-description">' . $description . '</span>';
		}

		do_action( 'marketify_widget_download_share_before' );
		do_action( 'marketify_widget_download_share' );
		do_action( 'marketify_widget_download_share_after' );

		echo $after_widget;
	}

}
