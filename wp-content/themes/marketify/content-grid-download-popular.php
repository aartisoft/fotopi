<?php
/**
 * Popular downloads
 *
 * @since Marketify 1.0
 */

if ( ! marketify()->get( 'edd' )->popular->show_popular() ) {
	return;
}
?>

<div class="widget widget--home marketify-widget--featured-popular marketify_widget_featured_popular popular">

	<h3 class="section-title"><span><?php echo apply_filters( 'marketify_get_the_archive_title', get_the_archive_title() ); ?></span></h3>

	<div class="featured-popular-tabs">
		<div id="items-popular" class="inactive featured-popular-slick">
			<?php echo do_shortcode( '[downloads number=6 flat=true orderby=sales pagination=false]' ); ?>
		</div>
	</div>

</div>
