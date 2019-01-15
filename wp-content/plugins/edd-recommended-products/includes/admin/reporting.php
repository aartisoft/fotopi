<?php

// Silence is golden
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Register the Log View
 *
 * @since  1.2.6
 * @param  array $views Registered Log Views
 * @return array        Views with recommendations registered
 */
function edd_rp_log_views( $views ) {
	$views['recommendations'] = 'Recommendations';
	return $views;
}
add_filter( 'edd_log_views', 'edd_rp_log_views', 10, 1 );

/**
 * Recommendations Log View
 *
 * @since 1.2.6
 * @return void
 */
function edd_rp_logs_view_recommenations() {

	$logs_table = new EDD_RP_Logs_Table();
	$logs_table->prepare_items();

	?>
	<div class="wrap">
		<form id="edd-logs-filter" method="get" action="<?php echo admin_url( 'edit.php?post_type=download&page=edd-reports&tab=logs' ); ?>">
			<?php
			$logs_table->display();
			?>
			<input type="hidden" name="post_type" value="download" />
			<input type="hidden" name="page" value="edd-reports" />
			<input type="hidden" name="tab" value="logs" />
		</form>
	</div>
<?php

}
add_action( 'edd_logs_view_recommendations', 'edd_rp_logs_view_recommenations' );
