<?php


/**
 * Enqueue the block's assets for the editor.
 *
 */
function edd_social_login_editor_assets(){

	// Enqueue block editor scripts
	wp_register_script(
		'edd-social-login-block-js',
		plugins_url( 'block.js', __FILE__ ),
		array( 'wp-blocks', 'wp-components', 'wp-element', 'wp-i18n', 'wp-editor' ), 
		filemtime( plugin_dir_path( __FILE__ ) . 'block.js' )
	);

	// Enqueue block editor styles
    wp_enqueue_style(
        'edd-social-login-block-css',
        plugins_url( 'social-login-block.css', __FILE__ ),
        filemtime( plugin_dir_path( __FILE__ ) . 'social-login-block.css' ) 
    );

}
add_action( 'enqueue_block_editor_assets', 'edd_social_login_editor_assets' );


/**
* Handle Easy Digital Downloads - Social Login Block Registering
*
*/
function edd_register_social_login_block() {

    if ( function_exists( 'register_block_type' ) ) {
	    
	    $args = array(
			
			'editor_script'   => 'edd-social-login-block-js' ,
			'attributes'      => array(
	            'title'    => array(
	                'type'      => 'text',
	                'default'   => __('Prefer to Login with Social Media', 'eddslg'),
	            ),
	            'redirect_url' => array(
	                'type'      => 'url',
	                'default'   => '',
	            ),
	            'showonpage' => array(
	                'type'      => 'boolean',
	                'default'   => false,
	            ),
	            
	        ),
			'render_callback' => 'edd_render_block_social_login',

		);
	    
		// register edd social login custom block
		register_block_type( 'wpweb/edd-social-login-block', $args );
	}

}
add_action( 'init', 'edd_register_social_login_block' );

/**
* Handle Easy Digital Downloads - Social Login Block Rendering
*
*/
function edd_render_block_social_login( $attributes) {

	global $edd_slg_render,$edd_slg_model, $edd_options, $post ;

	extract( $attributes );

	$content = '';

	$showblock = false;

    // check if user is not login or access via admin block
    if ( !is_user_logged_in() || isset( $_GET['context'] ) && $_GET['context'] == 'edit' ) {
	   $showblock = true;
    }

	$showbuttons = true;
	
	// if show only on inners pages is set and current page is not inner page 
	if( !empty( $showonpage ) &&  !is_singular() ) {
		
		$showbuttons = false; 
		
		if( isset( $_GET['context'] ) && $_GET['context'] == 'edit' ) {
			$showbuttons = true; 
		}
	}
	
	//check show social buttons or not
	if( $showbuttons ) {
		
		//check user is logged in to site or not and any single social login button is enable or not
		if( $showblock && edd_slg_check_social_enable() ) {
			
			// login heading from setting page
			$login_heading = isset( $edd_options['edd_slg_login_heading'] ) ? $edd_options['edd_slg_login_heading'] : '';
			//  check title first from shortcode
			$login_heading = !empty( $title ) ? $title : $login_heading;
			
			// get redirect url from settings 
			$defaulturl = isset( $edd_options['edd_slg_redirect_url'] ) && !empty( $edd_options['edd_slg_redirect_url'] ) 
								? $edd_options['edd_slg_redirect_url'] : edd_slg_get_current_page_url();
			
			//redirect url for shortcode
			$defaulturl = isset( $redirect_url ) && !empty( $redirect_url ) ? $redirect_url : $defaulturl; 
			
			//session create for access token & secrets		
			EDD()->session->set( 'edd_slg_stcd_redirect_url', $defaulturl );
			
			// get html for all social login buttons
			ob_start();
			
			echo '<fieldset id="edd_slg_social_login" class="edd-slg-social-container">';
			if( !empty($login_heading) ) {
				echo '<span><legend>'. $login_heading.'</legend></span>';
			}
			
			$edd_slg_render->edd_slg_social_login_inner_buttons( $redirect_url );
			
			echo '</fieldset><!--#edd_slg_social_login-->';
			
			$content .= ob_get_clean();
		} else {
			
			if( isset( $_GET['context'] ) && $_GET['context'] == 'edit' ) {
				ob_start();

				echo '<div class="edd_no_content">Please make sure atleast one of the social network is enabled. <a href="edit.php?post_type=download&page=edd-settings&tab=extensions">Click Here</a> to configure social networks.</div>';

				$content .= ob_get_clean();
			}
		}
	}
	return $content;
}

