<?php
/**
 * Template Name: Layout: Minimal
 *
 * @package Marketify
 */

get_header( 'minimal' ); ?>

	<div class="site-content container">
		<div id="content" class="content-area col-lg-6 offset-lg-3 col-sm-8 offset-sm-2" role="main">

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', 'page' ); ?>

				<?php endwhile; ?>

			<?php else : ?>

				<?php get_template_part( 'no-results', 'index' ); ?>

			<?php endif; ?>

		</div>
	</div>
	
<?php get_footer( 'minimal' ); ?>
