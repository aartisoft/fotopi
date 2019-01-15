<?php
/**
 * Textarea Field.
 *
 * @package  EDD_FES
 * @category Fields
 * @author   Easy Digital Downloads
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * FES_Textarea_Field Class.
 *
 * @see FES_Field
 */
class FES_Textarea_Field extends FES_Field {

	/**
	 * Field Version.
	 *
	 * @access public
	 * @var    string
	 */
	public $version = '1.0.0';

	/**
	 * For 3rd parameter of get_post/user_meta.
	 *
	 * @access public
	 * @var    bool
	 */
	public $single = true;

	/**
	 * Supports are things that are the same for all fields of a field type.
	 * E.g. whether or not a field type supports jQuery Phoenix. Stored in object, not database.
	 *
	 * @access public
	 * @var    array
	 */
	public $supports = array(
		'multiple'    => true,
		'is_meta'     => true,
		'forms'       => array(
			'registration'   => true,
			'submission'     => true,
			'vendor-contact' => true,
			'profile'        => true,
			'login'          => true,
		),
		'position'    => 'custom',
		'permissions' => array(
			'can_remove_from_formbuilder' => true,
			'can_change_meta_key'         => true,
			'can_add_to_formbuilder'      => true,
		),
		'template'    => 'textarea',
		'title'       => 'Textarea',
		'phoenix'     => true,
	);

	/**
	 * Characteristics are things that can change from field to field of the same field type. Like the placeholder
	 * between two email fields. Stored in db.
	 *
	 * @access public
	 * @var    array
	 */
	public $characteristics = array(
		'name'         => '',
		'template'     => 'textarea',
		'public'       => true,
		'required'     => false,
		'label'        => '',
		'css'          => '',
		'default'      => '',
		'size'         => '',
		'help'         => '',
		'placeholder'  => '',
		'rows'         => '8',
		'rich'         => '',
		'insert_image' => false,
	);

	/**
	 * Set the title of the field.
	 *
	 * @access public
	 */
	public function set_title() {
		$this->supports['title'] = apply_filters( 'fes_' . $this->name() . '_field_title', _x( 'Textarea', 'FES Field title translation', 'edd_fes' ) );
	}

	/**
	 * Returns the HTML to render a field in admin.
	 *
	 * @access public
	 *
	 * @param int $user_id  Save ID.
	 * @param int $readonly Is the field read only?
	 *
	 * @return string HTML to render field in admin.
	 */
	public function render_field_admin( $user_id = -2, $readonly = -2 ) {
		if ( -2 === $user_id ) {
			$user_id = get_current_user_id();
		}

		if ( -2 === $readonly ) {
			$readonly = $this->readonly;
		}

		$user_id   = apply_filters( 'fes_render_textarea_field_user_id_admin', $user_id, $this->id );
		$readonly  = apply_filters( 'fes_render_textarea_field_readonly_admin', $readonly, $user_id, $this->id );
		$value     = $this->get_field_value_admin( $this->save_id, $user_id, $readonly );
		$req_class = 'rich-editor';
		$required  = $this->required( $readonly );

		$output = '';
		$output .= sprintf( '<div class="fes-el %1s %2s %3s">', $this->template(), $this->name(), $this->css() );
		$output .= $this->label( $readonly );
		ob_start(); ?>
		<div class="fes-fields">
			<?php
			$rows = isset( $this->characteristics['rows'] ) ? $this->characteristics['rows'] : 8;
			if ( isset( $this->characteristics['rich'] ) && 'yes' === $this->characteristics['rich'] ) {
				$options = array(
					'editor_height' => $rows,
					'quicktags'     => false,
					'editor_class'  => $req_class,
				);

				if ( isset( $this->characteristics['insert_image'] ) && $this->characteristics['insert_image'] ) {
					$options['media_buttons'] = true;
				}

				printf( '<span class="fes-rich-validation" data-required="%s" data-type="rich" data-id="%s"></span>', esc_attr( $this->characteristics['required'] ), esc_attr( $this->name() ) );
				wp_editor( $value, $this->name(), $options );
			} elseif ( isset( $this->characteristics['rich'] ) && 'teeny' === $this->characteristics['rich'] ) {
				$options = array(
					'editor_height' => $rows,
					'quicktags'     => false,
					'teeny'         => true,
					'editor_class'  => $req_class,
				);

				if ( isset( $this->characteristics['insert_image'] ) && $this->characteristics['insert_image'] ) {
					$options['media_buttons'] = true;
				}

				printf( '<span class="fes-rich-validation" data-required="%s" data-type="rich" data-id="%s"></span>', esc_attr( $this->characteristics['required'] ), esc_attr( $this->name() ) );
				wp_editor( $value, $this->name(), $options );
			} else { ?>
				<textarea class="textareafield" id="<?php echo esc_attr( $this->name() ); ?>" name="<?php echo esc_attr( $this->name() ); ?>" data-required="false" data-type="textarea" placeholder="<?php echo esc_attr( $this->placeholder() ); ?>" rows="<?php echo esc_attr( $rows ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
			<?php } ?>
		</div>
		<?php
		$output .= ob_get_clean();
		$output .= '</div>';

		return $output;
	}

	/**
	 * Returns the HTML to render a field in frontend.
	 *
	 * @access public
	 *
	 * @param int $user_id  Save ID.
	 * @param int $readonly Is the field read only?
	 *
	 * @return string HTML to render field in admin.
	 */
	public function render_field_frontend( $user_id = -2, $readonly = -2 ) {
		if ( -2 === $user_id ) {
			$user_id = get_current_user_id();
		}

		if ( -2 === $readonly ) {
			$readonly = $this->readonly;
		}

		$user_id   = apply_filters( 'fes_render_textarea_field_user_id_frontend', $user_id, $this->id );
		$readonly  = apply_filters( 'fes_render_textarea_field_readonly_frontend', $readonly, $user_id, $this->id );
		$value     = $this->get_field_value_frontend( $this->save_id, $user_id, $readonly );
		$required  = $this->required( $readonly );
		$req_class = $required ? 'required' : 'rich-editor';
		$output    = '';
		$output    .= sprintf( '<div class="fes-el %1s %2s %3s">', $this->template(), $this->name(), $this->css() );
		$output    .= $this->label( $readonly );
		ob_start();
		?>
		<div class="fes-fields">
			<?php
			$rows = isset( $this->characteristics['rows'] ) ? $this->characteristics['rows'] : 8;
			if ( isset( $this->characteristics['rich'] ) && 'yes' === $this->characteristics['rich'] ) {
				$options = array(
					'editor_height' => $rows,
					'quicktags'     => false,
					'editor_class'  => $req_class,
				);

				if ( isset( $this->characteristics['insert_image'] ) && $this->characteristics['insert_image'] ) {
					$options['media_buttons'] = true;
				}

				printf( '<span class="fes-rich-validation" data-required="%s" data-type="rich" data-id="%s"></span>', esc_attr( $this->characteristics['required'] ), esc_attr( $this->name() ) );
				wp_editor( $value, $this->name(), $options );

			} elseif ( isset( $this->characteristics['rich'] ) && 'teeny' === $this->characteristics['rich'] ) {
				$options = array(
					'editor_height' => $rows,
					'quicktags'     => false,
					'teeny'         => true,
					'editor_class'  => $req_class,
				);

				if ( isset( $this->characteristics['insert_image'] ) && $this->characteristics['insert_image'] ) {
					$options['media_buttons'] = true;
				}

				printf( '<span class="fes-rich-validation" data-required="%s" data-type="rich" data-id="%s"></span>', esc_attr( $this->characteristics['required'] ), esc_attr( $this->name() ) );
				wp_editor( $value, $this->name(), $options );
			} else { ?>
				<textarea class="textareafield<?php echo $this->required_class( $readonly ); ?>" id="<?php echo esc_attr( $this->name() ); ?>" name="<?php echo esc_attr( $this->name() ); ?>" data-required="<?php echo esc_attr( $required ); ?>" data-type="textarea"<?php esc_attr( $this->required_html5( $readonly ) ); ?> placeholder="<?php echo esc_attr( $this->placeholder() ); ?>" rows="<?php echo esc_attr( $rows ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
			<?php } ?>
		</div>
		<?php
		$output .= ob_get_clean();
		$output .= '</div>';

		return $output;
	}

	/**
	 * Returns the HTML to render a field within the Form Builder.
	 *
	 * @access public
	 *
	 * @param int  $index  Form builder index.
	 * @param bool $insert Whether the field is being inserted.
	 *
	 * @return string HTML to render field in Form Builder.
	 */
	public function render_formbuilder_field( $index = -2, $insert = false ) {
		$removable = $this->can_remove_from_formbuilder();
		ob_start();
		?>
		<li class="custom-field textarea_field">
			<?php $this->legend( $this->title(), $this->get_label(), $removable ); ?>
			<?php FES_Formbuilder_Templates::hidden_field( "[$index][template]", $this->template() ); ?>

			<?php FES_Formbuilder_Templates::field_div( $index, $this->name(), $this->characteristics, $insert ); ?>
			<?php FES_Formbuilder_Templates::public_radio( $index, $this->characteristics, $this->form_name ); ?>
			<?php FES_Formbuilder_Templates::standard( $index, $this ); ?>
			<?php FES_Formbuilder_Templates::common_textarea( $index, $this->characteristics ); ?>
			</div>
		</li>
		<?php
		return ob_get_clean();
	}

	/**
	 * Sanitize given input data.
	 *
	 * @access public
	 *
	 * @param  array $values  Input values.
	 * @param  int   $save_id Save ID.
	 * @param  int   $user_id User ID.
	 *
	 * @return array $return_value Sanitized input data.
	 */
	public function sanitize( $values = array(), $save_id = -2, $user_id = -2 ) {
		$name = $this->name();
		if ( ! empty( $values[ $name ] ) ) {
			$values[ $name ] = trim( $values[ $name ] );
			$values[ $name ] = wp_kses( $values[ $name ], fes_allowed_html_tags() );
		}

		return apply_filters( 'fes_sanitize_' . $this->template() . '_field', $values, $name, $save_id, $user_id );
	}
}
