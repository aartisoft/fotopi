<?php

class Marketify_Widget_Recent_Posts extends Marketify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'marketify_widget_recent_posts';
		$this->widget_description = __( 'Display recent blog posts.', 'marketify' );
		$this->widget_id          = 'marketify_widget_recent_posts';
		$this->widget_name        = __( 'Marketify - Page: Recent Posts', 'marketify' );
		$this->settings           = array(
			'home-1' => array(
				'type' => 'widget-area',
				'std' => __( 'Home', 'marketify' ),
			),
			'title' => array(
				'type'  => 'text',
				'std'   => 'Recent Posts',
				'label' => __( 'Title:', 'marketify' ),
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 4,
				'label' => __( 'Number to display:', 'marketify' ),
			),
			'style' => array(
				'type'  => 'select',
				'std'   => 'classic',
				'options' => array(
					'classic' => __( 'Classic', 'marketify' ),
					'grid'    => __( 'Grid', 'marketify' ),
				),
				'label' => __( 'Display Style:', 'marketify' ),
			),
		);
		parent::__construct();
	}

	function widget( $args, $instance ) {
		global $post;

		extract( $args );

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		$number       = isset( $instance['number'] ) ? absint( $instance['number'] ) : 8;
		$style        = isset( $instance['style'] ) ? $instance['style'] : 'classic';

		$posts = new WP_Query( array(
			'post_type'              => 'post',
			'posts_per_page'         => $number,
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'cache_results'          => false,
		) );

		if ( ! $posts->have_posts() ) {
			return;
		}

		global $more;

		$more = 0;

		echo $before_widget;

		if ( $title ) {
			echo $before_title . esc_attr( $title ) . $after_title;
		}
?>
		<div class="widget--blog-posts <?php echo 'grid' == $style ? 'widget--blog-posts-grid' : 'widget--blog-posts-list' ?>">
			<?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
				<?php get_template_part( 'content', 'grid' == $style ? 'grid' : get_post_format() ); ?>
			<?php endwhile; ?>
		</div>
<?php
		echo $after_widget;
	}

}
