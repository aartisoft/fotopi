<?php
/**
 * Template Name: Page: Widgetized
 *
 * @package Marketify
 */

get_header(); ?>

	<?php do_action( 'marketify_entry_before' ); ?>

	<div id="content" class="site-content site-content--home" role="main">

		<?php dynamic_sidebar( 'widget-area-page-' . get_post()->ID ); ?>

	</div><!-- #content -->

<?php get_footer(); ?>
