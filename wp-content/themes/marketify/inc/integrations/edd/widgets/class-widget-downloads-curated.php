<?php

class Marketify_Widget_Curated_Downloads extends Marketify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'marketify_widget_curated_downloads';
		$this->widget_description = sprintf( __( 'Display curated %s in a grid.', 'marketify' ), edd_get_label_plural() );
		$this->widget_id          = 'marketify_widget_curated_downloads';
		$this->widget_name        = sprintf( __( 'Marketify - Page: Curated %s', 'marketify' ), edd_get_label_plural() );
		$this->settings           = array(
			'home-1' => array(
				'type' => 'widget-area',
				'std' => __( 'Home', 'marketify' ),
			),
			'title' => array(
				'type'  => 'text',
				'std'   => edd_get_label_plural(),
				'label' => __( 'Title:', 'marketify' ),
			),
			'ids' => array(
				'type' => 'text',
				'std'  => '',
				'label' => sprintf( __( '%s IDs: (comma separated)', 'marketify' ), edd_get_label_singular() ),
			),
			'columns' => array(
				'type' => 'select',
				'std' => 3,
				'label' => __( 'Columns:', 'marketify' ),
				'options' => array(
					1 => 1,
					2 => 2,
					3 => 3,
					4 => 4,
				),
			),
		);
		parent::__construct();
	}

	function widget( $args, $instance ) {
		global $post;

		extract( $args );

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );

		$ids          = isset( $instance['ids'] ) ? $instance['ids'] : array();
		$ids          = implode( ',', array_map( 'trim', explode( ',', $ids ) ) );

		$columns      = isset( $instance['columns'] ) ? absint( $instance['columns'] ) : 3;

		echo $before_widget;

		if ( $title ) {
			echo $before_title . esc_attr( $title ) . $after_title;
		}

		echo do_shortcode( "[downloads columns={$columns} ids={$ids} orderby=post__in]" );

		echo $after_widget;
	}
}
