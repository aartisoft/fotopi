<?php
/**
 *
 */

global $post;
?>

<header class="content-grid-download__entry-header">
	<?php do_action( 'marketify_download_entry_title_before_' . get_post_format() ); ?>

	<h3 class="entry-title<?php if ( in_array( get_theme_mod( 'downloads-archives-truncate-title', 'on' ), array( 'on', '1' ) ) ) : ?> entry-title--truncated<?php endif; ?>"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>

	<?php do_action( 'marketify_download_entry_title_after_' . get_post_format() ); ?>

	<?php if ( get_theme_mod( 'downloads-archives-excerpt', '' ) && $post->post_excerpt ) : ?>
		<div class="entry-excerpt"><?php echo esc_attr( wp_trim_words( $post->post_excerpt ) ); ?></div>
	<?php endif; ?>

	<div class="entry-meta"><?php do_action( 'marketify_download_entry_meta_before_' . get_post_format() ); ?><?php do_action( 'marketify_download_entry_meta' ); ?><?php do_action( 'marketify_download_entry_meta_after_' . get_post_format() ); ?></div>
</header><!-- .entry-header -->
