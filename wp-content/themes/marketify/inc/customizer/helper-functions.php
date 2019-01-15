<?php
/**
 * Customizer helper functions and template tags.
 *
 * @package Marketify
 * @category Customizer
 * @since 2.11.0
 */

/**
 * Return a single theme mod, or its default.
 *
 * @since 2.11.0
 *
 * @param string $key The mod key.
 * @return string $mod The mod.
 */
function marketify_theme_mod( $key, $default = null ) {
	return get_theme_mod( $key, $default );
}

/**
 * Stub until full color packs are implemented
 *
 * @since 3.5.0
 *
 * @param string     $key
 * @param $deprecated
 * @return string $mod
 */
function marketify_theme_color( $key, $deprecated = false ) {
	return astoundify_themecustomizer_get_colorscheme_mod( $key );
}

/**
 * Get default control settings for the Typography multi-control.
 *
 * @since 2.11.0
 *
 * @return array $controls
 */
function marketify_themecustomizer_get_default_typography_controls() {
	return array(
		'font-family' => array(
			'label' => __( 'Font Family', 'marketify' ),
			'placeholder' => __( 'Search for a font...', 'marketify' ),
		),
		'font-size' => array(
			'label' => __( 'Font Size', 'marketify' ),
		),
		'font-weight' => array(
			'label' => __( 'Font Weight', 'marketify' ),
			'choices' => array(
				'normal' => __( 'Normal', 'marketify' ),
				'bold' => __( 'Bold', 'marketify' ),
			),
		),
		'line-height' => array(
			'label' => __( 'Line Height', 'marketify' ),
		),
	);
}

/**
 * Get the customizable typography elements.
 *
 * @since 2.11.0
 *
 * @return array $elements
 */
function marketify_themecustomizer_get_typography_elements() {
	$elements = array(
		'body' => _x( 'Global', 'customizer section title', 'marketify' ),
		'page-header' => _x( 'Page Headers', 'customizer section title', 'marketify' ),
		'entry-title' => _x( 'Blog Post Titles', 'customizer section title', 'marketify' ),
		'widget-title' => _x( 'Widget Titles', 'customizer section title', 'marketify' ),
		'home-widget-title' => _x( 'Homepage Widget Titles', 'customizer section title', 'marketify' ),
		'button' => _x( 'Buttons', 'customizer section title', 'marketify' ),
		'input' => _x( 'Inputs', 'customizer section title', 'marketify' ),
	);

	return $elements;
}
