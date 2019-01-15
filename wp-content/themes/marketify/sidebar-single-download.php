<?php
/**
 *
 * @package Marketify
 */

if ( ! is_active_sidebar( 'sidebar-download-single' ) ) {
	return;
}
?>
<div class="widget-area widget-area--single-download col-xs-12 col-md-4" role="complementary">
	<?php dynamic_sidebar( 'sidebar-download-single' ); ?>
</div><!-- #secondary -->
