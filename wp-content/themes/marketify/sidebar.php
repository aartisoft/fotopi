<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package Marketify
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<div class="widget-area col-xs-12 col-md-4" role="complementary">
	<?php do_action( 'before_sidebar' ); ?>

	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</div><!-- #secondary -->
