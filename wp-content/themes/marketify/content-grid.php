<?php
/**
 * @package Marketify
 */

global $post;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'content-grid-download' ); ?>>
	<div class="content-grid-download__entry-image">
		<div class="content-grid-download__overlay">
			<?php do_action( 'marketify_download_content_image_overlay_before' ); ?>

			<div class="content-grid-download__actions">
				<a href="<?php the_permalink(); ?>" rel="bookmark" class="button"><?php _e( 'Read More', 'marketify' ); ?></a>
				<strong class="item-price"><span><?php comments_number( __( '0 Comments', 'marketify' ), __( '1 Comment', 'marketify' ), __( '% Comments', 'marketify' ) ); ?></span></strong>
			</div>
		</div>

		<?php the_post_thumbnail( 'medium' ); ?>
	</div>

	<header class="content-grid-download__entry-header">
		<h1 class="entry-title <?php if ( in_array( get_theme_mod( 'downloads-archives-truncate-title', 'on' ), array( 'on', '1' ) ) ) : ?> entry-title--truncated<?php endif; ?>"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>

		<div class="entry-meta">
			<?php do_action( 'marketify_entry_meta_before_' . get_post_format() ); ?>

			<?php do_action( 'marketify_entry_meta' ); ?>

			<?php do_action( 'marketify_entry_meta_after_' . get_post_format() ); ?>
		</div>
	</header><!-- .entry-header -->
</article><!-- #post-## -->
