<p><?php _e( 'Manage the appearance and behavior of various theme components with the live customizer.', 'marketify' ); ?></p>

<ul>
	<li><a href="<?php echo esc_url_raw( admin_url( 'customize.php?autofocus[section]=header_image' ) ); ?>"><?php _e( 'Upload a logo', 'marketify' ); ?></a></li>
	<li><a href="<?php echo esc_url_raw( admin_url( 'customize.php?autofocus[section]=colors' ) ); ?>"><?php _e( 'Change colors', 'marketify' ); ?></a></li>
	<?php if ( class_exists( 'Easy_Digital_Downloads' ) ) : ?>
	<li><a href="<?php echo esc_url_raw( admin_url( 'customize.php?autofocus[panel]=downloads' ) ); ?>"><?php _e( 'Adjust download content styles and layout', 'marketify' ); ?></a></li>
	<li><a href="<?php echo esc_url_raw( admin_url( 'customize.php?autofocus[panel]=footer' ) ); ?>"><?php _e( 'Update footer text', 'marketify' ); ?></a></li>
	<?php endif; ?>
</ul>

<p><a href="<?php echo admin_url( 'customize.php' ); ?>" class="button button-primary button-large"><?php _e( 'Launch Customizer', 'marketify' ); ?></a></p>
