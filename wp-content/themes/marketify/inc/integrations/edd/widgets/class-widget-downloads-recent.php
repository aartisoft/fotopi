<?php

class Marketify_Widget_Recent_Downloads extends Marketify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'marketify_widget_recent_downloads';
		$this->widget_description = sprintf( __( 'Display recent %s in a grid.', 'marketify' ), edd_get_label_plural() );
		$this->widget_id          = 'marketify_widget_recent_downloads';
		$this->widget_name        = sprintf( __( 'Marketify - Page: Recent %s', 'marketify' ), edd_get_label_plural() );
		$this->settings           = array(
			'home-1 widgetized' => array(
				'type' => 'widget-area',
				'std' => __( 'Widgetized Page or Home', 'marketify' ),
			),
			'title' => array(
				'type'  => 'text',
				'std'   => 'Recent',
				'label' => __( 'Title:', 'marketify' ),
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 8,
				'label' => __( 'Number to display:', 'marketify' ),
			),
			'columns' => array(
				'type' => 'select',
				'std' => 3,
				'label' => __( 'Columns:', 'marketify' ),
				'options' => array( 1 => 1, 2 => 2, 3 => 3, 4 => 4 ),
			),
		);
		parent::__construct();
	}

	function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 8;
		$columns = isset( $instance['columns'] ) ? absint( $instance['columns'] ) : 3;

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . esc_attr( $title ) . $args['after_title'];
		}
		?>

		<?php echo do_shortcode( "[downloads columns={$columns} number={$number} pagination=false]" ); ?>

		<?php
		echo $args['after_widget'];
	}

}
