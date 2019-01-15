<?php
/**
 * @package Marketify
 */

// Are we on a homepage widget?
$is_home = is_page_template( 'page-templates/home.php' ) || is_page_template( 'page-templates/home-search.php' );

global $more;

$more = 0;
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header entry-header--hentry">
		<h3 class="entry-title entry-title--hentry"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>


		<div class="entry-meta entry-meta--hentry">
			<?php get_template_part( 'content', 'entry-meta' ); ?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
</article><!-- #post-## -->
