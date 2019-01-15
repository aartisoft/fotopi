<?php

class Marketify_Multiple_Post_Thumbnails extends Marketify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	public function setup_actions() {
		add_action( 'after_setup_theme', array( $this, 'add_extra_thumbnail' ) );

		add_action( 'marketify_download_content_image_before', array( $this, 'filter_id' ) );
		add_action( 'marketify_download_content_image_after', array( $this, 'unfilter_id' ) );

		add_action( 'fes_add_field_to_common_form_element', array( $this, 'use_field_checkbox' ), 100, 4 );
		add_action( 'fes_submit_submission_form_bottom', array( $this, 'assign_image' ) );

		add_filter( 'marketify_download_get_featured_images', array( $this, 'remove_from_featured' ), 10, 2 );
	}

	public function filter_id() {
		add_filter( 'get_post_metadata', array( $this, 'get_post_thumbnail_id' ), 10, 4 );
	}

	public function unfilter_id() {
		remove_filter( 'get_post_metadata', array( $this, 'get_post_thumbnail_id' ), 10, 4 );
	}

	public function add_extra_thumbnail() {
		new MultiPostThumbnails(
			array(
				'label'     => __( 'Grid Image', 'marketify' ),
				'id'        => 'grid-image',
				'post_type' => 'download',
			)
		);
	}

	public function get_post_thumbnail_id( $value, $object_id, $meta_key, $single ) {
		if ( '_thumbnail_id' != $meta_key ) {
			return $value;
		}

		if ( 'download' == get_post( $object_id )->post_type ) {
			$id = MultiPostThumbnails::get_post_thumbnail_id( 'download', 'grid-image', $object_id );

			if ( $id ) {
				return $id;
			}
		}

		return $value;
	}

	public function remove_from_featured( $images, $post ) {
		$id = MultiPostThumbnails::get_post_thumbnail_id( 'download', 'grid-image', $post->ID );

		if ( isset( $images[ $id ] ) ) {
			unset( $images[ $id ] );
		}

		return $images;
	}

	public function use_field_checkbox( $tpl, $input_name, $id, $values ) {
		if ( isset( $values['input_type'] ) && 'file_upload' != $values['input_type'] ) {
			return;
		}

		$field_name  = sprintf( $tpl, $input_name, $id, 'download-grid-image' );
		$field_value = $values && isset( $values['download-grid-image'] ) ? esc_attr( $values['download-grid-image'] ) : '';
	?>
		<div class="fes-form-rows">
			<label><?php _e( 'Use as Grid Thumbnail', 'marketify' ); ?></label>

			<div class="fes-form-sub-fields">
				<label for="<?php esc_attr( $field_name ); ?>">
					<input type="checkbox" data-type="label" id="<?php echo esc_attr( $field_name ); ?>" name="<?php echo esc_attr( $field_name ); ?>" value="1" class="smallipopInput" <?php checked( $field_value, 1 ); ?>>
					<?php _e( 'Use this image as the grid thumbnail.', 'marketify' ); ?>
				</label>
			</div>
		</div><!-- .fes-form-rows -->
	<?php
	}

	public function assign_image( $post_id ) {
		$image = $this->find_image_field( $post_id );

		if ( ! $image ) {
			return;
		}

		MultiPostThumbnails::set_meta( $post_id, 'download', 'grid-image', $image );
	}

	private function find_image_field( $post_id ) {
		$form_id = EDD_FES()->helper->get_option( 'fes-submission-form' );

		if ( ! $form_id ) {
			return;
		}

		$fields = get_post_meta( $form_id, 'fes-form', true );
		$image = false;

		if ( ! $fields ) {
			return;
		}

		foreach ( $fields as $field ) {
			if ( isset( $field['download-grid-image'] ) ) {
				$image = $field;
				break;
			}
		}

		if ( ! $image ) {
			return;
		}

		$image = get_post_meta( $post_id, $image['name'], true );

		if ( $image && is_array( $image ) ) {
			$image = current( $image );
		}

		return $image;
	}

}
