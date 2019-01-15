<?php
/**
 *
 */

global $post;
?>

<?php do_action( 'marketify_download_content_image_before' ); ?>

<div class="content-grid-download__entry-image">
	<div class="content-grid-download__overlay">
		<?php do_action( 'marketify_download_content_image_overlay_before' ); ?>

		<div class="content-grid-download__actions">
			<?php do_action( 'marketify_download_content_actions_before' ); ?>

			<a href="<?php the_permalink(); ?>" rel="bookmark" class="button button--color-white"><?php _e( 'Details', 'marketify' ); ?></a>

			<strong class="item-price"><span><?php printf( __( 'Item Price: %s', 'marketify' ), edd_price( get_the_ID(), false ) ); ?></span></strong>

			<?php do_action( 'marketify_download_content_actions_after' ); ?>
		</div>
	</div>

	<?php the_post_thumbnail( 'medium' ); ?>
</div>

<?php locate_template( array( 'modal-download-purchase.php' ), true, false ); ?>

<?php do_action( 'marketify_download_content_image_after' ); ?>
