<?php

class Marketify_Widget_Featured_Popular_Downloads extends Marketify_Widget {

	public function __construct() {
		$this->has_featured = marketify()->get( 'edd-featured-downloads' );

		$this->widget_cssclass    = 'marketify_widget_featured_popular';
		$this->widget_description = sprintf( __( 'Display featured and popular %s in sliding grid.', 'marketify' ), edd_get_label_plural() );
		$this->widget_id          = 'marketify_widget_featured_popular';
		$this->widget_name        = sprintf( __( 'Marketify - Page:  Featured &amp; Popular %s', 'marketify' ), edd_get_label_plural() );
		$this->settings           = array(
			'popular-title' => array(
				'type'  => 'text',
				'std'   => 'Popular',
				'label' => __( 'Popular Title:', 'marketify' ),
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 6,
				'label' => __( 'Number to display:', 'marketify' ),
			),
			'scroll' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Automatically scroll items', 'marketify' ),
			),
			'speed' => array(
				'type'  => 'text',
				'std'   => 7000,
				'label' => __( 'Slideshow Speed (ms)', 'marketify' ),
			),
		);

		if ( $this->has_featured ) {
			$featured = array(
				'featured-title' => array(
					'type'  => 'text',
					'std'   => 'Featured',
					'label' => __( 'Featured Title:', 'marketify' ),
				),
			);

			$this->settings = array_merge( $featured, $this->settings );
		}

		$this->settings = array_reverse( $this->settings );

		$this->settings['home-1'] = array(
			'type' => 'widget-area',
			'std'  => __( 'Home', 'marketify' ),
		);

		$this->settings = array_reverse( $this->settings );

		parent::__construct();
	}

	function widget( $args, $instance ) {
		global $post;

		extract( $args );

		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 8;
		$f_title   = isset( $instance['featured-title'] ) ? $instance['featured-title'] : __( 'Featured', 'marketify' );
		$p_title   = isset( $instance['popular-title'] ) ? $instance['popular-title'] : __( 'Popular', 'marketify' );

		$auto      = isset( $instance['scroll'] ) && 1 == $instance['scroll'] ? 'true' : 'false';
		$speed     = isset( $instance['speed'] ) ? $instance['speed'] : 70000;

		echo $before_widget;
	?>
		<h3 class="featured-popular-switcher section-title">
			<?php if ( $this->has_featured ) : ?>
				<span data-tab="#items-featured"><?php echo esc_attr( $f_title ); ?> </span>
			<?php endif; ?>

			<span data-tab="#items-popular"><?php echo esc_attr( $p_title ); ?></span>
		</h3>

		<script>
			var marketifyFeaturedPopular = {
				autoPlay: <?php echo $auto; ?>,
				autoPlaySpeed: <?php echo $speed; ?>,
				isRTL: <?php echo is_rtl() ? 'true' : 'false'; ?>
			}
		</script>

		<div class="featured-popular-tabs">
			<?php if ( $this->has_featured ) : ?>
				<div id="items-featured" class="featured-popular-slick">
					<?php echo do_shortcode( "[downloads pagination=false number={$number} flat=true orderby=featured]" ); ?>
				</div>
			<?php endif; ?>

			<div id="items-popular" class="featured-popular-slick">
				<?php echo do_shortcode( "[downloads pagination=false number={$number} flat=true orderby=sales]" ); ?>
			</div>
		</div>

	<?php
		echo $after_widget;
	}

}
