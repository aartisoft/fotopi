<?php
/**
 * Template Name: Page: Home
 *
 * @package Marketify
 */

get_header(); ?>

	<?php do_action( 'marketify_entry_before' ); ?>

	<div id="content" class="site-content site-content--home" role="main">

		<?php
		if ( ! dynamic_sidebar( 'home-1' ) ) :
			$args = array(
			'before_widget' => '<aside class="widget widget--home container">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title widget-title--home section-title"><span>',
			'after_title'   => '</span></h3>',
			);

			the_widget( 'Marketify_Widget_Recent_Downloads', array(
				'title' => 'Recent Downloads',
			), $args );
			endif;
		?>

	</div><!-- #content -->

<?php get_footer(); ?>
