<?php
/**
 *
 * @package Marketify
 */

if ( ! is_active_sidebar( 'sidebar-download' ) ) {
	return;
}
?>
<div class="widget-area widget-area--shop col-xs-12 col-md-4" role="complementary">
	<?php dynamic_sidebar( 'sidebar-download' ); ?>
</div>
