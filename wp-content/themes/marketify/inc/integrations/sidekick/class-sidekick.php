<?php
/**
 * Sidekick integration.
 *
 * @package Marketify
 * @category Integration
 * @since 2.4.1
 */
class Marketify_Sidekick extends Marketify_Integration {

	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );

		define( 'SK_PRODUCT_ID', 423 );
		define( 'SK_ENVATO_PARTNER', 'l0+H4H71qrXslK8wNC1lpdDR1NAAs/TcvGAu7MKmfn8=' );
		define( 'SK_ENVATO_SECRET', 'dozca5IuACSFRz35udPSLiGejSpGtgs0P1UM0NG9PKo=' );
	}

}
