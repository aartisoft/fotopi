<?php

class Marketify_EDD_FES_Widgets {

	public function __construct() {
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'widgets_init', array( $this, 'register_sidebars' ), 20 );
	}

	public function register_widgets() {
		register_widget( 'Marketify_Widget_FES_Vendor' );
		register_widget( 'Marketify_Widget_FES_Vendor_Description' );
		register_widget( 'Marketify_Widget_FES_Vendor_Contact' );
		register_widget( 'Marketify_Widget_FES_Product_Details' );
	}

	public function register_sidebars() {
		register_sidebar( array(
			'name'          => __( 'Vendor Sidebar', 'marketify' ),
			'id'            => 'sidebar-vendor',
			'before_widget' => '<aside id="%1$s" class="widget widget--vendor-profile widget-detail %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title widget-title--vendor-profile">',
			'after_title'   => '</h3>',
		) );
	}

}
