<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package Marketify
 */
?>

	<footer id="colophon" class="site-footer site-footer--<?php echo esc_attr( get_theme_mod( 'footer-style', 'light' ) ); ?>" role="contentinfo">
		<div class="container">
			<?php do_action( 'marketify_footer_above' ); ?>

			<div class="footer-widget-areas">
				<div class="widget widget--site-footer">
					<?php dynamic_sidebar( 'footer-1' ); ?>
				</div>

				<div class="widget widget--site-footer">
					<?php dynamic_sidebar( 'footer-2' ); ?>
				</div>

				<div class="widget widget--site-footer">
					<?php dynamic_sidebar( 'footer-3' ); ?>
				</div>
			</div>

			<?php do_action( 'marketify_footer_site_info' ); ?>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
