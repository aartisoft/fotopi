<?php
/**
 * @package Marketify
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-content">
		<?php the_content(); ?>

		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'marketify' ),
				'after'  => '</div>',
			) );
		?>

		<div class="entry-meta entry-meta--hentry entry-meta--footer">
			<?php the_tags( '<span class="entry-tags">', ', ', '</span>' ); ?>
			<span class="entry-categories"><?php the_category( ', ' ); ?></span>
			<?php edit_post_link( __( 'Edit', 'marketify' ), '<span class="edit-link">', '</span>' ); ?>
		</div><!-- .entry-meta -->
	</div><!-- .entry-content -->
</article><!-- #post-## -->
