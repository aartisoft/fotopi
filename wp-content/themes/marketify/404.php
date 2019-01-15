<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package Marketify
 */

get_header(); ?>

	<?php do_action( 'marketify_entry_before' ); ?>

	<div class="container">
		<div id="content" class="site-content row">

			<div id="primary" class="content-area col-sm-<?php echo is_active_sidebar( 'sidebar-1' ) ? '8' : '12'; ?>">
				<main id="main" class="site-main" role="main">

				<section class="error-404 not-found">
					<div class="page-content">
						<p><?php _e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'marketify' ); ?></p>

						<?php get_search_form(); ?>

						<div class="row not-found-links">
							<div class="col-md-6">
								<?php the_widget( 'WP_Widget_Recent_Posts', array(), array(
									'before_title' => '<h3 class="widget-title">',
									'after_title' => '</h3>',
								) ); ?>
							</div>

							<div class="col-md-6">
								<div class="widget widget_categories">
									<h3 class="widget-title"><?php _e( 'Most Used Categories', 'marketify' ); ?></h3>
									<ul>
									<?php
										wp_list_categories( array(
											'orderby'    => 'count',
											'order'      => 'DESC',
											'show_count' => 1,
											'title_li'   => '',
											'number'     => 10,
										) );
									?>
									</ul>
								</div><!-- .widget -->
							</div>
						</div>

					</div><!-- .page-content -->
				</section><!-- .error-404 -->

				</main><!-- #main -->
			</div><!-- #primary -->

			<?php get_sidebar(); ?>
		</div>
	</div>

<?php get_footer(); ?>
