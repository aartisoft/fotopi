<?php

class Marketify_Widget_FES_Product_Details extends Marketify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'widget--download-single-meta marketify_widget_fes_product_details';
		$this->widget_description = __( 'Output specificed submission form fields.', 'marketify' );
		$this->widget_id          = 'marketify_widget_fes_product_details';
		$this->widget_name        = sprintf( __( 'Marketify - %1$s: Meta', 'marketify' ), function_exists( 'edd_get_label_singular' ) ? edd_get_label_singular() : 'Download' );
		$this->settings           = array(
			'sidebar-download-single' => array(
				'type' => 'widget-area',
				'std'  => sprintf( __( '%s Sidebar', 'marketify' ), function_exists( 'edd_get_label_singular' ) ? edd_get_label_singular() : 'Download' ),
			),
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title:', 'marketify' ),
			),
		);
		parent::__construct();
	}

	function widget( $args, $instance ) {
		global $post;

		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];

		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$form_id = EDD_FES()->helper->get_option( 'fes-submission-form', false );

		if ( $form_id ) {
			$form = EDD_FES()->helper->get_form_by_id( $form_id, $post->ID );
			echo $form->display_fields();
		}

		echo $args['after_widget'];
	}

}
