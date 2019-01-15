<?php

class Marketify_Template_Pagination {

	public function __construct() {
		add_action( 'marketify_loop_after', array( $this, 'output' ) );
	}

	public function output() {
		the_posts_pagination( array(
			'prev_text' => __( 'Previous', 'marketify' ),
			'next_text' => __( 'Next', 'marketify' ),
		) );
	}

}
