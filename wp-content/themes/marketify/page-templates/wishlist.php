<?php
/**
 * Template Name: Account: Loves
 *
 * @package Marketify
 */

$author = get_query_var( 'author_wishlist' );

if ( ! $author ) {
	$author = get_current_user_id();
}

$author = new WP_User( $author );

get_header(); ?>

	<?php do_action( 'marketify_entry_before' ); ?>

	<div class="container">
		<div id="content" class="site-content row">

			<div id="secondary" class="author-widget-area col-md-3 col-sm-5 col-xs-12" role="complementary">
				<div class="vendor-widget-area">
					<?php
					if ( ! dynamic_sidebar( 'sidebar-vendor' ) ) :
						$args = array(
						'before_widget' => '<aside class="widget widget--vendor-profile widget-detail">',
						'after_widget'  => '</aside>',
						'before_title'  => '<h3 class="widget-title widget-title--vendor-profile">',
						'after_title'   => '</h3>',
						);

						the_widget( 'Marketify_Widget_FES_Vendor', array(
							'extras' => '',
						), $args );
						endif;
					?>
					<aside class="widget widget--vendor-profile widget-detail">
						<?php
							$loves = get_user_option( 'li_user_loves', $author->ID );

						if ( ! is_array( $loves ) ) {
							$loves = array();
						}
						?>

						<strong class="widget-detail__title"><?php echo count( $loves ); ?></strong>
						<span class="widget-detail__info"><?php echo _n( 'Love', 'Loves', count( $loves ), 'marketify' ); ?></span>
					</aside>
				</div>
			</div><!-- #secondary -->

			<div role="main" class="content-area col-md-9 col-sm-7 col-xs-12">

				<?php while ( have_posts() ) : the_post(); ?>
					<?php the_content(); ?>
				<?php endwhile; ?>

			</div>

		</div><!-- #content -->
	</div>

<?php get_footer(); ?>
