<?php

class Marketify_Widget_Price_Option extends Marketify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'marketify_widget_price_option';
		$this->widget_description = __( 'Create a price option for the pricing table.', 'marketify' );
		$this->widget_id          = 'marketify_widget_price_option';
		$this->widget_name        = __( 'Marketify - Page: Price Option', 'marketify' );
		$this->settings           = array(
			'color' => array(
				'type'  => 'colorpicker',
				'std'   => '#515a63',
				'label' => __( 'Background Color:', 'marketify' ),
			),
			'description' => array(
				'type'  => 'textarea',
				'rows'  => 8,
				'std'   => '',
				'label' => __( 'Description:', 'marketify' ),
			),
			'nothing' => array(
				'type' => 'description',
				'std'  => __( 'Use <code>h2</code> tags for large titles, and <code>h3</code> tags for subtitles. Unordered lists create a great look.', 'marketify' ),
			),
		);
		$this->control_ops = array(
			'width'  => 300,
		);

		parent::__construct();
	}

	function widget( $args, $instance ) {
		extract( $args );

		$color       = isset( $instance['color'] ) ? $instance['color'] : get_theme_mod( 'color-page-header-background', '#515a63' );
		$description = isset( $instance['description'] ) ? $instance['description'] : '';

		echo $before_widget;
		?>
		<div class="pricing-table-option" style="background-color: <?php echo $color; ?>">
			<div class="pricing-table-widget-description">
				<?php echo wpautop( apply_filters( 'marketify_price_option_description', $description ) ); ?>
			</div>
		</div>
		<?php
		echo $after_widget;
	}

}
