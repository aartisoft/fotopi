<?php

class Marketify_EDD_Popular {

	public function __construct() {
		$this->slug = _x( 'popular', 'URL slug to determine category sorting', 'marketify' );

		add_action( 'marketify_downloads_before', array( $this, 'archive_section_title' ) );
		add_filter( 'marketify_get_the_archive_title', array( $this, 'marketify_get_the_archive_title' ) );
		add_filter( 'get_the_archive_title', array( $this, 'get_the_archive_title' ) );

		add_action( 'init', array( $this, 'endpoint' ) );
		add_filter( 'edd_download_category_args', array( $this, 'category_args' ) );
		add_action( 'template_redirect', array( $this, 'filter_query' ) );

		add_filter( 'term_link', array( $this, 'term_link' ) );
	}

	public function term_link( $termlink ) {
		if ( ! ( $this->is_popular_query() || is_page_template( 'page-templates/popular.php' ) ) ) {
			return $termlink;
		}

		return esc_url( $termlink . trailingslashit( $this->slug ) );
	}

	public function show_popular() {
		if ( 'on' != get_theme_mod( 'downloads-archives-popular', 'on' ) ) {
			return false;
		}

		if ( $this->is_popular_query() ) {
			return false;
		}

		if ( get_query_var( 'm-order' ) ) {
			return false;
		}

		if ( is_page_template( 'page-templates/popular.php' ) ) {
			return false;
		}

		return true;
	}

	public function is_popular_query() {
		global $wp_query;

		if ( isset( $wp_query->query_vars[ $this->slug ] ) ) {
			return true;
		}

		return false;
	}

	public function filter_query() {
		if ( ! $this->is_popular_query() ) {
			return;
		}

		add_filter( 'edd_downloads_query', array( $this, 'edd_downloads_query' ), 10, 2 );
	}

	public function edd_downloads_query( $query, $atts ) {
		$query['meta_key'] = '_edd_download_sales';
		$query['orderby']  = 'meta_value_num';

		return $query;
	}

	public function category_args( $args ) {
		$args['rewrite']['ep_mask'] = EP_CATEGORIES;

		return $args;
	}

	public function endpoint() {
		add_rewrite_endpoint( $this->slug, EP_CATEGORIES );
	}

	public function marketify_get_the_archive_title( $title ) {
		if ( is_search() ) {
			$title = sprintf( _x( 'Popular in "%s"', 'popular search results', 'marketify' ), get_search_query() );
		} elseif ( is_tax( array( 'download_tag', 'download_category' ) ) ) {
			$title = sprintf( __( 'Popular in %s', 'marketify' ), single_term_title( '', false ) );
		} elseif ( is_post_type_archive( 'download' ) || is_page_template( 'page-templates/shop.php' ) ) {
			$title = sprintf( __( 'Popular %s', 'marketify' ), edd_get_label_plural() );
		}

		return $title;
	}

	public function get_the_archive_title( $title ) {
		if ( $this->is_popular_query() ) {
			$title = sprintf( __( 'Popular in %s', 'marketify' ), single_term_title( '', false ) );
		} elseif ( is_page_template( 'page-templates/shop.php' ) ) {
			$title = edd_get_label_plural();
		}

		return $title;
	}

	public function archive_section_title() {
		if ( ! $this->show_popular() ) {
			return;
		}
	?>
		<div class="section-title"><span>
			<?php the_archive_title(); ?>
		</span></div>
	<?php
	}

}
