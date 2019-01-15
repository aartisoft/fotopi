<?php

class Marketify_EDD_Widgets {

	public function __construct() {
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'widgets_init', array( $this, 'register_sidebars' ), 20 );
	}

	public function register_widgets() {
		register_widget( 'Marketify_Widget_Recent_Downloads' );
		register_widget( 'Marketify_Widget_Curated_Downloads' );
		register_widget( 'Marketify_Widget_Featured_Popular_Downloads' );
		register_widget( 'Marketify_Widget_Taxonomy_Stylized' );

		register_widget( 'Marketify_Widget_Download_Archive_Sorting' );

		register_widget( 'Marketify_Widget_Download_Details' );

		if ( apply_filters( 'marketify_edd_product_details_widget', true ) ) {
			unregister_widget( 'edd_product_details_widget' );
		}
	}

	public function register_sidebars() {
		register_sidebar( array(
			'name'          => __( 'Shop Sidebar', 'marketify' ),
			'id'            => 'sidebar-download',
			'before_widget' => '<aside id="%1$s" class="widget widget--download-archive %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title widget-title--download-archive">',
			'after_title'   => '</h3>',
		) );

		register_sidebar( array(
			'name'          => sprintf( __( '%s Sidebar', 'marketify' ), edd_get_label_singular() ),
			'id'            => 'sidebar-download-single',
			'before_widget' => '<aside id="%1$s" class="widget widget--download-single %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title widget-title--download-single">',
			'after_title'   => '</h3>',
		) );

		register_sidebar( array(
			'name'          => sprintf( __( '%s Comments Sidebar', 'marketify' ), edd_get_label_singular() ),
			'id'            => 'sidebar-download-single-comments',
			'before_widget' => '<aside id="%1$s" class="widget widget--download-single widget--download-single-comments %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title widget-title--download-single-comments">',
			'after_title'   => '</h3>',
		) );
	}

}
