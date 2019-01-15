<?php

class Marketify_Widget_Download_Details extends Marketify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'marketify-widget--download-single-details';
		$this->widget_description = __( 'Display information related to the current download', 'marketify' );
		$this->widget_id          = 'marketify_widget_download_details';
		$this->widget_name        = sprintf( __( 'Marketify - %1$s: About', 'marketify' ), edd_get_label_singular() );
		$this->settings           = array(
			'sidebar-download-single' => array(
				'type' => 'widget-area',
				'std'  => sprintf( __( '%s Sidebar', 'marketify' ), edd_get_label_singular() )
			),
			'title' => array(
				'type'  => 'text',
				'std'   => 'Product Details',
				'label' => __( 'Title:', 'marketify' )
			),
			'purchase-count' => array(
				'type'  => 'checkbox',
				'std'   => '',
				'label' => __( 'Hide purchase count', 'marketify' )
			)
		);

		parent::__construct();

		add_action( 'marketify_product_details_after', array( $this, 'inline_extra_details' ) );
	}

	function widget( $args, $instance ) {
		global $post;

		if ( ! $post->post_author ) {
			return;
		}

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance[ 'title' ] : '', $instance, $this->id_base );
		$count = isset( $instance[ 'purchase-count' ] ) && 1 == $instance[ 'purchase-count' ] ? false : true;

		$user = new WP_User( $post->post_author );
		$url = esc_url( marketify()->get( 'edd' )->template->author_url( $user->ID ) );

		echo $args[ 'before_widget' ];

		if ( $title ) {
			$args[ 'before_title' ] = '<h3 class="section-title widget-title"><span>';
			$args[ 'after_title' ]  = '</span></h3>';

			echo $args[ 'before_title' ] . $title . $args[ 'after_title' ];
		}

		do_action( 'marketify_product_details_widget_before', $instance );
?>

		<div class="widget-download-details">
			<?php do_action( 'marketify_product_details_before', $instance ); ?>

			<div class="widget-detail widget-detail--author">
				<?php do_action( 'marketify_download_author_before' ); ?>

				<?php printf(  '<a class="author-avatar" href="%s" rel="author">%s</a>', $url, get_avatar( $user->ID, 130 ) ); ?>
				<?php printf( '<a class="author-link" href="%s" rel="author">%s</a>', $url, $user->display_name ); ?>

		<span class="widget-detail__info"><?php 
		printf( 
			__( 'Author since: %s', 'marketify' ), 
			date_i18n( get_option( 'date_format' ), strtotime( $user->user_registered ) )
		);
?></span>
				<?php do_action( 'marketify_download_author_after' ); ?>
			</div>

			<?php if ( $count ) : ?>
			<div class="widget-detail<?php if ( comments_open() ) : ?> widget-detail--half<?php endif; ?>">
				<strong class="widget-detail__title"><?php echo edd_get_download_sales_stats( get_the_ID() ); ?></strong>
				<span class="widget-detail__info"><?php echo _n( 'Purchase', 'Purchases', edd_get_download_sales_stats( get_the_ID() ), 'marketify' ); ?></span>
			</div>
			<?php endif; ?>

			<?php if ( comments_open() ) : ?>
			<div class="widget-detail <?php if ( $count ) : ?>widget-detail--half widget-detail--last<?php endif; ?>">
				<a href="#comments"><strong class="widget-detail__title"><?php echo get_comments_number(); ?></strong>
				<span class="widget-detail__info"><?php echo _n( 'Comment', 'Comments', get_comments_number(), 'marketify' ); ?></a></span>
			</div>
			<?php endif; ?>

			<?php do_action( 'marketify_product_details_after', $instance ); ?>
		</div>
<?php

		do_action( 'marketify_product_details_widget_after', $instance );

		echo $args[ 'after_widget' ];
	}

	function inline_extra_details() {
		if ( 'top' == esc_attr( get_theme_mod( 'download-' . ( get_post_format() ? get_post_format() : 'standard' ) . '-feature-area', 'top' ) ) ) {
			return;
		}
?>
		<div class="widget-detail">
			<?php do_action( 'marketify_download_info' ); ?>
		</div>

		<div class="widget-detail">
			<?php do_action( 'marketify_download_actions' ); ?>
		</div>
<?php
	}

}
