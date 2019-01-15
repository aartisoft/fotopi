<?php
/**
 */
?>

<form id="astoundify-content-importer" action="" method="">

	<div style="display: none;">

	<p>
		<strong><?php _e( 'Content Pack:', 'marketify' ); ?></strong>
	</p>

	<p>
		<label for="default">
			<input type="radio" value="default" name="demo_style" id="default" checked="checked">
			<?php _e( 'Default', 'marketify' ); ?>
		</label><br />
		<label for="audio">
			<input type="radio" value="audio" name="demo_style" id="audio">
			<?php _e( 'Audio Marketplace', 'marketify' ); ?>
		</label>
	</p>

	</div>


	<div id="import-summary" style="display: none;">
		<p><?php _e( 'Please do not navigate away from this page.', 'marketify' ); ?></p>

		<p><strong id="import-status"><?php _e( 'Summary:', 'marketify' ); ?></strong></p>

		<?php foreach ( Marketify_Setup::$content_importer_strings['type_labels'] as $key => $labels ) : ?>
		<p id="import-type-<?php echo esc_attr( $key ); ?>" class="import-type">
			<span class="dashicons import-type-<?php echo esc_attr( $key ); ?>"></span>&nbsp;
			<strong class="process-type"><?php echo esc_attr( $labels[1] ); ?>:</strong>
			<span class="process-count">
				<span id="<?php echo esc_attr( $key ); ?>-processed">0</span> / <span id="<?php echo esc_attr( $key ); ?>-total">0</span>
			</span>
			<span id="<?php echo esc_attr( $key ); ?>-spinner" class="spinner"></span>
		</p>
		<?php endforeach; ?>
	</div>

	<ul id="import-errors"></ul>

	<div id="plugins-to-import">
		<p><?php _e( 'Marketify can import content for recommended plugins, but only if they are active. Please review the list of plugins below and activate any plugins you would like the content imported for.', 'marketify' ); ?> <a href="<?php echo admin_url( 'themes.php?page=tgmpa-install-plugins' ); ?>"><?php _e( 'Install or activate plugins &rarr;', 'marketify' ); ?></a></p>

		<ul>
		<?php foreach ( Marketify_Setup::get_importable_plugins() as $key => $plugin ) : ?>
		<li>
			<?php if ( $plugin['condition'] ) : ?>
				<span class="active"><span class="dashicons dashicons-yes"></span></span>
			<?php else : ?>
				<span class="inactive"><span class="dashicons dashicons-no"></span></span>
			<?php endif; ?>

			<?php echo $plugin['label']; ?>

			<?php if ( ! $plugin['condition'] ) : ?>
			&mdash; <span class="inactive"><?php _e( 'Demo content for this plugin will not be imported.', 'marketify' ); ?></span>
			<?php endif; ?>
		</li>
		<?php endforeach; ?>
		</ul>
	</div>

	<?php submit_button( __( 'Import Content', 'marketify' ), 'primary', 'import', false ); ?>
	&nbsp;
	<?php submit_button( __( 'Reset Content', 'marketify' ), 'secondary', 'reset', false ); ?>

</form>
