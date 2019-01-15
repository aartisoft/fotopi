<?php
/**
 * The template for displaying search forms in Marketify
 *
 * @package Marketify
 */
?>

<form role="search" method="get" class="search-form<?php echo '' != get_search_query() ? ' active' : ''; ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="s"><?php _ex( 'Search for:', 'label', 'marketify' ); ?></label>
	<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Search', 'marketify' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" title="<?php esc_attr_e( 'Search for:', 'marketify' ); ?>">
	<input type="hidden" name="post_type" value="download" />

	<button type="submit" class="search-submit"><span class="screen-reader-text"><?php _e( 'Search', 'marketify' ); ?></span></button>
	<a href="#" class="js-toggle-search js-toggle-search--close"><span class="screen-reader-text"><?php _e( 'Close', 'marketify' ); ?></span></a>
</form>
