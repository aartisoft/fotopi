<?php
/**
 * Home: Feature Callout
 *
 * @since Marketify 2.0.0
 */
class Marketify_Widget_Feature_Callout extends Marketify_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_description = __( 'Display a full-width "feature" callout', 'marketify' );
		$this->widget_id          = 'marketify_widget_feature_callout';
		$this->widget_name        = __( 'Marketify - Page: Feature Callout', 'marketify' );
		$this->control_ops        = array(
			'width' => 400,
		);
		$this->settings           = array(
			'text_align' => array(
				'type'  => 'select',
				'std'   => 'left',
				'label' => __( 'Text Align:', 'marketify' ),
				'options' => array(
					'left' => __( 'Left', 'marketify' ),
					'right' => __( 'Right', 'marketify' ),
					'center' => __( 'Center (cover only)', 'marketify' ),
				),
			),
			'background' => array(
				'type'  => 'select',
				'std'   => 'pull',
				'label' => __( 'Image Style:', 'marketify' ),
				'options' => array(
					'cover' => __( 'Cover', 'marketify' ),
					'pull'  => __( 'Pull Out', 'marketify' ),
				),
			),
			'background_position' => array(
				'type'  => 'select',
				'std'   => 'center center',
				'label' => __( 'Image Position:', 'marketify' ),
				'options' => array(
					'left top' => __( 'Left Top', 'marketify' ),
					'left center' => __( 'Left Center', 'marketify' ),
					'left bottom' => __( 'Left Bottom', 'marketify' ),
					'right top' => __( 'Right Top', 'marketify' ),
					'right center' => __( 'Right Center', 'marketify' ),
					'right bottom' => __( 'Right Bottom', 'marketify' ),
					'center top' => __( 'Center Top', 'marketify' ),
					'center center' => __( 'Center Center', 'marketify' ),
					'center bottom' => __( 'Center Bottom', 'marketify' ),
					'center top' => __( 'Center Top', 'marketify' ),
				),
			),
			'cover_overlay' => array(
				'type' => 'checkbox',
				'std'  => 1,
				'label' => __( 'Use transparent overlay', 'marketify' ),
			),
			'margin' => array(
				'type' => 'checkbox',
				'std'  => 1,
				'label' => __( 'Add standard spacing above/below widget', 'marketify' ),
			),
			'text_color' => array(
				'type'  => 'colorpicker',
				'std'   => '#515a63',
				'label' => __( 'Text Color:', 'marketify' ),
			),
			'background_color' => array(
				'type'  => 'colorpicker',
				'std'   => '#f9f9f9',
				'label' => __( 'Background Color:', 'marketify' ),
			),
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title:', 'marketify' ),
			),
			'content' => array(
				'type'  => 'textarea',
				'std'   => '',
				'label' => __( 'Content:', 'marketify' ),
				'rows'  => 5,
			),
			'image' => array(
				'type'  => 'image',
				'std'   => '',
				'label' => __( 'Image:', 'marketify' ),
			),
		);

		parent::__construct();
	}

	function widget( $args, $instance ) {
		extract( $args );

		$text_align = isset( $instance['text_align'] ) ? esc_attr( $instance['text_align'] ) : 'left';
		$background = isset( $instance['background'] ) ? esc_attr( $instance['background'] ) : 'cover';
		$background_color = isset( $instance['background_color'] ) ? esc_attr( $instance['background_color'] ) : '#ffffff';
		$background_position = isset( $instance['background_position'] ) ? esc_attr( $instance['background_position'] ) : 'center center';
		$overlay = isset( $instance['cover_overlay'] ) && 1 == $instance['cover_overlay'] ? 'has-overlay' : 'no-overlay';
		$margin = isset( $instance['margin'] ) && 1 == $instance['margin'] ? true : false;

		if ( ! $margin ) {
			$before_widget = str_replace( 'home-widget', 'home-widget no-margin', $before_widget );
		}

		$image = isset( $instance['image'] ) ? esc_url( $instance['image'] ) : null;
		$content = $this->assemble_content( $instance );

		echo str_replace( 'container', '', $before_widget );
		?>

		<div class="feature-callout text-<?php echo $text_align; ?> image-<?php echo $background; ?>" style="background-color: <?php echo $background_color; ?>;">

			<?php if ( 'pull' == $background ) : ?>
				<div class="container">
					<div class="col-xs-12 col-sm-6 <?php echo ( 'right' == $text_align ) ? 'offset-sm-6' : ''; ?>">
						<?php echo $content; ?>
					</div>
				</div>

				<div class="col-xs-12 col-sm-6 <?php echo ( 'left' == $text_align ) ? 'offset-sm-6' : ''; ?> feature-callout-image-pull" style="background-image:url(<?php echo $image; ?>); ?>; background-position: <?php echo $background_position; ?>"></div>
			<?php else : ?>

				<div class="feature-callout-cover <?php echo $overlay; ?>" style="background-image:url(<?php echo $image; ?>); ?>; background-position: <?php echo $background_position; ?>">

					<div class="container">
						<div class="row">
							<div class="col-xs-12 <?php echo ( in_array( $text_align, array( 'left', 'right' ) ) ) ? 'col-sm-8 col-md-6' : ''; ?> <?php echo ( 'right' == $text_align ) ? 'offset-sm-4 offset-md-6' : ''; ?>">
								<?php echo $content; ?>
							</div>
						</div>
					</div>

				</div>

			<?php endif; ?>

		</div>

	<?php
		echo $after_widget;
	}

	private function assemble_content( $instance ) {
		$text_color = isset( $instance['text_color'] ) ? esc_attr( $instance['text_color'] ) : '#717A8F';

		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$content = isset( $instance['content'] ) ? $instance['content'] : '';
		$content = do_shortcode( $content );

		$output  = '<div class="callout-feature-content" style="color:' . $text_color . '">';
		$output .= '<h2 class="callout-feature-title" style="color:' . $text_color . '">' . $title . '</h2>';
		$output .= wpautop( $content );
		$output .= '</div>';

		return $output;
	}

}
