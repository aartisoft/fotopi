<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Public Pages Class
 *
 * Handles all the different features and functions
 * for the front end pages.
 *
 * @package Easy Digital Downloads - Points and Rewards
 * @since 1.0.0
 */
class EDD_Points_Public	{
	
	var $render,$model,$logs;
	
	public function __construct() {
		
		global $edd_points_model, $edd_points_render, $edd_points_log;
		
		$this->render 	= $edd_points_render;
		$this->logs 	= $edd_points_log;
		$this->model 	= $edd_points_model;
	}
	
	/**
	 * Add points for signup
	 *
	 * Handles to add users points for signup
	 *  
	 * @package Easy Digital Downloads - Points and Rewards
 	 * @since 1.0.0
	 */
	public function edd_points_add_point_for_signup( $user_id ) {
		
		global $edd_options;
		
		//update users points for signup
		edd_points_add_points_to_user( $edd_options['edd_points_earned_account_signup'], $user_id );
		
		//points label
		$pointslable = $this->model->edd_points_get_points_label( $edd_options['edd_points_earned_account_signup'] );
		
		$post_data = array(
						'post_title'	=> sprintf( __( '%s for Signup','eddpoints'), $pointslable ),
						'post_content'	=> sprintf( __('Get %s for signing up new account','eddpoints' ),$pointslable ),
						'post_author'	=>	$user_id
					);
		$log_meta = array(
						'userpoint'		=>	$edd_options['edd_points_earned_account_signup'],
						'events'		=>	'signup',
						'operation'		=>	'add' //add or minus
					);
					
		$this->logs->edd_points_insert_logs( $post_data ,$log_meta );
	}
	
	/**
	 * Add points for purchase
	 * 
	 * Handles to add points for purchases
	 *
	 * @package Easy Digital Downloads - Points and Rewards
 	 * @since 1.0.0
	 */
	public function edd_points_add_point_for_complete_purchase( $payment_id ) {
		
		global $edd_options, $current_user;
		
		//get payment data
		$paymentdata	=	edd_get_payment_meta( $payment_id );
		$userdata		=	edd_get_payment_meta_user_info( $payment_id );
		$user_id		=	( !empty($userdata['id']) && $userdata['id'] > 0 ) ? $userdata['id'] : 0;
		
		//get discount towards points
		$gotdiscount = $this->model->edd_points_get_payment_discount( $payment_id );
		
		//check user has redeemed points or not & user_id should not empty
		if( isset( $gotdiscount ) && !empty( $gotdiscount ) && !empty( $user_id ) ) {
			
			//get discounte price from points
			$discountedpoints = $this->model->edd_points_calculate_points( $gotdiscount );
			
			//update user points
			edd_points_minus_points_from_user( $discountedpoints, $user_id );
			
			//points label
			$pointslable = $this->model->edd_points_get_points_label( $discountedpoints );
			
			//record data logs for redeem for purchase
			$post_data = array(
								'post_title'	=> sprintf( __( 'Redeem %s for purchase', 'eddpoints' ), $pointslable ),
								'post_content'	=> sprintf( __( '%s redeemed for purchasing download by redeeming the points and get discounts.', 'eddpoints' ), $pointslable ),
								'post_author'	=>	$user_id
							);
			//log meta array
			$log_meta = array(
								'userpoint'		=>	$discountedpoints,
								'events'		=>	'redeemed_purchase',
								'operation'		=>	'minus'//add or minus
							);
						
			//insert entry in log
			$this->logs->edd_points_insert_logs( $post_data, $log_meta );
			
			// set order meta, regardless of whether any points were earned, just so we know the process took place
			update_post_meta( $payment_id, '_edd_points_order_redeemed', $discountedpoints );

			$points_redeem_note = apply_filters('edd_points_payment_redeem_note', 
													sprintf( __( '%s %s redeemed for a %s discount.', 'eddpoints' ), $discountedpoints, $pointslable, edd_currency_filter( $gotdiscount ))
												);

			// Add payment note for Points and Rewards
			edd_insert_payment_note( $payment_id, $points_redeem_note );
			
		} //end if to check points redeemed taken by buyer or not
		
		// get cartdata from older order
		$cartdata = edd_get_payment_meta_cart_details( $payment_id );
		
		//get bought points for points downloads types
		$boughtpoints = $this->model->edd_points_get_bought_points( $cartdata );
		
		//get cart points from cartdata and payment discount given to user
		$cartpoints = $this->model->edd_points_get_user_checkout_points( $cartdata );

		//add bought points to cart points
		$cartpoints = !empty( $boughtpoints ) ? ( $cartpoints + $boughtpoints ) : $cartpoints;
		
		//check checkout points earned points or user id is not empty
		if( !empty( $cartpoints ) && !empty( $user_id ) ) {
			
			//points label
			$pointslable = $this->model->edd_points_get_points_label( $cartpoints );
						
			//get user points after subtracting the redemption points
			$userpoints = edd_points_get_user_points();
			
			$post_data = array(
								'post_title'	=> sprintf( __('%s earned for purchasing the downloads.','eddpoints'), $pointslable ),
								'post_content'	=> sprintf( __('Get %s for purchasing the downloads.','eddpoints'), $pointslable ),
								'post_author'	=>	$user_id
							);
			$log_meta = array(
								'userpoint'		=>	$cartpoints,
								'events'		=>	'earned_purchase',
								'operation'		=>	'add'//add or minus
							);
						
			//insert entry in log	
			$this->logs->edd_points_insert_logs( $post_data, $log_meta );
			
			//update user points
			edd_points_add_points_to_user( $cartpoints, $user_id );

			$points_redeem_note = apply_filters('edd_points_payment_purchase_note', 
													sprintf( __( '%s %s earned for purchasing the downloads.', 'eddpoints' ), $cartpoints, $pointslable)
												);

			// Add payment note for Points and Rewards
			edd_insert_payment_note( $payment_id, $points_redeem_note );
			
			// set order meta, regardless of whether any points were earned, just so we know the process took place
			update_post_meta( $payment_id, '_edd_points_order_earned', $cartpoints );
			
		} //end if to check checkout points should not empty
	}
	
	/**
	 * Add points for Sell
	 * 
	 * Handles to add points for seller
	 *
	 * @package Easy Digital Downloads - Points and Rewards
 	 * @since 1.0.0
	 */
	public function  edd_points_add_seller_points_for_complete_purchase( $payment_id ) {

		global $edd_options;
		
		//get payment data
		$paymentdata	= edd_get_payment_meta( $payment_id );
		$cart_details 	= $paymentdata['cart_details'];				

		if( !empty( $cart_details ) ) {
			
			$points = isset( $edd_options['edd_points_selling_conversion']['points'] ) 	? abs ( $edd_options['edd_points_selling_conversion']['points'] ) 	: 0;
			$rate 	= isset( $edd_options['edd_points_selling_conversion']['rate'] ) 	? abs ( $edd_options['edd_points_selling_conversion']['rate'] ) 	: 0;
			
			if( !empty( $rate ) && !empty( $points ) ) {

				$seller_points_arr = array();
				foreach ( $cart_details as $key => $cart_detail ) {														
							
					if( !empty( $cart_detail['item_number']['id'] ) ) {
						
						$download  = get_post( $cart_detail['item_number']['id'] );

						if( $download->post_author != $paymentdata['user_info']['id'] ) {

							//Calculate total points for seller
							$total_seller_points = ( $points * ( $cart_detail['item_price'] * $cart_detail['quantity'] ) ) / $rate;
							
							//points label
							$pointslable = $this->model->edd_points_get_points_label( $total_seller_points );
						
							$post_data = array(
											'post_title'	=> sprintf( __('%s earned for selling the downloads.','eddpoints'), $pointslable ),
											'post_content'	=> sprintf( __('Get %s for selling the downloads.','eddpoints'), $pointslable ),
											'post_author'	=> $download->post_author
										);
							$log_meta = array(
												'userpoint'		=>	$total_seller_points,
												'events'		=>	'earned_sell',
												'operation'		=>	'add'//add or minus
											);
										
							//insert entry in log	
							$this->logs->edd_points_insert_logs( $post_data, $log_meta );
						
							//update user points
							edd_points_add_points_to_user( $total_seller_points, $download->post_author );

							if( !empty( $seller_points_arr ) && array_key_exists( $download->post_author, $seller_points_arr ) ) {

								$seller_points = $seller_points_arr[$download->post_author] + $total_seller_points;
							} else {

								$seller_points = $total_seller_points;
							}

							$seller_points_arr[$download->post_author] = $total_seller_points;
						}
					}
				}

				if( !empty( $seller_points_arr ) ) {
					update_post_meta( $payment_id, '_edd_points_seller_earned', $seller_points_arr );
				}
			}			
		}
	}
	
	/**
	 * Adjust the Tool Bar
	 * 
	 * @package Easy Digital Downloads - Points and Rewards
 	 * @since 1.0.0
	 */
	function edd_points_tool_bar( $wp_admin_bar ) {
		
		global  $current_user;
		
		$wp_admin_bar->add_group( array(
			'parent' => 'my-account',
			'id'     => 'edd-points-actions',
		) );
		
		//get total users points
		$tot_points = edd_points_get_user_points();
		
		$wp_admin_bar->add_menu( array(
			'parent' => 'edd-points-actions',
			'id'     => 'user-balance',
			'title'  => __( 'My Balance:', 'eddpoints' ) .' '. $tot_points,
			'href'   => admin_url( 'profile.php' )
		) );
	}
	
	/**
	 * Calculate Discount towards Points
	 * 
	 * Handles to calculate the discount towards points
	 * 
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.0.0
	 **/
	public function edd_points_redeem_points() {
		
		global $current_user, $edd_options;
		
		$cart_contents 	= edd_get_cart_contents(); // Get cart contents
		$cart_qty		= count ( $cart_contents ); // Get cart quantity
		
		//remove applied points discount
		if( isset( $_GET['edd_points_remove_discount'] ) && !empty( $_GET['edd_points_remove_discount'] )
			&& $_GET['edd_points_remove_discount'] == 'remove'  ) {

			//get discount towards points
			//$gotdiscount = EDD()->fees->get_fee( 'points_redeem' );
			$edd_fees = EDD()->fees->get_fees();

			if ( !empty ( $edd_fees ) ) {

				foreach ( $edd_fees as $edd_fee_key => $edd_fee_val ) {

					if ( strpos ( $edd_fee_key, 'points_redeem' ) !== false ) {
	
						//remove fees towards fees
						EDD()->fees->remove_fee( $edd_fee_key );
					}
				}
			}
			$redirecturl = remove_query_arg( 'edd_points_remove_discount', get_permalink() );
					
			//redirect to current page
			wp_redirect( $redirecturl );
			exit;
			
		} //end if to check remove discount is called or not
		
		
		//get points plural label
		$pointslabel = isset( $edd_options['edd_points_label']['plural'] ) && !empty( $edd_options['edd_points_label']['plural'] )
					? $edd_options['edd_points_label']['plural'] : 'Points';
		
		
		//check apply discount button is clicked or not
		if( isset( $_POST['edd_points_apply_discount'] ) && !empty( $_POST['edd_points_apply_discount'] ) 
			&& $_POST['edd_points_apply_discount'] == __( 'Apply Discount', 'eddpoints' ) ) {

			// Handles to add discount per product
			$this->add_disc_per_product();

    		wp_redirect( edd_get_checkout_uri() );
			exit;
    		
		} else {

			//else change the fees if its changed from backend
			$edd_fees = EDD()->fees->get_fees();
			$discount_amount = 0;

			if ( !empty ( $edd_fees ) ) {

				foreach ( $edd_fees as $edd_fee_key => $edd_fee_val ) {
	
					if ( strpos ( $edd_fee_key, 'points_redeem' ) !== false ) {

						$discount_amount = $edd_fee_val['amount'];
					}
				}
			}

			if( !empty( $discount_amount ) ) {

				// Handles to add discount per product
				$this->add_disc_per_product();
			}
		}
	}

	/**
	 * Add fees after calculating discounts
	 * 
	 * Handles to calculate the discount towards points
	 * and than add fees based on it
	 * 
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.0.0
	 **/
	public function add_disc_per_product($is_ajax=false){

		// Get global variables
		global $current_user, $edd_options;

		// Define variables
		$download_arr 		= $download_disc_arr = array();
		$items_subtotal 	= 0.00;

		$cart_contents 		= edd_get_cart_contents(); // Get cart contents
		$available_discount = $this->model->edd_points_get_discount_for_redeeming_points(); // Get total available discount

		// Get points plural label
		$pointslabel = isset( $edd_options['edd_points_label']['plural'] ) && !empty( $edd_options['edd_points_label']['plural'] )
					? $edd_options['edd_points_label']['plural'] : 'Points';

		// Loop on cart contents
		foreach ( $cart_contents as $cart_content ) {

			$download					= new EDD_Download( $cart_content['id'] ); // Get download from download id
			$item_price      			= EDD()->cart->get_item_price( $cart_content['id'], $cart_content['options'] ); // Get item price
			$max_applicable_discount 	= $this->model->edd_points_max_discount($cart_content['id']); // Get maximum applicable discount

			// Create array to loop on it for calculating and adding fees
			$download_arr[$cart_content['id']]['max_point_disc'] = ( $max_applicable_discount === '' ) ? $item_price : $max_applicable_discount; // Get max point discount
			$download_arr[$cart_content['id']]['qty'] = $cart_content['quantity']; // Get quantity

			$items_subtotal += $download_arr[$cart_content['id']]['max_point_disc'] * $cart_content['quantity']; // Add in cart subtotal
		}

		// Loop on array created in first loop
		foreach ( $download_arr as $download_key => $download_val ) {

			$download			= new EDD_Download( $download_key ); // Get download
			$subtotal_percent  	= ( ( $download_val['max_point_disc'] * $download_val['qty'] ) / $items_subtotal ); // Get subtotal percent

			$discounted_amount 	= $available_discount * $subtotal_percent; // Get discounted amount per product

			// If discounted amount is not empty
			if(!empty($discounted_amount)) {

				if($is_ajax == true){

					$download_disc_arr[$download_key] = $discounted_amount;
				} else {

					// Create args for fee
					$fee_args = array (
											'amount' 	=> $discounted_amount * -1, // Get amount
											'label'		=> sprintf( __( '%s Discount for %s', 'eddpoints' ),$pointslabel, $download->post_title ), // Get label
											'id'		=> 'points_redeem_'.$download_key, // Get id
											'download_id' => $download_key // Get download id
										);
	
					EDD()->fees->add_fee( $fee_args ); // Add fees
					$points_labels[]  = 'points_redeem_'.$download_key;
				}
			}
		}

		if($is_ajax == true){

			return $download_disc_arr;
		}
	}

	/**
	 * Remove Applied Points Discount
	 * 
	 * Handles to remove applied points 
	 * discount when cart is going to empty
	 * 
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.0.0 
	 **/
	public function edd_points_remove_point_discount() {
		
		//get discount towards points
		$gotdiscount = EDD()->fees->get_fee( 'points_redeem' );
		
		//get cart content
		$cart = edd_get_cart_contents();
		
		//check redeempoints set and cart is empty
		if( !empty( $gotdiscount ) && ! is_array( $cart ) ) {
			
			//remove fees towards fees
			EDD()->fees->remove_fee( 'points_redeem' );
		}
	}
	
	/**
	 * Replace Email Template Tags
	 * 
	 * Handles to replace email template tags
	 *
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.0.0
	 **/
	public function edd_points_replace_emails_template_tags( $message, $payment_data, $payment_id ) {
		
		//get earned points from order data
		$pointsearned = get_post_meta( $payment_id, '_edd_points_order_earned', true );
		//get redeemed points from order data
		$pointsredeem = get_post_meta( $payment_id, '_edd_points_order_redeemed', true );
		
		//replace email tags
		//check points earned should not empty
		if( !empty( $pointsearned ) ) {
			//replace earned points template tag
			$message 	= str_replace( '{points_earned}', $pointsearned, $message  );
		} else {
			//replace earned points template tag
			$message 	= str_replace( '{points_earned}', '', $message  );
		}
		//check points redeemed should not empty
		if( !empty( $pointsredeem ) ) {
			//replace redeemed points template tag
			$message 	= str_replace( '{points_redeemed}', $pointsredeem, $message  );
		} else {
			//replace redeemed points template tag
			$message 	= str_replace( '{points_redeemed}', '', $message  );
		}
		
		//return message after replacing then points data
		return $message;
	}
	
	/**
	 * Add Error For Points Download
	 * 
	 * Handles to show to guest user to
	 * he can not buy a points download
	 * as a guest user
	 * 
	 * @package  Easy Digital Downloads - Points and Rewards
	 * @since 1.1.0
	 **/
	public function edd_points_download_error() {
		
		global $edd_options;
		
		//get cart data
		$cartdata = edd_get_cart_contents();
		
		//get bought points is exist in cart or not
		$boughtpoints = $this->model->edd_points_get_bought_points( $cartdata );
		
		//check user is not logged in and bought points is not empty
		if( ! is_user_logged_in() && !empty( $boughtpoints ) ) {
			
			//message 
			$guestmessage = $this->model->edd_points_guest_bought_download_message( $boughtpoints );
			
			//set error to show to user
			edd_set_error( 'points_download', $guestmessage );
			
		} //end if to check user is not logged in and not purchased points download
	}
	
	/**
	 * Add Point and rewards for recurring payment to buyer
	 * 
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.3.9
	 */
	public function edd_points_recurring_purchased_payment( $payment, $subscription ) {
		
		global $edd_options;
		
		// get payment id
		$payment_id			= isset( $payment->ID ) ? $payment->ID : '';
		
		// get recurring point option
		$recurring_payments	= isset( $edd_options['edd_points_apply_recurring_payments'] ) ? $edd_options['edd_points_apply_recurring_payments'] : '';
		
		if( !empty( $payment_id ) && !empty( $recurring_payments ) ) { // payment id and recurring option is not empty
			
			// get customer ID
			$customer_id	= isset( $payment->customer_id ) ? $payment->customer_id : '';
			
			// Get cart data
			$cartdata = edd_get_payment_meta_cart_details( $payment_id );
			
			//get cart points from cartdata and payment discount given to user
			$cartpoints = $this->model->edd_points_get_user_checkout_points( $cartdata, 0 );
			
			//check checkout points earned points or user id is not empty
			if( !empty( $cartpoints ) && !empty( $customer_id ) ) {
				
				//points label
				$pointslable = $this->model->edd_points_get_points_label( $cartpoints );
							
				//get user points after subtracting the redemption points
				$userpoints = edd_points_get_user_points();
				
				$post_data = array(
									'post_title'	=> sprintf( __('%s earned for purchasing the downloads.','eddpoints'), $pointslable ),
									'post_content'	=> sprintf( __('Get %s for purchasing the downloads.','eddpoints'), $pointslable ),
									'post_author'	=>	$customer_id
								);
				$log_meta = array(
									'userpoint'		=>	$cartpoints,
									'events'		=>	'earned_purchase',
									'operation'		=>	'add'//add or minus
								);
				
				//insert entry in log
				$this->logs->edd_points_insert_logs( $post_data, $log_meta );
				
				//update user points
				edd_points_add_points_to_user( $cartpoints, $customer_id );
				
				// set order meta, regardless of whether any points were earned, just so we know the process took place
				update_post_meta( $payment_id, '_edd_points_order_earned', $cartpoints );
			}
		}
	}
	
	/**
	 * Add Point and rewards for recurring payment to seller
	 * 
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.3.9
	 */
	public function edd_points_recurring_seller_payment( $payment, $subscription ) {
		
		global $edd_options;
		
		// get payment id
		$payment_id			= isset( $payment->ID ) ? $payment->ID : '';
		
		// get recurring point option
		$recurring_payments	= isset( $edd_options['edd_points_apply_recurring_payments'] ) ? $edd_options['edd_points_apply_recurring_payments'] : '';
		
		if( !empty( $payment_id ) && !empty( $recurring_payments ) ) { // payment id and recurring option is not empty
			
			// Get edd payment data
			$paymentdata	= edd_get_payment_meta( $payment_id );
			
			// get cart detail
			$cart_details 	= $paymentdata['cart_details'];
			
			if( !empty( $cart_details ) ) { // if cart detail is not empty
				
				// seller point option
				$points = isset( $edd_options['edd_points_selling_conversion']['points'] ) 	? abs ( $edd_options['edd_points_selling_conversion']['points'] ) 	: 0;
				
				//seller rate option
				$rate 	= isset( $edd_options['edd_points_selling_conversion']['rate'] ) 	? abs ( $edd_options['edd_points_selling_conversion']['rate'] ) 		: 0;
				
				if( !empty( $rate ) && !empty( $points ) ) { // if rate and point option is not empty
					
					foreach ( $cart_details as $key => $cart_detail ) {
						
						if( !empty( $cart_detail['item_number']['id'] ) ) {
							
							$download  = get_post( $cart_detail['item_number']['id'] );
						
							//Calculate total points for seller
							$total_seller_points = ( $points * ( $cart_detail['item_price'] * $cart_detail['quantity'] ) ) / $rate;
							
							//points label
							$pointslable = $this->model->edd_points_get_points_label( $total_seller_points );
						
							$post_data = array(
											'post_title'	=> sprintf( __('%s earned for selling the downloads.','eddpoints'), $pointslable ),
											'post_content'	=> sprintf( __('Get %s for selling the downloads.','eddpoints'), $pointslable ),
											'post_author'	=> $download->post_author
										);
							$log_meta = array(
												'userpoint'		=>	$total_seller_points,
												'events'		=>	'earned_sell',
												'operation'		=>	'add'//add or minus
											);
										
							//insert entry in log	
							$this->logs->edd_points_insert_logs( $post_data, $log_meta );
							
							//update user points
							edd_points_add_points_to_user( $total_seller_points, $download->post_author );				
						}
					}
				}
			}
		}
	}

	/**
	 * Handles to modify discount data when quantity is changed
	 * in cart through AJAX
	 * 
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.3.9
	 */
	public function edd_points_cart_modify_fee ( $data ) {

		global $current_user, $edd_options;

		$cart_contents 	= edd_get_cart_contents(); // Get cart contents
		$cart_qty		= count ( $cart_contents ); // Get cart quantity
		$edd_fees 		= EDD()->fees->get_fees(); // Get EDD Fees
		
		if ( !empty ( $edd_fees ) ) {

			foreach ( $edd_fees as $edd_fee_key => $edd_fee_val ) {

				if ( strpos ( $edd_fee_key, 'points_redeem' ) !== false ) {

					$cart_item_disc = $this->add_disc_per_product( true );
					foreach($cart_item_disc as $disc_key => $disc_val){

						$data['points_redeem_'.$disc_key] = html_entity_decode( edd_currency_filter( edd_format_amount( $disc_val * -1 ) ), ENT_COMPAT, 'UTF-8' ); // Get amount;
					}

					break;
				}
			}
		}

		return $data;
	}

	/**
	 * Awarded Points on User Rated on product.
	 *
	 * Awarded points rated on product.
	 *
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 2.0.3
	 */
	public function edd_points_rate_on_download( $id, $comment ) {

		global $edd_points_log, $edd_options;

		//Check if review need to do
		if( !empty( $edd_options['edd_points_enable_reviews'] ) && !empty( $comment->user_id )
			&& isset( $comment->comment_type ) && $comment->comment_type == 'edd_review' ) {

			//Get details
			$rating        = ( isset( $_POST['edd-reviews-review-rating'] ) ) ? trim( $_POST['edd-reviews-review-rating'] ) : null;
			$rating        = wp_filter_nohtml_kses( $rating );

			//Get points
			$product_review_points = get_post_meta( $comment->comment_post_ID, '_edd_points_review_points', true );
			$review_points = !empty( $product_review_points[$rating] ) ? $product_review_points[$rating] : '';
			if( empty( $review_points ) ) {

				//Get global points if not at product level
				$review_points = !empty( $edd_options['edd_points_review_points'][$rating] ) ? $edd_options['edd_points_review_points'][$rating] : '';
			}

			if( !empty( $review_points ) ) {

				// Add points to post author 
				edd_points_add_points_to_user( $review_points , $comment->user_id );

				// insert add point log
				$post_data = array(
					'post_title'	=> __( 'Points earned for rate on product.', 'eddpoints' ),
					'post_content'	=> __( 'Points earned for rate on product.', 'eddpoints' ),
					'post_author'	=>	$comment->user_id
				);

				$log_meta = array(
									'userpoint'		=>	$review_points,
									'events'		=>	'earned_product_review',
									'operation'		=>	'add'//add or minus
								);

				//insert entry in log	
				$points_log_id = $edd_points_log->edd_points_insert_logs( $post_data, $log_meta );
			}
		}
	}

	/**
	 * Revert Points on Refunded Payment
	 *
	 * Handles to revert earned and redeemed points
	 * for refunded order
	 *
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 2.0.5
	 */
	public function edd_points_revert_points_for_payment( $payment_id, $status, $old_status ){

		global $edd_options;

		// Create status array for which we need to revert points
		$revert_points_status = array('refunded');

		// Get settings to revert points when purchase is refunded
		$edd_points_revert_points_refund = edd_get_option( 'edd_points_revert_points_refund' );

		// If payment id is not empty and payment status needs to revert points
		if( !empty( $edd_points_revert_points_refund ) && $edd_points_revert_points_refund == '1' 
			&& !empty( $payment_id ) && in_array( $status, $revert_points_status ) ){

			// Get points earned
			$points_earned 		= get_post_meta( $payment_id, '_edd_points_order_earned', true );

			// Get points redeemed
			$points_redeemed 	= get_post_meta( $payment_id, '_edd_points_order_redeemed', true );

			// Get user id from payment
			$payment_user_id 	= get_post_meta( $payment_id, '_edd_payment_user_id', true );

			// If points earned is not empty
			if( !empty( $points_earned ) ) {

				//points label
				$pointslable 		= $this->model->edd_points_get_points_label( $points_earned );
			
				//record data logs for redeem for purchase
				$post_data = array(
									'post_title'	=> sprintf( __( ' %s debited for refunded Payment %d', 'eddpoints' ), $pointslable, $points_earned ),
									'post_content'	=> sprintf( __( '%s debited for refunded Payment %d', 'eddpoints' ), $pointslable, $points_earned ),
									'post_author'	=>	$payment_user_id
								);
				//log meta array
				$log_meta = array(
									'userpoint'		=>	$points_earned,
									'events'		=>	'refunded_purchase_debited',
									'operation'		=>	'minus'//add or minus
								);

				//insert entry in log
				$this->logs->edd_points_insert_logs( $post_data, $log_meta );

				// Deduct points from user points log
				edd_points_minus_points_from_user( $points_earned, $payment_user_id );

				$points_debit_note = apply_filters('edd_points_debit_refund_order', 
														sprintf( __( '%s %s debited for refunded Payment.', 'eddpoints' ), $points_earned, $pointslable)
													);
	
				// Add payment note for Points and Rewards
				edd_insert_payment_note( $payment_id, $points_debit_note );

				// Delete points earned meta
				delete_post_meta( $payment_id, '_edd_points_order_earned' );
			}

			// If points redeemed is not empty
			if( !empty( $points_redeemed ) ) {

				//points label
				$pointslable 		= $this->model->edd_points_get_points_label( $points_redeemed );

				//record data logs for redeem for purchase
				$post_data = array(
									'post_title'	=> sprintf( __( ' %s credited for refunded Payment %d', 'eddpoints' ), $pointslable, $payment_id ),
									'post_content'	=> sprintf( __( '%s credited for refunded Payment %d', 'eddpoints' ), $pointslable, $payment_id ),
									'post_author'	=>	$payment_user_id
								);
				//log meta array
				$log_meta = array(
									'userpoint'		=>	$points_redeemed,
									'events'		=>	'refunded_purchase_credited',
									'operation'		=>	'add'//add or minus
								);
							
				//insert entry in log
				$this->logs->edd_points_insert_logs( $post_data, $log_meta );

				// Add points from user points log
				edd_points_add_points_to_user( $points_redeemed, $payment_user_id );

				$points_credit_note = apply_filters('edd_points_credit_refund_order', 
														sprintf( __( '%s %s credited for refunded Payment.', 'eddpoints' ), $points_redeemed, $pointslable )
													);
	
				// Add payment note for Points and Rewards
				edd_insert_payment_note( $payment_id, $points_credit_note );

				// Delete points redeemed meta
				delete_post_meta( $payment_id, '_edd_points_order_redeemed' );
			}

			$edd_points_seller_earned = get_post_meta( $payment_id, '_edd_points_seller_earned', true );

			// If order is placed after we started saving seller points
			if( !empty( $edd_points_seller_earned ) && is_array( $edd_points_seller_earned ) ) {

				foreach( $edd_points_seller_earned as $seller_id => $seller_points ) {

					$post_data = array(
									'post_title'	=> sprintf( __('Seller earned %s debited towards purchase refund','eddpoints'), $pointslable ),
									'post_content'	=> sprintf( __('Seller earned %s debited towards purchase refund','eddpoints'), $pointslable ),
									'post_author'	=> $seller_id
								);
					$log_meta = array(
										'userpoint'		=>	$seller_points,
										'events'		=>	'refunded_sell',
										'operation'		=>	'minus'//add or minus
									);

					//insert entry in log	
					$this->logs->edd_points_insert_logs( $post_data, $log_meta );
				
					//update user points
					edd_points_minus_points_from_user( $seller_points, $seller_id );

					// Delete the post meta
					delete_post_meta( $payment_id, '_edd_points_seller_earned' );
				}
			} else { // If it is older order then debit points based on global settings
				//get payment data
				$paymentdata	= edd_get_payment_meta( $payment_id );
				$cart_details 	= $paymentdata['cart_details'];
		
				if( !empty( $cart_details ) ) {
					
					$points = isset( $edd_options['edd_points_selling_conversion']['points'] ) 	? abs ( $edd_options['edd_points_selling_conversion']['points'] ) 	: 0;
					$rate 	= isset( $edd_options['edd_points_selling_conversion']['rate'] ) 	? abs ( $edd_options['edd_points_selling_conversion']['rate'] ) 	: 0;
					
					if( !empty( $rate ) && !empty( $points ) ) {
						
						foreach ( $cart_details as $key => $cart_detail ) {														
									
							if( !empty( $cart_detail['item_number']['id'] ) ) {
								
								$download  = get_post( $cart_detail['item_number']['id'] );
		
								if( $download->post_author != $paymentdata['user_info']['id'] ) {
		
									//Calculate total points for seller
									$total_seller_points = ( $points * ( $cart_detail['item_price'] * $cart_detail['quantity'] ) ) / $rate;
									
									//points label
									$pointslable = $this->model->edd_points_get_points_label( $total_seller_points );
								
									$post_data = array(
													'post_title'	=> sprintf( __('Earned %s for selling product, debited towards purchase refund','eddpoints'), $pointslable ),
													'post_content'	=> sprintf( __('Earned %s for selling product, debited towards purchase refund','eddpoints'), $pointslable ),
													'post_author'	=> $download->post_author
												);
									$log_meta = array(
														'userpoint'		=>	$total_seller_points,
														'events'		=>	'refunded_sell',
														'operation'		=>	'minus'//add or minus
													);

									//insert entry in log	
									$this->logs->edd_points_insert_logs( $post_data, $log_meta );
								
									//update user points
									edd_points_minus_points_from_user( $total_seller_points, $download->post_author );				
								}
							}
						}	
					}
				}
			}
		}
	}

	/**
	 * Adding Hooks
	 *
	 * Adding proper hoocks for the public pages.
	 *
	 * @package Easy Digital Downloads - Points and Rewards
	 * @since 1.0.0
	 */
	public function add_hooks() {
		
		//Add points for signup
		add_action( 'user_register',	array( $this,'edd_points_add_point_for_signup' ) );

		//Add points for complete purchase
		add_action( 'edd_complete_purchase', array( $this,'edd_points_add_point_for_complete_purchase'), 100, 3 );
		
		//Add points for complete purchase
		add_action( 'edd_complete_purchase', array( $this,'edd_points_add_seller_points_for_complete_purchase'), 100, 3 );
		
		// Add message before content
		add_action( 'edd_before_download_content',array( $this->render,'edd_points_message_content' ) );
		
		// Add message for checkout
		//add_action( 'edd_before_checkout_cart', array( $this->render,'edd_points_checkout_message_content' ) );
		add_action( 'edd_before_purchase_form', array( $this->render,'edd_points_checkout_message_content' ) );
		
		//Add some content before checkout cart
		add_action( 'edd_before_purchase_form', array( $this->render,'edd_points_redeem_point_markup' ) );
		
		//Show message to guest user when he purchased points type product
		add_action( 'edd_before_purchase_form', array( $this->render,'edd_points_buy_points_type_user_message' ) );
		
		//Show message to guest user about points and rewards
		add_action( 'edd_before_purchase_form', array( $this->render,'edd_points_guest_user_message' ) );
		
		// Add menu in admin bar
		add_action( 'admin_bar_menu', array( $this, 'edd_points_tool_bar' ) );
		
		//AJAX Call for paging for points log
		add_action( 'wp_ajax_edd_points_next_page', array( $this->render, 'edd_points_user_log_list' ) );
		add_action( 'wp_ajax_nopriv_edd_points_next_page', array( $this->render, 'edd_points_user_log_list' ) );
		
		//add action which will call when cart will going to empty
		add_action( 'edd_post_remove_from_cart', array( $this, 'edd_points_remove_point_discount' ) );
		
		//add action to calculate fees when user apply the discount agains points
		add_action( 'init', array( $this, 'edd_points_redeem_points' ) );
		
		//Add filter to replace the points template tags for email template
		add_filter( 'edd_email_template_tags', array( $this, 'edd_points_replace_emails_template_tags' ), 10, 3 );
		
		//add action to add error on checkout page when user is guest & going to purchase points download
		add_action( 'edd_checkout_error_checks', array( $this, 'edd_points_download_error' ) );
		
		// add action to add points for recurring payments to buyer
		add_action('edd_recurring_add_subscription_payment', array( $this, 'edd_points_recurring_purchased_payment' ), 10, 2 );
		
		// add action to add points for recurring payments to seller
		add_action('edd_recurring_add_subscription_payment', array( $this, 'edd_points_recurring_seller_payment' ), 10, 2 );
		
		add_filter('edd_ajax_cart_item_quantity_response', array( $this, 'edd_points_cart_modify_fee' ));

		//Action to rate on product
	    add_action( 'wp_insert_comment', array( $this, 'edd_points_rate_on_download' ), 10, 2 );

	    // Action to revert redeemed and earned points when payment is refunded
	    add_action( 'edd_update_payment_status', array( $this, 'edd_points_revert_points_for_payment' ), 10, 3);
	}
}