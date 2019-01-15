<?php

class Marketify_Template_Assets {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 200 ); // late because plugins are crazy

		add_filter( 'mce_css', array( $this, 'mce_css' ) );

		// Remove the auto output of inline styles that happen too early by default.
		// @see https://github.com/Astoundify/theme-customizer/blob/master/app/output/manager.php#L50
		$customizer = new Astoundify_ThemeCustomizer_Manager();
		remove_action( 'wp_enqueue_scripts', array( $customizer->output(), 'wp_enqueue_scripts' ), 20 );
	}

	public function enqueue_scripts() {
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		wp_enqueue_script( 'marketify', get_template_directory_uri() . '/js/marketify.min.js', array( 'jquery' ), marketify_version(), true );

		wp_localize_script( 'marketify', 'Marketify', apply_filters( 'marketify_js', array(
			'widgets' => array(
				'testimonials' => array(
					'individualSliderSpeed' => 3000,
				),
			),
		) ) );
	}

	public function enqueue_styles() {
		$fonts_url = $this->google_fonts_url();

		if ( ! empty( $fonts_url ) ) {
			wp_enqueue_style( 'marketify-fonts', esc_url_raw( $fonts_url ), array(), marketify_version() );
		}

		wp_enqueue_style( 'marketify-base', get_template_directory_uri() . '/style.css', array(), marketify_version() );
		wp_add_inline_style( 'marketify-base', astoundify_themecustomizer_get_css() );
	}

	public function mce_css( $mce_css ) {
		$fonts_url = $this->google_fonts_url();

		if ( empty( $fonts_url ) ) {
			return $mce_css;
		}

		if ( ! empty( $mce_css ) ) {
			$mce_css .= ',';
		}

		$mce_css .= esc_url_raw( str_replace( ',', '%2C', $fonts_url ) );

		return $mce_css;
	}

	private function google_fonts_url() {
		return astoundify_themecustomizer_get_googlefont_url();
	}

}
