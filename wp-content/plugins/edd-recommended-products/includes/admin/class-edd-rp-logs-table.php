<?php
/**
 * EDD_RP_Logs_Table Class
 *
 * Renders the file downloads log view
 *
 * @since 1.2.6
 */

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class EDD_RP_Logs_Table extends WP_List_Table {

	/**
	 * Number of items per page
	 *
	 * @var int
	 * @since 1.2.6
	 */
	public $per_page = 15;

	/**
	 * Base URL
	 *
	 * @var int
	 * @since 1.2.6
	 */
	public $base;

	/**
	 * Get things started
	 *
	 * @since 1.2.6
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {
		global $status, $page;

		// Set parent defaults
		parent::__construct( array(
			'singular' => 'recommendation',
			'plural'   => 'recommendations',
			'ajax'     => false,
		) );

		$this->base = admin_url( 'edit.php?post_type=download&page=edd-reports&tab=logs&view=recommendations' );
		add_action( 'edd_log_view_actions', array( $this, 'source_filter' ) );
		add_action( 'edd_log_view_actions', array( $this, 'download_filter' ) );

	}

	/**
	 * Retrieve the table columns
	 *
	 * @access public
	 * @since 1.2.6
	 * @return array $columns Array of all the list table columns
	 */
	public function get_columns() {
		$columns = array(
			'id'       => __( 'ID', 'edd-rp-txt' ),
			'source'   => __( 'Source', 'edd-rp-txt' ),
			'download' => __( 'Purchased', 'edd-rp-txt' ),
			'payment'  => __( 'Payment', 'edd-rp-txt' ),
			'amount'   => __( 'Amount', 'edd-rp-txt' ),
			'date'     => __( 'Date', 'edd-rp-txt' ),
		);
		return $columns;
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @access public
	 * @since 1.2.6
	 *
	 * @param array $item Contains all the data of the discount code
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'id':
				return $item['id'];
			case 'download':
				return '<a href="' . add_query_arg( array( 'download' => $item['download'] ), $this->base ) . '">' . get_the_title( $item['download'] ) . '</a>';
			case 'source':
				return '<a href="' . add_query_arg( array( 'source' => $item['source'] ), $this->base ) . '">' . get_the_title( $item['source'] ) . '</a>';
			case 'payment':
				return '<a href="' . admin_url( 'edit.php?post_type=download&page=edd-payment-history&view=view-order-details&id=' . $item['payment'] ) . '">' . $item['payment'] . '</a>';

			case 'amount':
				return edd_currency_filter( edd_format_amount( $item['amount'] ) );
			default:
				return $item[ $column_name ];
		}
	}

	/**
	 * Retrieves the search query string
	 *
	 * @access public
	 * @since 1.2.6
	 * @return mixed string If search is present, false otherwise
	 */
	public function get_search() {
		return ! empty( $_GET['s'] ) ? urldecode( trim( $_GET['s'] ) ) : false;
	}

	/**
	 * Displayes the source dropdown menu
	 *
	 * @since  1.2.6
	 * @return void
	 */
	public function source_filter() {

		$edd_logs = new EDD_Logging();

		$log_query = array(
			'log_type'       => 'recommendation_sale',
			'posts_per_page' => -1,
			'fields'         => array( 'post_parent' ),
		);

		$logs = $edd_logs->get_connected_logs( $log_query );

		if ( $logs ) {

			foreach ( $logs as $log ) {
				$sources[ $log->post_parent ] = get_the_title( $log->post_parent );
			}

			$dropdown_args = array(
				'options'          => $sources,
				'name'             => 'source',
				'id'               => 'source',
				'selected'         => ! empty( $_GET['source'] ) ? absint( $_GET['source'] ) : false,
				'show_option_all'  => _x( 'All Sources', 'all dropdown items', 'edd-rp-txt' ),
				'show_option_none' => false,
			);

			echo EDD()->html->select( $dropdown_args );

		}
	}

	/**
	 * Displayes the download dropdown menu
	 *
	 * @since  1.2.6
	 * @return void
	 */
	public function download_filter() {
		$downloads = get_posts( array(
			'post_type'      => 'download',
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'fields'         => 'ids',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false
		) );

		$selected = ! empty( $_GET['download'] ) ? absint( $_GET['download'] ) : false;

		if ( $downloads ) {
			echo '<select name="download" id="edd-log-download-filter">';
				echo '<option value="0">' . __( 'All Purchsed Items', 'edd-rp-txt' ) . '</option>';
				foreach ( $downloads as $download ) {
					echo '<option value="' . $download . '"' . selected( $download, $selected ) . '>' . esc_html( get_the_title( $download ) ) . '</option>';
				}
			echo '</select>';
		}
	}

	/**
	 * Gets the meta query for the log query
	 *
	 * This is used to return log entries that match our search query, user query, or download query
	 *
	 * @access public
	 * @since 1.2.6
	 * @return array $meta_query
	 */
	public function get_meta_query() {

		$download  = ! empty( $_GET['download'] ) ? absint( $_GET['download'] ) : false;

		$meta_query = array();

		if ( $download ) {
			// Show only logs from a specific site
			$meta_query[] = array(
				'key'   => '_edd_log_download_id',
				'value' => $download
			);
		}

		return $meta_query;
	}

	/**
	 * Retrieve the current page number
	 *
	 * @access public
	 * @since 1.2.6
	 * @return int Current page number
	 */
	function get_paged() {
		return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	}

	/**
	 * Outputs the log views
	 *
	 * @access public
	 * @since 1.2.6
	 * @return void
	 */
	public function bulk_actions( $which = '' ) {
		// These aren't really bulk actions but this outputs the markup in the right place
		edd_log_views();
	}

	/**
	 * Gets the log entries for the current view
	 *
	 * @access public
	 * @since 1.2.6
	 * @global object $edd_logs EDD Logs Object
	 * @return array $logs_data Array of all the Log entires
	 */
	function get_logs() {
		global $edd_logs;

		// Prevent the queries from getting cached. Without this there are occasional memory issues for some installs
		wp_suspend_cache_addition( true );

		$logs_data = array();
		$paged     = $this->get_paged();
		$log_query = array(
			'log_type'       => 'recommendation_sale',
			'paged'          => $paged,
			'meta_query'     => $this->get_meta_query(),
			'posts_per_page' => $this->per_page,
			'orderby'        => 'ID',
		);

		if ( ! empty( $_GET['source'] ) && is_numeric( $_GET['source'] ) ) {
			$log_query['post_parent'] = absint( $_GET['source'] );
		}

		$logs = $edd_logs->get_connected_logs( $log_query );

		if ( $logs ) {
			foreach ( $logs as $log ) {

				$logs_data[] = array(
					'id'       => $log->ID,
					'payment'  => get_post_meta( $log->ID, '_edd_log_payment_id', true ),
					'date'     => $log->post_date,
					'source'   => $log->post_parent ,
					'download' => get_post_meta( $log->ID, '_edd_log_download_id', true ),
					'amount'   => get_post_meta( $log->ID, '_edd_log_price', true ),
				);
			}
		}

		return $logs_data;
	}

	/**
	 * Setup the final data for the table
	 *
	 * @access public
	 * @since 1.2.6
	 * @global object $edd_logs EDD Logs Object
	 * @return void
	 */
	function prepare_items() {
		global $edd_logs;

		$columns               = $this->get_columns();
		$hidden                = array(); // No hidden columns
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$current_page          = $this->get_pagenum();
		$this->items           = $this->get_logs();
		$total_items           = $edd_logs->get_log_count( null, 'recommendation_sale', $this->get_meta_query() );
		$this->set_pagination_args( array(
				'total_items'  => $total_items,
				'per_page'     => $this->per_page,
				'total_pages'  => ceil( $total_items / $this->per_page )
			)
		);
	}
}
