<?php
/**
 * Template Name: Page: Pricing Table
 *
 * @package Marketify
 */

get_header(); ?>

	<?php do_action( 'marketify_entry_before' ); ?>

	<div class="container site-content">
		<div role="main" id="primary" class="content-area">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php
					the_widget(
						'Marketify_Widget_Price_Table',
						array(
							'title'       => null,
							'description' => null,
						),
						array(
							'widget_id'     => 'widget_price_table_page',
							'before_widget' => '<aside class="widget">',
							'after_widget'  => '</aside>',
							'before_title'  => '<h3 class="widget-title--home"><span>',
							'after_title'   => '</span></h3>',
						)
					);
				?>

				<?php get_template_part( 'content', 'page' ); ?>

			<?php endwhile; ?>

		</div>
	</div>

<?php get_footer(); ?>
