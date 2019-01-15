<?php
/**
 * Steps for the setup guide.
 *
 * @since 2.7.0
 */

/** Create the steps */
$steps = array();

if ( ! wp_get_theme()->parent() ) {
	$steps['child-theme'] = array(
		'title' => __( 'Enable Child Theme', 'marketify' ),
		'completed' => wp_get_theme()->parent(),
	);
}

$api = Astoundify_Envato_Market_API::instance();

$steps['theme-updater'] = array(
	'title' => __( 'Enable Automatic Updates', 'marketify' ),
	'completed' => $api->can_make_request_with_token(),
);

$steps['install-plugins'] = array(
	'title' => __( 'Install Plugins', 'marketify' ),
	'completed' => class_exists( 'Easy_Digital_Downloads' ),
);

if ( current_user_can( 'import' ) ) {
	$steps['import-content'] = array(
		'title' => __( 'Import Content', 'marketify' ),
		'completed' => get_option( 'page_for_posts' ),
	);
}

$steps['customize-theme'] = array(
	'title' => __( 'Customize', 'marketify' ),
	'completed' => 'n/a',
);

$steps['support-us'] = array(
	'title' => __( 'Get Involved', 'marketify' ),
	'completed' => 'n/a',
);

return $steps;
