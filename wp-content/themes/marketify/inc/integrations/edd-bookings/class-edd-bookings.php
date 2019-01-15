<?php
/**
 * EDD: Bookings
 *
 * @since 2.9.0
 */
class Marketify_EDD_Bookings extends Marketify_Integration {

	/**
	 * Definte integration.
	 *
	 * @since 2.9.0
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( dirname( __FILE__ ) );
	}

	/**
	 * Hook in to WordPress.
	 *
	 * @since 2.9.0
	 *
	 * @return void
	 */
	public function setup_actions() {
		add_filter( 'marketify_disable_buy_popup', array( $this, 'maybe_enable_buy_popup' ) );
	}

	/**
	 * Maybe enable the Buy Options popup.
	 *
	 * If bookings are enabled we need to force the popup to display as this
	 * is where the actual calendar is output when a standalone button is used.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public function maybe_enable_buy_popup() {
		$post = get_post();

		$service = eddBookings()->getServiceController()->get( $post->ID );
		return ! $service->getBookingsEnabled();
	}

}
