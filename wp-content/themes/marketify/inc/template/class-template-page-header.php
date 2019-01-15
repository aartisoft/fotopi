<?php

class Marketify_Template_Page_Header {

	/**
	 * The HTML tag to use for the page title.
	 *
	 * @since 2.9.0
	 * @var $tag string
	 */
	protected $tag;

	public function __construct() {
		$this->tag = apply_filters( 'marketify_page_header_tag', 'h2' );

		add_filter( 'marketify_page_header', array( $this, 'tag_atts' ) );
		add_action( 'marketify_entry_before', array( $this, 'close_div' ), 10 ); // close the .page-header div opened in each callback
		add_action( 'marketify_entry_before', array( $this, 'close_div' ), 20 ); // close the .header-outer div opened in header.php

		add_filter( 'get_the_archive_title', array( $this, 'get_the_archive_title' ) );

		add_action( 'marketify_entry_before', array( $this, 'archive_title' ), 5 );
		add_action( 'marketify_entry_before', array( $this, 'page_title' ), 5 );
		add_action( 'marketify_entry_before', array( $this, 'attachment_title' ), 5 );
		add_action( 'marketify_entry_before', array( $this, 'post_title' ), 5 );
		add_action( 'marketify_entry_before', array( $this, 'home_title' ), 5 );
		add_action( 'marketify_entry_before', array( $this, 'blog_title' ), 5 );
		add_action( 'marketify_entry_before', array( $this, 'not_found_title' ), 5 );
	}

	public function close_div() {
		echo '</div>';
	}

	/**
	 * Get the tag to wrap page headers with.
	 *
	 * @since 2.9.0
	 *
	 * @return string $tag
	 */
	public function get_tag() {
		return esc_attr( $this->tag );
	}

	public function blog_title() {
		if ( ! is_home() ) {
			return;
		}
?>
<div class="page-header container">
	<<?php echo $this->get_tag(); ?> class="page-title"><?php echo get_option( 'page_for_posts' ) ? get_the_title( get_option( 'page_for_posts' ) ) : __( 'Blog', 'marketify' ); ?></<?php echo $this->get_tag(); ?>>
<?php
	}

	public function home_title() {
		if ( ! is_front_page() || is_home() ) {
			return;
		}

		if ( is_page_template( 'page-templates/home-search.php' ) ) {
?>
<div class="page-header__search">
<?php
	add_filter( 'get_search_form', array( marketify()->template->header, 'search_form' ) );
	get_search_form();
	remove_filter( 'get_search_form', array( marketify()->template->header, 'search_form' ) );
?>
</div>
<?php
		}

		the_post();
		the_content();
		rewind_posts();
	}

	public function page_title() {
		if ( ! is_singular( 'page' ) ) {
			return;
		}

		the_post();
?>
<div class="page-header page-header--singular container">
	<<?php echo $this->get_tag(); ?> class="page-title"><?php the_title(); ?></<?php echo $this->get_tag(); ?>>
<?php
		rewind_posts();
	}

	/**
	 * Page title for attachments.
	 *
	 * @since 2.7.0
	 *
	 * @return void
	 */
	public function attachment_title() {
		if ( ! is_attachment() ) {
			return;
		}

		the_post();

		$post = get_post();
?>
<div class="page-header page-header--singular container">
	<<?php echo $this->get_tag(); ?> class="page-title"><a href="<?php echo esc_url( get_permalink( $post->post_parent ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'Return to %s', 'marketify' ), strip_tags( get_the_title( $post->post_parent ) ) ) ); ?>" rel="gallery"><?php
		/* translators: %s - title of parent post */
		printf( __( '<span class="meta-nav">&larr;</span> %s', 'marketify' ), get_the_title( $post->post_parent ) );
	?></a></<?php echo $this->get_tag(); ?>>
<?php
		rewind_posts();
	}

	public function archive_title() {
		if ( ! is_archive() ) {
			return;
		}
?>
<div class="page-header container">
	<<?php echo $this->get_tag(); ?> class="page-title"><?php the_archive_title(); ?></<?php echo $this->get_tag(); ?>>
<?php
	}

	public function get_the_archive_title( $title ) {
		if ( is_search() ) {
			$title = get_search_query();
		} elseif ( is_post_type_archive( 'download' ) ) {
			$title = edd_get_label_plural();
		} elseif ( is_tax() ) {
			$title = single_term_title( '', false );

			if ( did_action( 'marketify_downloads_before' ) ) {
				$title = sprintf( __( 'All %s', 'marketify' ), $title );
			}
		} elseif ( function_exists( 'fes_get_vendor' ) && fes_get_vendor() ) {
			$title = sprintf( __( 'Recent %s', 'marketify' ), edd_get_label_plural() );
		}

		return $title;
	}

	public function post_title() {
		if ( ! is_singular( 'post' ) ) {
			return;
		}

		the_post();

		$social = marketify()->template->entry->social_profiles();
?>
<div class="page-header page-header--single container">
	<<?php echo $this->get_tag(); ?> class="page-title"><?php the_title(); ?></<?php echo $this->get_tag(); ?>>

	<div class="page-header__entry-meta page-header__entry-meta--date entry-meta entry-meta--hentry">
		<?php get_template_part( 'content', 'entry-meta' ); ?>
	</div>
<?php
	rewind_posts();
	}

	public function not_found_title() {
		if ( ! is_404() ) {
			return;
		}
?>
<div class="page-header page-header--singular container">
	<<?php echo $this->get_tag(); ?> class="page-title"><?php _e( 'Oops! That page can&rsquo;t be found.', 'marketify' ); ?></<?php echo $this->get_tag(); ?>>
<?php
	}

	/**
	 * Add the HTML attributes to the wrapper div around the header elements
	 */
	public function tag_atts( $args = array() ) {
		$defaults = apply_filters( 'marketify_page_header_defaults', array(
			'class' => array( 'header-outer' ),
			'object_ids' => false,
			'size' => 'full',
			'style' => array(),
		) );

		$args = wp_parse_args( $args, $defaults );
		$atts = $this->build_tag_atts( $args );

		$output = '';

		foreach ( $atts as $attribute => $properties ) {
			$output .= sprintf( '%s="%s"', $attribute, implode( ' ', $properties ) );
		}

		return $output;
	}

	public function build_tag_atts( $args ) {
		$args = $this->add_background_image( $args );
		$args = $this->add_background_video( $args );

		$allowed = apply_filters( 'marketify_page_header_allowed_atts', array( 'class', 'style' ) );
		$atts = apply_filters( 'marketify_page_header_atts', $args );

		$atts = array_intersect_key( $atts, array_flip( $allowed ) );

		return $atts;
	}

	public function add_background_image( $args ) {
		$background_image = $this->find_background_image( $args );

		if ( $background_image ) {
			$args['style'][] = 'background-image:url(' . $background_image . ');';
			$args['class'][] = 'has-image';
		} else {
			$args['class'][] = 'no-image';
		}

		return $args;
	}

	public function add_background_video( $args ) {
		$video = true;

		if ( $video ) {
			$args['class'][] = 'has-video';
		}

		return $args;
	}

	public function find_background_image( $args ) {
		$background_image = false;
		$format_style_is_background = false;

		if ( marketify()->get( 'edd' ) ) {
			$format_style_is_background = marketify()->get( 'edd' )->template->download->is_format_style( 'background' );
		}

		if (
			is_singular( array( 'post', 'page' ) ) ||
			is_home() ||
			( is_singular( 'download' ) && $format_style_is_background )
		) {
			$id = get_post_thumbnail_id();

			if ( is_home() ) {
				$id = get_post_thumbnail_id( get_option( 'page_for_posts' ) );
			}

			$background_image = wp_get_attachment_image_src( $id, $args['size'] );
			$background_image = $background_image[0];
		}

		return apply_filters( 'marketify_page_header_image', $background_image, $args );
	}

}
