<?php
/**
 * Plugin integrations.
 */

abstract class Marketify_Integration {

	public $includes = array();

	public $directory;

	public function __construct( $directory ) {
		$this->directory = $directory;

		$this->includes();
		$this->start();
		$this->internal_actions();
	}

	private function includes() {
		if ( empty( $this->includes ) ) {
			return;
		}

		foreach ( $this->includes as $file ) {
			require_once( trailingslashit( $this->directory ) . $file );
		}
	}

	public function start() {
		if ( method_exists( $this, 'init' ) ) {
			add_action( 'after_setup_theme', array( $this, 'init' ), 0 );
		}

		if ( method_exists( $this, 'setup_actions' ) ) {
			add_action( 'after_setup_theme', array( $this, 'setup_actions' ), 0 );
		}
	}

	private function internal_actions() {
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}

	public function body_class( $classes ) {
		$classes[] = 'marketify-plugin-' . $this->get_slug();

		return $classes;
	}

	private function get_slug() {
		return basename( $this->directory );
	}

	public function get_url() {
		return trailingslashit( get_template_directory_uri() . '/inc/integrations/' . $this->get_slug() );
	}

}
