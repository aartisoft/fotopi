<?php
/**
 * Taxonomy display on the homepage.
 *
 * @since Marketify 1.0
 */
class Marketify_Widget_Taxonomy_Stylized extends Marketify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'widget--home-taxonomy-stylized marketify_widget_taxonomy_stylized';
		$this->widget_description = __( 'Display a taxonomy in a styled way.', 'marketify' );
		$this->widget_id          = 'marketify_widget_taxonomy_stylized';
		$this->widget_name        = __( 'Marketify - Home: Taxonomy', 'marketify' );
		$this->settings           = array(
			'home-1' => array(
				'type' => 'widget-area',
				'std' => __( 'Home', 'marketify' ),
			),
			'taxonomy' => array(
				'type'  => 'select',
				'std'   => 'category',
				'label' => __( 'Taxonomy:', 'marketify' ),
				'options' => array(
					'download_category' => __( 'Category', 'marketify' ),
					'download_tag'      => __( 'Tag', 'marketify' ),
				),
			),
		);
		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		extract( $args );

		$taxonomy = isset( $instance['taxonomy'] ) ? $instance['taxonomy'] : 'download_category';

		echo str_replace( 'container', '', $before_widget );
		?>

		<div class="container">

			<ul class="taxonomy-stylized">
				<?php wp_list_categories( apply_filters( 'marketify_taxonomy_stylized_terms', array(
					'title_li' => '',
					'taxonomy' => $taxonomy,
					'depth'    => 1,
				) ) ); ?>
			</ul>

		</div>

		<?php
		echo $after_widget;
	}

}
