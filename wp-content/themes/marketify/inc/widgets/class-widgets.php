<?php

class Marketify_Widgets {

	public function __construct() {
		$widgets = array(
			'class-widget-blog-posts.php',
			'class-widget-price-option.php',
			'class-widget-price-table.php',
			'class-widget-slider.php',
			'class-widget-feature-callout.php',
			'class-widget-footer-social-icons.php',
			'class-widget-footer-contact.php',
			'class-widget-footer-copyright.php',
		);

		foreach ( $widgets as $widget ) {
			require_once( dirname( __FILE__ ) . '/_widgets/' . $widget );
		}

		add_filter( 'widget_text', 'do_shortcode' );
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'widgets_init', array( $this, 'register_sidebars' ) );
	}

	function register_widgets() {
		register_widget( 'Marketify_Widget_Slider' );
		register_widget( 'Marketify_Widget_Price_Table' );
		register_widget( 'Marketify_Widget_Price_Option' );
		register_widget( 'Marketify_Widget_Recent_Posts' );
		register_widget( 'Marketify_Widget_Feature_Callout' );
		register_widget( 'Marketify_Widget_Footer_Social_Icons' );
		register_widget( 'Marketify_Widget_Footer_Contact' );
		register_widget( 'Marketify_Widget_Footer_Copyright' );
	}

	public function register_sidebars() {
		register_sidebar( array(
			'name'          => __( 'Sidebar', 'marketify' ),
			'id'            => 'sidebar-1',
			'before_widget' => '<aside id="%1$s" class="widget widget--blog %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title widget-title--blog"><span>',
			'after_title'   => '</span></h3>',
		) );

		register_sidebar( array(
			'name'          => __( 'Home', 'marketify' ),
			'description'   => __( 'Widgets that appear on the "Page: Home" Page Template', 'marketify' ),
			'id'            => 'home-1',
			'before_widget' => '<aside id="%1$s" class="widget widget--home container %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title widget-title--home section-title"><span>',
			'after_title'   => '</span></h3>',
		) );

		/* Footer */
		register_sidebar( array(
			'name'          => __( 'Footer Left', 'marketify' ),
			'description'   => __( 'The left footer widget area', 'marketify' ),
			'id'            => 'footer-1',
			'before_widget' => '<aside id="%1$s" class="%2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title widget-title--site-footer">',
			'after_title'   => '</h3>',
		) );

		register_sidebar( array(
			'name'          => __( 'Footer Center', 'marketify' ),
			'description'   => __( 'The center footer widget area', 'marketify' ),
			'id'            => 'footer-2',
			'before_widget' => '<aside id="%1$s" class="%2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title widget-title--site-footer">',
			'after_title'   => '</h3>',
		) );

		register_sidebar( array(
			'name'          => __( 'Footer Right', 'marketify' ),
			'description'   => __( 'The right footer widget area', 'marketify' ),
			'id'            => 'footer-3',
			'before_widget' => '<aside id="%1$s" class="%2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title widget-title--site-footer">',
			'after_title'   => '</h3>',
		) );

		/* Price Table */
		register_sidebar( array(
			'name'          => __( 'Price Table', 'marketify' ),
			'id'            => 'widget-area-price-options',
			'description'   => __( 'Drag multiple "Price Option" widgets here. Then drag the "Pricing Table" widget to the "Homepage" Widget Area.', 'marketify' ),
			'before_widget' => '<div id="%1$s" class="widget pricing-table-widget %2$s col-xs-12 col-sm-6 col-md-4">',
			'after_widget'  => '</div>',
		) );
	}

}
