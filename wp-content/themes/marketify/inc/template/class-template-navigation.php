<?php

class Marketify_Template_Navigation {

	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'register_menus' ) );
		add_filter( 'wp_nav_menu_items', array( $this, 'add_search_item' ), 11, 2 );
	}

	public function add_search_item( $items, $args ) {
		if ( 'primary' != $args->theme_location ) {
			return $items;
		}

		add_filter( 'get_search_form', array( marketify()->template->header, 'search_form' ) );
		$searchform = get_search_form( false );
		remove_filter( 'get_search_form', array( marketify()->template->header, 'search_form' ) );

		$link = sprintf( '<li class="nav-menu-search"><a href="#" class="js-toggle-search"><span class="screen-reader-text">%s</span></a>%s</li>', __( 'Search', 'marketify' ), $searchform );

		if ( apply_filters( 'marketify_nav_menu_search_icon_left', true ) ) {
			return $link . $items;
		} else {
			return $items . $link;
		}
	}

	public function register_menus() {
		register_nav_menus( array(
			'primary' => __( 'Primary Menu', 'marketify' ),
			'social'  => __( 'Footer Social', 'marketify' ),
		) );
	}

	public function get_theme_menu( $theme_location ) {
		$theme_locations = get_nav_menu_locations();

		if ( ! isset( $theme_locations[ $theme_location ] ) ) {
			return false;
		}

		$menu_obj = get_term( $theme_locations[ $theme_location ], 'nav_menu' );

		if ( ! $menu_obj ) {
			return false;
		}

		return $menu_obj;
	}

	public function get_theme_menu_name( $theme_location ) {
		$menu_obj = $this->get_theme_menu( $theme_location );
		$default  = _x( 'Menu', 'noun', 'marketify' );

		if ( ! $menu_obj ) {
			return $default;
		}

		if ( ! isset( $menu_obj->name ) ) {
			return $default;
		}

		return $menu_obj->name;
	}

}
