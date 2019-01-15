<?php
/**
 * Theme activation.
 *
 * @since 2.0.0
 */
class Marketify_Activation {

	public function __construct() {
		$this->theme = wp_get_theme( 'marketify' );

		if ( ! isset( $this->theme->Version ) ) {
			$current_child_theme = wp_get_theme();

			if ( false !== $current_child_theme->parent() ) {
				$this->theme = wp_get_theme( $current_child_theme->get_template() );
			} else {
				$this->theme = $current_child_theme;
			}
		}

		$this->version = get_option( 'marketify_version', false );

		if ( $this->version && version_compare( $this->version, '2.0.0', '<' ) ) {
			$this->upgrade( '200' );
		}

		add_action( 'after_switch_theme', array( $this, 'after_switch_theme' ), 10 );
	}

	public function upgrade( $version ) {
		$upgrade = '_upgrade_' . $version;

		if ( method_exists( $this, $upgrade ) ) {
			$this->$upgrade();
		}
	}

	public function after_switch_theme( $theme ) {
		// If it's set just update version can cut out
		if ( get_option( 'marketify_version' ) ) {
			$this->set_version();

			return;
		}

		// to help images import
		update_option( 'medium_size_w', 740 );
		update_option( 'medium_size_h', 600 );

		// dont let EDD create pages
		$edd_settings = get_option( 'edd_settings', array() );
		$edd_settings['purchase_page'] = 0;
		$edd_settings['fes-vendor-dashboard-page'] = 1;
		$edd_settings['fes-vendor-page'] = 1;

		update_option( 'edd_settings', $edd_settings );

		$this->redirect();
	}

	public function set_version() {
		update_option( 'marketify_version', $this->theme->version );
	}

	public function redirect() {
		$this->set_version();

		$template = Marketify_Setup::get_template_name();
		wp_safe_redirect( admin_url( sprintf( 'themes.php?page=%s-setup', $template ) ) );

		exit();
	}

	private function _upgrade_200() {
		update_option( 'medium_size_w', 740 );
		update_option( 'medium_size_h', 600 );

		$theme_mods = get_theme_mods();

		if ( ! $theme_mods ) {
			return;
		}

		foreach ( $theme_mods as $mod => $value ) {
			switch ( $mod ) {
				case 'general-downloads-label-singular' :
					if ( ! $value ) {
						$value = 'Download';
					}

					set_theme_mod( 'download-label-singular', $value );
					set_theme_mod( 'download-label-generate', 'on' );
					break;
				case 'general-downloads-label-plural' :
					if ( ! $value ) {
						$value = 'Downloads';
					}

					set_theme_mod( 'download-label-plural', $value );
					break;
				case 'grid-height' :
					set_theme_mod( 'downloads-archives-grid-height', $value );
					break;
				case 'product-display-columns' :
					set_theme_mod( 'downloads-archives-columns', $value );
					break;
				case 'product-display-single-style' :
					set_theme_mod( 'download-standard-feature-area', $value );
					set_theme_mod( 'download-audio-feature-area', $value );
					set_theme_mod( 'download-video-feature-area', $value );
					break;
				case 'product-display-grid-info' :
					set_theme_mod( 'downloads-archives-meta', $value );
					break;
				case 'product-display-excerpt' :
					set_theme_mod( 'downloads-archives-excerpt', $value );
					break;
				case 'product-display-truncate-title' :
					set_theme_mod( 'downloads-archives-truncate-title', $value );
					break;
				case 'footer-contact-address' :
					set_theme_mod( 'footer-contact-us-adddress', $value );
					break;
				case 'footer-logo' :
					set_theme_mod( 'footer-copyright-logo', $value );
					break;
				case 'header' :
					set_theme_mod( 'color-page-header-background', $value );
					break;
				case 'primary' :
					set_theme_mod( 'color-primary', $value );
					break;
				case 'accent' :
					set_theme_mod( 'color-accent', $value );
					break;
				default:
										break;
			}// End switch().
		}// End foreach().
	}

}
