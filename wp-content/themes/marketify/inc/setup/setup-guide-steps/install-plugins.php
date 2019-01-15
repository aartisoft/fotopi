<?php
/**
 * Install Plugins
 *
 * @since 2.7.0
 */

global $tgmpa;
?>

<p><?php _e( 'Before you can use Marketify you must first install Easy Digital Downloads at minimum. You can read about <a href="http://marketify.astoundify.com/article/711-why-does-this-theme-require-plugins-to-function">why this theme requires plugins</a> in our documentation.', 'marketify' ); ?></p> 

<p><strong><?php _e( 'Note:', 'marketify' ); ?></strong></p>
<ul>
<li><?php _e( 'Only free plugins/add-ons can be installed automatically. You will need to install any premium plugins/add-ons separately.', 'marketify' ); ?></li>
<li><?php _e( 'Once your plugins are installed and content is imported please review all plugin settings pages to make sure everything has been properly set up.', 'marketify' ); ?></li>
</ul>

<p><a href="<?php echo esc_url( $tgmpa->get_tgmpa_url() ); ?>" class="button button-primary button-large"><?php _e( 'Install Plugins', 'marketify' ); ?></a></p>
