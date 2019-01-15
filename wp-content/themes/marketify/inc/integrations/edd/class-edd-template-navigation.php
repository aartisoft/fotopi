<?php

class Marketify_EDD_Template_Navigation {

	public function __construct() {
		add_filter( 'wp_nav_menu_items', array( $this, 'add_cart_item' ), 10, 2 );
	}

	public function add_cart_item( $items, $args ) {
		if ( 'primary' != $args->theme_location ) {
			return $items;
		}

		ob_start();

		$widget_args = array(
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '',
			'after_title'   => '',
		);

		$widget = the_widget( 'edd_cart_widget', array(
			'title' => '',
		), $widget_args );

		$widget = ob_get_clean();

		$link = sprintf( '
            <li class="current-cart menu-item menu-item-has-children">
                <a href="%1$s"><span class="edd-cart-quantity">%2$d</span></a>
                <a href="%1$s"><span class="edd-checkout-link">' . __( 'Checkout', 'marketify' ) . '</span></a>
                <ul class="sub-menu nav-menu"><li class="widget">%3$s</li></ul>
            </li>',
			esc_url( get_permalink( edd_get_option( 'purchase_page' ) ) ),
			edd_get_cart_quantity(),
			$widget
		);

		if ( apply_filters( 'marketify_nav_menu_cart_icon_left', true ) ) {
			return $link . $items;
		} else {
			return $items . $link;
		}
	}

}
