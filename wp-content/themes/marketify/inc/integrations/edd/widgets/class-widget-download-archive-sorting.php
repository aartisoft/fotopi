<?php

class Marketify_Widget_Download_Archive_Sorting extends Marketify_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'marketify_widget_download_archive_sorting';
		$this->widget_description = __( 'Display a way to sort the current product archives.', 'marketify' );
		$this->widget_id          = 'marketify_widget_download_archive_sorting';
		$this->widget_name        = __( 'Marketify - Shop: Download Sorting', 'marketify' );
		$this->settings           = array(
			'sidebar-download' => array(
				'type' => 'widget-area',
				'std' => __( 'Shop', 'marketify' ),
			),
			'title' => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title:', 'marketify' ),
			),
		);
		parent::__construct();
	}

	function widget( $args, $instance ) {
		if ( is_page_template( 'page-templates/popular.php' ) || marketify()->get( 'edd' )->popular->is_popular_query() ) {
			return;
		}

		extract( $args );

		$title = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '', $instance, $this->id_base );
		$order = get_query_var( 'm-order' ) ? strtolower( get_query_var( 'm-order' ) ) : 'desc';
		$orderby = get_query_var( 'm-orderby' ) ? get_query_var( 'm-orderby' ) : 'post_date';

		echo $before_widget;

		if ( $title ) {
			echo $before_title . esc_attr( $title ) . $after_title;
		}
		?>

		<form action="" method="get" class="download-sorting">
			<label for="orderby">
				<?php _e( 'Sort by', 'marketify' ); ?>
			</label>
			<?php
				echo EDD()->html->select( array(
					'name' => 'm-orderby',
					'id' => 'm-orderby',
					'selected' => $orderby,
					'show_option_all' => false,
					'show_option_none' => false,
					'options' => marketify()->get( 'edd' )->sorting->options(),
				) );
			?>

			<label for="order-asc" class="download-sorting__dir download-sorting__dir--<?php echo checked( 'asc', $order, false ) ? 'active ': ''; ?>">
				<?php _e( 'ASC', 'marketify' ); ?>
				<input type="radio" name="m-order" id="order-asc" value="asc" <?php checked( 'asc', $order ); ?> />
			</label>

			<label for="order-desc" class="download-sorting__dir download-sorting__dir--<?php echo checked( 'desc', $order, false ) ? 'active ': ''; ?>">
				<?php _e( 'DESC', 'marketify' ); ?>
				<input type="radio" name="m-order" id="order-desc" value="desc" <?php checked( 'desc', $order ); ?> />
			</label>

			<?php if ( is_search() ) : ?>
				<input type="hidden" name="s" value="<?php echo get_query_var( 's' ); ?>" />
				<input type="hidden" name="post_type" value="download" />
			<?php endif; ?>
		</form>

		<?php
		echo $after_widget;
	}

}
