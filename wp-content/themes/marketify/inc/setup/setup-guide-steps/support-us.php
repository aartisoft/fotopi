<p><?php _e( 'Help improve Marketify by submitting a rating and helping to translate the theme to as many languages as possible!', 'marketify' ); ?></p>

<p>
	<a href="https://astoundify.com/go/rate-marketify/" class="button button-secondary button-large"><?php _e( 'Rate us on ThemeForest', 'marketify' ); ?></a>
	&nbsp;
	<a href="https://astoundify.com/go/translate-marketify/" class="button button-secondary button-large"><?php _e( 'Help Translate', 'marketify' ); ?></a>
</p>

<?php if ( ! get_option( 'astoundify_setup_guide_hidden', false ) ) : ?>
<p>
	<a href="<?php echo esc_url( Astoundify_Setup_Guide::get_hide_menu_item_url() ); ?>"><em><?php _e( 'Move this guide under the "Appearance" menu item', 'marketify' ); ?></em></a>
</p>
<?php endif; ?>
