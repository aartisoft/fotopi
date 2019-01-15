<?php
/**
 * Number Field.
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
 * FES_Number_Field Class.
 *
 * @since 2.6
 *
 * @see   FES_Text_Field
 */
class FES_Number_Field extends FES_Text_Field {

	/**
	 * Field Version.
	 *
	 * @access public
	 * @since  2.6
	 * @var    string
	 */
	public $version = '1.0.0';

	/**
	 * For 3rd parameter of get_post/user_meta.
	 *
	 * @access public
	 * @since  2.6
	 * @var    bool
	 */
	public $single = true;

	/**
	 * Supports are things that are the same for all fields of a field type.
	 * E.g. whether or not a field type supports jQuery Phoenix. Stored in object, not database.
	 *
	 * @access public
	 * @since  2.3
	 * @var    array
	 */
	public $supports = array(
		'multiple'    => false,
		'is_meta'     => true,
		'forms'       => array(
			'registration'   => true,
			'submission'     => true,
			'vendor-contact' => true,
			'profile'        => true,
			'login'          => false,
		),
		'position'    => 'custom',
		'permissions' => array(
			'can_remove_from_formbuilder' => true,
			'can_change_meta_key'         => true,
			'can_add_to_formbuilder'      => true,
		),
		'template'    => 'number',
		'title'       => 'Number',
		'phoenix'     => false,
	);

	/**
	 * Characteristics are things that can change from field to field of the same field type. Like the placeholder
	 * between two email fields. Stored in db.
	 *
	 * @access public
	 * @since  2.3
	 * @var    array
	 */
	public $characteristics = array(
		'name'        => 'number',
		'template'    => 'number',
		'public'      => true,
		'required'    => false,
		'label'       => 'Number',
		'css'         => '',
		'default'     => '',
		'size'        => '',
		'help'        => '',
		'placeholder' => '',
		'extension'   => array(),
	);

	/**
	 * Set the title of the field.
	 *
	 * @access public
	 * @since  2.3
	 */
	public function set_title() {
		$this->supports['title'] = apply_filters( 'fes_' . $this->name() . '_field_title', _x( 'Number', 'FES Field title translation', 'edd_fes' ) );
	}

	/**
	 * Returns the HTML to render a field in admin.
	 *
	 * @access public
	 * @since  2.3
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

		$user_id  = apply_filters( 'fes_render_text_field_user_id_admin', $user_id, $this->id );
		$readonly = apply_filters( 'fes_render_text_field_readonly_admin', $readonly, $user_id, $this->id );
		$value    = $this->get_field_value_admin( $this->save_id, $user_id, $readonly );

		$output = '';
		$output .= sprintf( '<div class="fes-el %1s %2s %3s">', $this->template(), $this->name(), $this->css() );
		$output .= $this->label( $readonly );
		ob_start(); ?>
		<div class="fes-fields">
			<input class="textfield<?php echo esc_attr( $this->required_class( $readonly ) ); ?>" id="<?php echo esc_attr( $this->name() ); ?>" type="number" data-required="false" data-type="text" name="<?php echo esc_attr( $this->name() ); ?>" placeholder="<?php echo esc_attr( $this->placeholder() ); ?>" value="<?php echo esc_attr( $value ); ?>" size="<?php echo esc_attr( $this->size() ); ?>" <?php echo $readonly ? 'disabled' : ''; ?> />
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
	 * @since  2.6
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

		$user_id  = apply_filters( 'fes_render_telephone_number_field_user_id_frontend', $user_id, $this->id );
		$readonly = apply_filters( 'fes_render_telephone_number_field_readonly_frontend', $readonly, $user_id, $this->id );
		$value    = $this->get_field_value_frontend( $this->save_id, $user_id, $readonly );
		$required = $this->required( $readonly );

		$output = '';
		$output .= sprintf( '<div class="fes-el %1s %2s %3s">', $this->template(), $this->name(), $this->css() );
		$output .= $this->label( $readonly );
		ob_start(); ?>
		<div class="fes-fields">
			<input class="textfield<?php echo esc_attr( $this->required_class( $readonly ) ); ?>" id="<?php echo esc_attr( $this->name() ); ?>" type="number" data-required="false" data-type="text" name="<?php echo esc_attr( $this->name() ); ?>" placeholder="<?php echo esc_attr( $this->placeholder() ); ?>" value="<?php echo esc_attr( $value ); ?>" size="<?php echo esc_attr( $this->size() ); ?>" <?php echo $readonly ? 'disabled' : ''; ?> />
		</div>
		<?php
		$output .= ob_get_clean();
		$output .= '</div>';

		return $output;
	}

	/**
	 * Sanitize given input data.
	 *
	 * @access public
	 * @since  2.6
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
			$values[ $name ] = sanitize_text_field( trim( $values[ $name ] ) );
		}

		/**
		 * Filters the sanitized values.
		 *
		 * @param array  $values  Pre-validated return values.
		 * @param string $name    Field name.
		 * @param int    $save_id Save ID.
		 * @param int    $user_id User ID.
		 */
		return apply_filters( 'fes_sanitize_' . $this->template() . '_field', $values, $name, $save_id, $user_id );
	}

	/**
	 * Validate the input data.
	 *
	 * @access public
	 * @since  2.6
	 *
	 * @param array $values  Input values.
	 *                       Default empty array.
	 * @param int   $save_id Save ID.
	 *                       Default -2.
	 * @param int   $user_id User ID.
	 *                       Default -2.
	 *
	 * @return mixed|false Error message, otherwise false.
	 */
	public function validate( $values = array(), $save_id = -2, $user_id = -2 ) {
		$name = $this->name();

		$return_value = false;

		if ( $this->required() ) {
			if ( ! empty( $values[ $name ] ) ) {
				if ( ! is_numeric( $values[ $name ] ) ) {
					$return_value = __( 'Please enter a valid number.', 'edd_fes' );
				}
			}
		}

		/**
		 * Filters the return values.
		 *
		 * @param array  $return_values Validated return values.
		 * @param array  $values        Pre-validated return values.
		 * @param string $name          Field name.
		 * @param int    $save_id       Save ID.
		 * @param int    $user_id       User ID.
		 */
		return apply_filters( 'fes_validate_' . $this->template() . '_field', $return_value, $values, $name, $save_id, $user_id );
	}
}