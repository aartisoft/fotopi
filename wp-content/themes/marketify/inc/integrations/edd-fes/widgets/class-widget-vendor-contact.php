<?php

class Marketify_Widget_FES_Vendor_Contact extends Marketify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'widget--vendor-profile-contact marketify_widget_fes_vendor_contact';
		$this->widget_description = __( 'Display the vendor contact form.', 'marketify' );
		$this->widget_id          = 'marketify_widget_fes_vendor_contact';
		$this->widget_name        = __( 'Marketify - Vendor: Contact', 'marketify' );
		$this->settings           = array(
			'widget-areas' => array(
				'type' => 'widget-area',
				'std'  => array( __( 'Vendor Sidebar', 'marketify' ), __( 'Download Sidebar', 'marketify' ) ),
				'sidebars' => array( 'sidebar-vendor', 'sidebar-download-single' ),
			),
			'extras' => array(
				'type'  => 'description',
				'std' => __( 'This widget has no options.', 'marketify' ),
			),
		);
		parent::__construct();
	}

	function widget( $args, $instance ) {
		$vendor = new Marketify_EDD_FES_Vendor( fes_get_vendor() );
		echo $args['before_widget'];
		echo do_shortcode( '[fes_vendor_contact_form id="' . $vendor->ID . '"]' );
		echo $args['after_widget'];
	}

}
