<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $wpdb;
?>

<div id="message" class="updated fade" style="display:none"></div>

<div class="wrap eddimgwtm_regenthumbs">
	<h2><?php _e('Regenerate Thumbnails', 'eddimgwtm'); ?></h2>

	<?php
		// If the button was clicked
		if ( ! empty( $_POST['eddimgwtm_regen_thumb'] ) || ! empty( $_REQUEST['ids'] ) ) {

			// Form nonce check
			check_admin_referer( 'eddimgwtm_regen_thumb' );

			// Create the list of image IDs
			if ( ! empty( $_REQUEST['ids'] ) ) {
				$images = array_map( 'intval', explode( ',', trim( $_REQUEST['ids'], ',' ) ) );
				$ids 	= implode( ',', $images );
			} else {
				// Directly querying the database is normally frowned upon, but all
				// of the API functions will return the full post objects which will
				// suck up lots of memory. This is best, just not as future proof.
				if ( ! $images = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%' ORDER BY ID DESC" ) ) {
					echo '	<p>' . sprintf( __( "Unable to find any images. Are you sure <a href='%s'>some exist</a>?", 'eddimgwtm' ), admin_url( 'upload.php?post_mime_type=image' ) ) . "</p></div>";
					return;
				}

				// Generate the list of IDs
				$ids = array();
				foreach ( $images as $image )
					$ids[] = $image->ID;
				$ids = implode( ',', $ids );
			}

			echo '	<p>' . __( "Please be patient while the thumbnails are regenerated. This can take a while if your server is slow (inexpensive hosting) or if you have many images. Do not navigate away from this page until this script is done or the thumbnails will not be resized. You will be notified via this page when the regenerating is completed.", 'eddimgwtm' ) . '</p>';

			$count = count( $images );

			$text_goback 	 = 
			$text_failures   = sprintf( __( 'All done! %1$s image(s) were successfully resized in %2$s seconds and there were %3$s failure(s).', 'eddimgwtm' ), "' + eddimgwtm_rt_successes + '", "' + eddimgwtm_rt_totaltime + '", "' + eddimgwtm_rt_errors + '" );
			$text_nofailures = sprintf( __( 'All done! %1$s image(s) were successfully resized in %2$s seconds and there were 0 failures.', 'eddimgwtm' ), "' + eddimgwtm_rt_successes + '", "' + eddimgwtm_rt_totaltime + '" );
			?>

			<div id="eddimgwtm_regen_thumb_bar" style="position:relative;height:25px;">
				<div id="eddimgwtm_regen_thumb_bar_percent" style="position:absolute;left:50%;top:50%;width:300px;margin-left:-150px;height:25px;margin-top:-9px;font-weight:bold;text-align:center;"></div>
			</div>

			<p><input type="button" class="button hide-if-no-js" name="eddimgwtm_regen_thumb_stop" id="eddimgwtm_regen_thumb_stop" value="<?php _e( 'Abort Resizing Images', 'eddimgwtm' ) ?>" /></p>

			<h3 class="title"><?php _e( 'Debugging Information', 'eddimgwtm' ) ?></h3>

			<p>
				<?php printf( __( 'Total Images: %s', 'eddimgwtm' ), $count ); ?><br />
				<?php printf( __( 'Images Resized: %s', 'eddimgwtm' ), '<span id="eddimgwtm_regen_thumb_success">0</span>' ); ?><br />
				<?php printf( __( 'Resize Failures: %s', 'eddimgwtm' ), '<span id="eddimgwtm_regen_thumb_failure">0</span>' ); ?>
			</p>

			<ol id="eddimgwtm_regen_thumb_debuglist">
				<li style="display:none"></li>
			</ol>

			<script type="text/javascript">
				jQuery(document).ready(function($){
					var i;
					var eddimgwtm_rt_images = [<?php echo $ids; ?>];
					var eddimgwtm_rt_total  = eddimgwtm_rt_images.length;
					var eddimgwtm_rt_count = 1;
					var eddimgwtm_rt_percent = 0;
					var eddimgwtm_rt_successes = 0;
					var eddimgwtm_rt_errors = 0;
					var eddimgwtm_rt_failedlist = '';
					var eddimgwtm_rt_resulttext = '';
					var eddimgwtm_rt_timestart = new Date().getTime();
					var eddimgwtm_rt_timeend = 0;
					var eddimgwtm_rt_totaltime = 0;
					var eddimgwtm_rt_continue = true;

					// Create the progress bar
					$("#eddimgwtm_regen_thumb_bar").progressbar();
					$("#eddimgwtm_regen_thumb_bar_percent").html( "0%" );

					// Stop button
					$("#eddimgwtm_regen_thumb_stop").click(function() {
						eddimgwtm_rt_continue = false;
						$('#eddimgwtm_regen_thumb_stop').val("<?php _e( 'Stopping...', 'eddimgwtm' ); ?>");
					});

					// Clear out the empty list element that's there for HTML validation purposes
					$("#eddimgwtm_regen_thumb_debuglist li").remove();

					// Called after each resize. Updates debug information and the progress bar.
					function RegenThumbsUpdateStatus( id, success, response ) {
						$("#eddimgwtm_regen_thumb_bar").progressbar( "value", ( eddimgwtm_rt_count / eddimgwtm_rt_total ) * 100 );
						$("#eddimgwtm_regen_thumb_bar_percent").html( Math.round( ( eddimgwtm_rt_count / eddimgwtm_rt_total ) * 1000 ) / 10 + "%" );
						eddimgwtm_rt_count = eddimgwtm_rt_count + 1;

						if ( success ) {
							eddimgwtm_rt_successes = eddimgwtm_rt_successes + 1;
							$("#eddimgwtm_regen_thumb_success").html(eddimgwtm_rt_successes);
							$("#eddimgwtm_regen_thumb_debuglist").append("<li>" + response.success + "</li>");
						} else {
							eddimgwtm_rt_errors = eddimgwtm_rt_errors + 1;
							eddimgwtm_rt_failedlist = eddimgwtm_rt_failedlist + ',' + id;
							$("#eddimgwtm_regen_thumb_failure").html(eddimgwtm_rt_errors);
							$("#eddimgwtm_regen_thumb_debuglist").append("<li>" + response.error + "</li>");
						}
					}

					// Called when all images have been processed. Shows the results and cleans up.
					function RegenThumbsFinishUp() {
						eddimgwtm_rt_timeend = new Date().getTime();
						eddimgwtm_rt_totaltime = Math.round( ( eddimgwtm_rt_timeend - eddimgwtm_rt_timestart ) / 1000 );
		
						$('#eddimgwtm_regen_thumb_stop').hide();
		
						if ( eddimgwtm_rt_errors > 0 ) {
							eddimgwtm_rt_resulttext = '<?php echo $text_failures; ?>';
						} else {
							eddimgwtm_rt_resulttext = '<?php echo $text_nofailures; ?>';
						}
		
						$("#message").html("<p><strong>" + eddimgwtm_rt_resulttext + "</strong></p>");
						$("#message").show();
					}

					// Regenerate a specified image via AJAX
					function RegenThumbs( id ) {
						$.ajax({
							type: 'POST',
							url: ajaxurl,
							data: { action: "edd_img_wtm_regenerate_thumb_process", id: id },
							success: function( response ) {
								if ( response !== Object( response ) || ( typeof response.success === "undefined" && typeof response.error === "undefined" ) ) {
									response = new Object;
									response.success = false;
								}

								if ( response.success ) {
									RegenThumbsUpdateStatus( id, true, response );
								}
								else {
									RegenThumbsUpdateStatus( id, false, response );
								}

								if ( eddimgwtm_rt_images.length && eddimgwtm_rt_continue ) {
									RegenThumbs( eddimgwtm_rt_images.shift() );
								}
								else {
									RegenThumbsFinishUp();
								}
							},
							error: function( response ) {
								RegenThumbsUpdateStatus( id, false, response );
		
								if ( eddimgwtm_rt_images.length && eddimgwtm_rt_continue ) {
									RegenThumbs( eddimgwtm_rt_images.shift() );
								}
								else {
									RegenThumbsFinishUp();
								}
							}
						});
					}

					RegenThumbs( eddimgwtm_rt_images.shift() );
				});
			</script>
		<?php
		} else {
		?>
			<form method="post" action="">
				<?php wp_nonce_field('eddimgwtm_regen_thumb') ?>

			<p><?php _e( "Thumbnail regeneration is not reversible, but you can just change your thumbnail dimensions back to the old values and click the button again if you don't like the results.", 'eddimgwtm' ); ?></p>

			<p><?php _e( 'To begin, just press the button below.', 'eddimgwtm'); ?></p>

			<p><input type="submit" class="button hide-if-no-js" name="eddimgwtm_regen_thumb" id="regenerate-thumbnails" value="<?php _e( 'Regenerate All Thumbnails', 'eddimgwtm' ) ?>" /></p>

			</form>
		<?php
		} ?>
		</div>