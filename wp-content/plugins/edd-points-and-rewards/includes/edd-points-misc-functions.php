<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Misc Functions
 * 
 * All misc functions handles to 
 * different functions 
 * 
 * @package Easy Digital Downloads - Points and Rewards
 * @since 1.0.0
 *
 */
	/**
	 * Add Custom Ratings Points Settings
	 * 
	 * Handle to add custom ratings points settings
	 *
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 2.0.3
	 */
	function edd_ratingpoints_callback( $args ) {
		
		global $edd_options;

		if ( isset( $edd_options[ $args['id'] ] ) ) {
			$value = $edd_options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size = isset( $args['size'] ) && !is_null($args['size']) ? $args['size'] : 'regular';

		$html = '';
		for ( $star_num = 5; $star_num >= 1; $star_num-- ) {

			$val = isset( $value[$star_num] ) ? $value[$star_num] : '';

			$html .= '<div class="edd_points_sub_field_item">';

			//Display Star description
			for ( $i = 1; $i <= 5; $i++ ) {
				$star_filled = ( $star_num >= $i ) ? 'dashicons-star-filled' : 'dashicons-star-empty';
				$html .= '<span class="dashicons '. $star_filled .'"></span>';
			}

			$html .= '<input type="text" class="' . $args['size'] . '-text" id="edd_settings[' . $args['id'] . ']" name="edd_settings[' . $args['id'] . ']['. $star_num .']" value="' . esc_attr( $val ) . '"/>';
			$html .= __( ' Point(s)', 'eddpoints' );
			$html .= '</div>';
		}
		$html .= '<label for="edd_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}

	/**
	 * Add Custom Points Conversion Rate Settings
	 * 
	 * Handle to add custom points conversion rate settings
	 *
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.0.0
	 * 
	 */
	function edd_pointsrate_callback( $args ) {
		
		global $edd_options;

		if ( isset( $edd_options[ $args['id'] ] ) ) {
			$value = $edd_options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}
		$points = isset( $value['points'] ) ? $value['points'] : '';
		$rate = isset( $value['rate'] ) ? $value['rate'] : '';
		
		$val = edd_currency_filter(' ');
		$size = isset( $args['size'] ) && !is_null($args['size']) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $args['size'] . '-text" id="edd_settings[' . $args['id'] . ']" name="edd_settings[' . $args['id'] . '][points]" value="' . esc_attr( $points ) . '"/> ';
		$html .= __( 'Points =', 'eddpoints' ).' '. $val . ' <input type="text" class="' . $args['size'] . '-text" id="edd_settings[' . $args['id'] . ']" name="edd_settings[' . $args['id'] . '][rate]" value="' . esc_attr( $rate ) . '"/>';
		$html .= '<label for="edd_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';
	
		echo $html;
	}
	
	/**
	 * Add Custom Points Label Settings
	 * 
	 * Handle to add custom points conversion rate settings
	 *
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.0.0
	 * 
	 */
	function edd_singularplural_callback( $args ) {
		
		global $edd_options;

		if ( isset( $edd_options[ $args['id'] ] ) ) {
			$value = $edd_options[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}
		$singular = isset( $value['singular'] ) ? $value['singular'] : '';
		$plural = isset( $value['plural'] ) ? $value['plural'] : '';
		
		$size = isset( $args['size'] ) && !is_null($args['size']) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . $args['size'] . '-text" id="edd_settings[' . $args['id'] . ']" name="edd_settings[' . $args['id'] . '][singular]" value="' . esc_attr( $singular ) . '"/> ';
		$html .= '<input type="text" class="' . $args['size'] . '-text" id="edd_settings[' . $args['id'] . ']" name="edd_settings[' . $args['id'] . '][plural]" value="' . esc_attr( $plural ) . '"/>';
		$html .= '<label for="edd_settings' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';
	
		echo $html;
	}
	
	/**
	 * Add Custom Points Label Settings
	 * 
	 * Handle to add custom points conversion rate settings
	 *
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.0.0
	 * 
	 */
	function edd_apply_points_callback( $args ) {
		
		global $edd_options;

		$button_value = isset( $args['button'] ) && !empty( $args['button'] ) ? $args['button'] : __( 'Apply Points', 'eddpoints' );
		$apply_points_url = add_query_arg( array( 'points_action' => 'apply_points' ), get_permalink() );
		
		$html = '';
		$html .= '<a href="' . $apply_points_url . '" class="edd-points-apply-disocunts-prev-orders ' . $args['size'] . '" id="edd_settings[' . $args['id'] . ']" >' . $button_value .  '</a>';
		$html .= $args['desc'];
	
		echo $html;
	}
	/**
	 * Add Custom Points Label Settings
	 * 
	 * Handle to add custom points conversion rate settings
	 *
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.0.0
	 * 
	 */
	function edd_delete_points_callback( $args ) {
		
		global $edd_options;

		$button_value = isset( $args['button'] ) && !empty( $args['button'] ) ? $args['button'] : __( 'Delete Points', 'eddpoints' );
		$apply_points_url = add_query_arg( array( 'points_action' => 'delete_points' ), get_permalink() );
		
		$html = '';
		$html .= '<a href="' . $apply_points_url . '" class="edd-points-delete-disocunts-prev-orders ' . $args['size'] . '" id="edd_settings[' . $args['id'] . ']" >' . $button_value .  '</a>';
		$html .= $args['desc'];
	
		echo $html;
	}
	/**
	 * Get Current User / Passed User ID Points
	 * 
	 * Handles to get total points of current user / passed user id
	 * and return
	 * 
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.0.0
	 **/
	function edd_points_get_user_points( $userid = '' ) {
		
		global $current_user;
		
		//check userid is empty then use current user id
		if( empty( $userid ) ) $userid = $current_user->ID;
		
		//get user points from user account
		$user_points = get_user_meta( $userid, '_edd_userpoints', true ); 
		
		//user points
		$user_points = !empty( $user_points ) ? $user_points : '0';
		
		return $user_points;
		
	}
	/**
	 * Add Points to user account
	 * 
	 * Handles to add points to user account
	 * 
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.0.0
	 **/
	function edd_points_add_points_to_user( $points = 0, $userid = '' ) {
		
		global $current_user;
		
		//check userid is empty then use current user id
		if( empty( $userid ) ) $userid = $current_user->ID;
		
		//check points should not empty
		if( !empty( $points ) ) {
		
			//get user current points
			$user_points = edd_points_get_user_points( $userid );
			
			//update users points for signup
			update_user_meta( $userid, '_edd_userpoints', ( $user_points + $points ) );

			// Do action after points added
			do_action( 'edd_points_after_add_points_to_user', $userid, $points, $user_points );
			
		} // end if to check points should not empty
	}
	
	/**
	 * Minus / Decrease Points from user account
	 * 
	 * Handles to minus / decrease points from user account
	 * 
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.0.0
	 **/
	function edd_points_minus_points_from_user( $points = 0, $userid = '' ) {
		
		global $current_user;
		
		//check userid is empty then use current user id
		if( empty( $userid ) ) $userid = $current_user->ID;
		
		//check points should not empty
		if( !empty( $points ) ) {
		
			//get user current points
			$user_points = edd_points_get_user_points( $userid );
			
			//update users points for signup
			update_user_meta( $userid, '_edd_userpoints', ( $user_points - $points ) );
			
			// Do action after points minus
			do_action( 'edd_points_after_minus_points_from_user', $userid, $points, $user_points );
			
		} // end if to check points should not empty
	}

	/**
	 * Get Easy Digital Downloads Screen ID
	 * 
	 * Handles to get edd screen id
	 * 
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.3.4
	 */
	function edd_points_get_edd_screen_id() {
		
		$edd_screen_id		= 'download';
		return apply_filters( 'edd_points_get_edd_screen_id', $edd_screen_id );
	}
	
	/**
	 * Function restrict the user to get points if he is the owner of product
	 * You can pass some user roles if you do not want to restrict them
	 * 
	 * @param array $roles
	 * @since 1.3.6
	 */
	function edd_points_restrict_seller( $roles = array() ){
		
		global $current_user;
		
		$result = true;
		
		// Getting current user role
		$user_roles = ( !empty($current_user->roles[0]) ) ? $current_user->roles[0] : null;
		
		$roles = !empty($roles) ? (array)$roles : array();
		$roles = apply_filters( 'edd_points_restrict_seller', $roles );
		
		if( in_array( $user_roles, $roles ) ) {
			$result = false;
		}
		
		return $result;
	}