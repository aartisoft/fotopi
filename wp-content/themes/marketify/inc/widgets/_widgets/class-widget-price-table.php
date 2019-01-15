<?php

class Marketify_Widget_Price_Table extends Marketify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'marketify_widget_price_table';
		$this->widget_description = __( 'Output the price table (based on the "Price Table" widget area)', 'marketify' );
		$this->widget_id          = 'marketify_widget_price_table';
		$this->widget_name        = __( 'Marketify - Page: Price Table', 'marketify' );
		$this->settings           = array(
			'home-1' => array(
				'type' => 'widget-area',
				'std' => __( 'Home', 'marketify' ),
			),
			'title' => array(
				'type'  => 'text',
				'std'   => 'Pricing Options',
				'label' => __( 'Title:', 'marketify' ),
			),
			'nothing' => array(
				'type' => 'description',
				'std'  => __( 'Drag "Price Option" widgets to the "Price Table" widget area to populate this widget.', 'marketify' ),
			),
		);

		parent::__construct();
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		$the_sidebars = wp_get_sidebars_widgets();

		if ( ! isset( $the_sidebars['widget-area-price-options'] ) ) {
			return _e( 'Add price options in "Appearance > Widgets"', 'marketify' );
		}

		$widget_count = count( $the_sidebars['widget-area-price-options'] );

		echo $before_widget;

		if ( $title ) {
			echo $before_title . esc_attr( $title ) . $after_title;
		}
		?>

		<div class="pricing-table-widget-<?php echo $widget_count; ?> row">
			<?php dynamic_sidebar( 'widget-area-price-options' ); ?>
		</div>

		<?php
		echo $after_widget;
	}

}
