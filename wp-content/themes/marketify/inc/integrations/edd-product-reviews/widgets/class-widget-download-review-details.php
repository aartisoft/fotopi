<?php

class Marketify_Widget_Download_Review_Details extends Marketify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'marketify_widget_download_review_details';
		$this->widget_description = __( 'Display average review information.', 'marketify' );
		$this->widget_id          = 'marketify_widget_download_review_details';
		$this->widget_name        = sprintf( __( 'Marketify - %1$s: Review Details', 'marketify' ), function_exists( 'edd_get_label_singular' ) ? edd_get_label_singular() : 'Download' );
		$this->settings           = array(
			'title' => array(
				'type'  => 'text',
				'std'   => 'Review Details',
				'label' => __( 'Title:', 'marketify' ),
			),
		);
		parent::__construct();
	}
	function widget( $args, $instance ) {
		global $post;

		extract( $args );

		$reviews = get_comments( apply_filters( 'edd_reviews_average_rating_query_args', array(
			'post_id' => $post->ID,
		) ) );

		$total_ratings = 0;

		foreach ( $reviews as $review ) {
			$rating = get_comment_meta( $review->comment_ID, 'edd_rating', true );
			$total_ratings += $rating;
		}

		$total = wp_count_comments( $post->ID )->total_comments;

		if ( 0 == $total ) {
			$total = 1;
		}

		$average = $total_ratings / $total;

		$title   = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$reviews = edd_reviews();

		echo $before_widget;
		echo $reviews->review_breakdown();
		echo $after_widget;
	}

}
