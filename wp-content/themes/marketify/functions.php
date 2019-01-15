<?php
/**
 * Marketify
 *
 * Do not modify this file. Place all modifications in a child theme.
 */

if ( ! isset( $content_width ) ) {
	$content_width = 680;
}

class Marketify {

	private static $instance;

	public $helpers;

	public $activation;

	public $integrations;
	public $widgets;

	public $template;

	public $page_settings;
	public $widgetized_pages;

	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Marketify ) ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct() {
		$this->base();
		$this->setup();
	}

	// Integration getter helper
	public function get( $integration ) {
		return $this->integrations->get( $integration );
	}

	private function base() {
		$this->files = array(
			'customizer/class-customizer.php',

			'activation/class-activation.php',

			'setup/class-setup.php',

			'class-helpers.php',

			'integrations/class-integration.php',
			'integrations/class-integrations.php',

			'widgets/class-widgets.php',
			'widgets/class-widget.php',

			'template/class-template.php',

			'pages/class-page-settings.php',
			'pages/class-widgetized-page.php',

			'deprecated.php',
		);

		foreach ( $this->files as $file ) {
			require_once( get_template_directory() . '/inc/' . $file );
		}
	}

	private function setup() {
		$this->helpers = new Marketify_Helpers();

		$this->activation = new Marketify_Activation();

		$this->integrations = new Marketify_Integrations();
		$this->widgets = new Marketify_Widgets();

		$this->template = new Marketify_Template();

		$this->widgetized_pages = new Marketify_Widgetized_Pages();

		// $this->page_settings = new Marketify_Page_Settings();
		add_action( 'after_setup_theme', array( $this, 'setup_theme' ) );
	}

	public function setup_theme() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'marketify' );
		load_textdomain( 'marketify', WP_LANG_DIR . "/marketify-$locale.mo" );
		load_theme_textdomain( 'marketify', get_template_directory() . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'title-tag' );

		add_editor_style( 'css/editor-style.css' );

		add_theme_support( 'custom-background', apply_filters( 'marketify_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		if ( apply_filters( 'marketify_hard_crop_images', true ) ) {
			add_image_size( 'medium', get_option( 'medium_size_w' ), get_option( 'medium_size_h' ), true );
			add_image_size( 'large', get_option( 'large_size_w' ), get_option( 'large_size_h' ), true );
		}
	}

}

function marketify() {
	return Marketify::instance();
}

marketify();


/**
 * Get Theme Version.
 *
 * @since 2.13.0
 * @link https://developer.wordpress.org/reference/functions/wp_get_theme/
 *
 * @return string
 */
function marketify_version() {
	// Current active theme data.
	$theme = wp_get_theme( get_template() );

	return $theme->Version;
}
