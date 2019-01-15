<?php
/**
 * Payment Moderation Table Class
 *
 * @package     EDD_Fraud_Monitor
 * @subpackage  Admin/Payments
 * @copyright   Copyright (c) 2015, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * EDD_Payment_Moderation_Table Class
 *
 * Renders the Payment History table on the Payment History page
 *
 * @since 1.0
 */
class EDD_Payment_Moderation_Table extends WP_List_Table {

	/**
	 * Number of results to show per page
	 *
	 * @var string
	 * @since 1.0
	 */
	public $per_page = 30;

	/**
	 * URL of this page
	 *
	 * @var string
	 * @since 1.0
	 */
	public $base_url;

	/**
	 * Total number of payments
	 *
	 * @var int
	 * @since 1.0
	 */
	public $total_count;

	/**
	 * Get things started
	 *
	 * @since 1.0
	 *
	 * @uses EDD_Payment_Moderation_Table::get_payment_counts()
	 * @see WP_List_Table::__construct()
	 */
	public function __construct() {

		global $status, $page;

		// Set parent defaults
		parent::__construct( array(
			'singular'  => edd_get_label_singular(),
			'plural'    => edd_get_label_plural(),
			'ajax'      => false,
		) );

		$this->get_payment_counts();
		$this->process_bulk_action();
		$this->base_url = admin_url( 'edit.php?post_type=download&page=edd-payment-history' );
	}

	/**
	 * Show the search field
	 *
	 * @since 1.0
	 *
	 * @param string $text Label for the search box
	 * @param string $input_id ID of the search box
	 *
	 * @return void
	 */
	public function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && !$this->has_items() ) {
			return;
		}

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		}

		if ( ! empty( $_REQUEST['order'] ) ) {
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		}
		?>
		<p class="search-box">
			<?php do_action( 'edd_payment_history_search' ); ?>
			<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
			<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
			<?php submit_button( $text, 'button', false, false, array('ID' => 'search-submit') ); ?><br/>
		</p>
		<?php
	}

	/**
	 * Retrieve the view types
	 *
	 * @since 1.0
	 *
	 * @return array $views All the views available
	 */
	public function get_views() {

		$current         = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$total_count     = '&nbsp;<span class="count">(' . $this->total_count    . ')</span>';

		$views = array(
			'all'       => sprintf( '<a href="%s"%s>%s</a>', esc_url( remove_query_arg( array( 'status', 'paged' ) ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __('All', 'edd') . $total_count ),
		);

		return apply_filters( 'edd_payments_table_views', $views );
	}

	/**
	 * Retrieve the table columns
	 *
	 * @since 1.0
	 *
	 * @return array $columns Array of all the list table columns
	 */
	public function get_columns() {
		$columns = array(
			'cb'       => '<input type="checkbox" />', //Render a checkbox instead of text
			'ID'       => __( 'ID', 'edd' ),
			'email'    => __( 'Email', 'edd' ),
			'amount'   => __( 'Amount', 'edd' ),
			'reasons'  => __( 'Reason(s)', 'edd' ),
			'date'     => __( 'Date', 'edd' ),
			'customer' => __( 'Customer', 'edd' ),
			'status'   => __( 'Status', 'edd' ),
		);

		return apply_filters( 'edd_payments_table_columns', $columns );
	}

	/**
	 * Retrieve the table's sortable columns
	 *
	 * @since 1.0
	 *
	 * @return array Array of all the sortable columns
	 */
	public function get_sortable_columns() {
		$columns = array(
			'ID'     => array( 'ID', true ),
			'amount' => array( 'amount', false ),
			'date'   => array( 'date', false ),
		);
		return apply_filters( 'edd_payments_table_sortable_columns', $columns );
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @since 1.0
	 *
	 * @param array $item Contains all the data of the discount code
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	public function column_default( $payment, $column_name ) {
		switch ( $column_name ) {
			case 'amount' :
				$amount  = ! empty( $payment->total ) ? $payment->total : 0;
				$value   = edd_currency_filter( edd_format_amount( $amount ) );
				break;
			case 'reasons' :

				$reasons = $payment->get_meta( '_edd_maybe_is_fraud_reason' );
				$reasons = explode( "\n", $reasons );

				$value = sprintf( _n( '%d Reason', '%d Reasons', count( $reasons ), 'edd-fm' ), count( $reasons ) );

				break;
			case 'date' :
				$date    = strtotime( $payment->date );
				$value   = date_i18n( get_option( 'date_format' ), $date );
				break;
			case 'status' :
				$payment = get_post( $payment->ID );
				$value   = edd_get_payment_status( $payment, true );
				break;
			default:
				$value = isset( $payment->$column_name ) ? $payment->$column_name : '';
				break;

		}
		return apply_filters( 'edd_payments_table_column', $value, $payment->ID, $column_name );
	}

	/**
	 * Render the Email Column
	 *
	 * @since 1.0
	 *
	 * @param array $payment Contains all the data of the payment
	 * @return string Data shown in the Email column
	 */
	public function column_email( $payment ) {

		$row_actions = array();

		$row_actions['edit'] = '<a href="' . esc_url( add_query_arg( array( 'view' => 'view-order-details', 'id' => $payment->ID ), $this->base_url ) ) . '">' . __( 'View/Edit', 'edd' ) . '</a>';

		$row_actions = apply_filters( 'edd_payment_row_actions', $row_actions, $payment );

		if ( ! isset( $payment->user_info['email'] ) ) {
			$payment->user_info['email'] = __( '(unknown)', 'edd' );
		}

		$value = $payment->user_info['email'] . $this->row_actions( $row_actions );

		return apply_filters( 'edd_payments_table_column', $value, $payment->ID, 'email' );
	}

	/**
	 * Render the checkbox column
	 *
	 * @since 1.0
	 *
	 * @param array $payment Contains all the data for the checkbox column
	 * @return string Displays a checkbox
	 */
	public function column_cb( $payment ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			'payment',
			$payment->ID
		);
	}

	/**
	 * Render the User Column
	 *
	 * @since 1.0
	 *
	 * @param array $payment Contains all the data of the payment
	 * @return string Data shown in the User column
	 */
	public function column_customer( $payment ) {

		$customer_id = edd_get_payment_customer_id( $payment->ID );

		$customer = new EDD_Customer( $customer_id );
		$name     = $customer->name;

		$value = '<a href="' . esc_url( add_query_arg( array( 'customer' => $customer_id, 'paged' => false ) ) ) . '">' . $name . '</a>';
		return apply_filters( 'edd_payments_table_column', $value, $payment->ID, 'customer' );
	}

	/**
	 * Retrieve the bulk actions
	 *
	 * @since 1.0
	 *
	 * @return array $actions Array of the bulk actions
	 */
	public function get_bulk_actions() {
		$actions = array(
			'delete'                 => __( 'Delete',                'edd' ),
			'set-status-publish'     => __( 'Set To Completed',      'edd' ),
			'set-status-pending'     => __( 'Set To Pending',        'edd' ),
			'set-status-refunded'    => __( 'Set To Refunded',       'edd' ),
			'set-status-revoked'     => __( 'Set To Revoked',        'edd' ),
			'set-status-failed'      => __( 'Set To Failed',         'edd' ),
			'set-status-abandoned'   => __( 'Set To Abandoned',      'edd' ),
			'set-status-preapproval' => __( 'Set To Preapproval',    'edd' ),
			'set-status-cancelled'   => __( 'Set To Cancelled',      'edd' ),
			'resend-receipt'         => __( 'Resend Email Receipts', 'edd' )
		);

		return apply_filters( 'edd_payments_table_bulk_actions', $actions );
	}

	/**
	 * Process the bulk actions
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function process_bulk_action() {
		$ids    = isset( $_GET['payment'] ) ? $_GET['payment'] : false;
		$action = $this->current_action();

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}


		if( empty( $action ) ) {
			return;
		}

		foreach ( $ids as $id ) {
			// Detect when a bulk action is being triggered...
			if ( 'delete' === $this->current_action() ) {
				edd_delete_purchase( $id );
			}

			if ( 'set-status-publish' === $this->current_action() ) {
				edd_update_payment_status( $id, 'publish' );
			}

			if ( 'set-status-pending' === $this->current_action() ) {
				edd_update_payment_status( $id, 'pending' );
			}

			if ( 'set-status-refunded' === $this->current_action() ) {
				edd_update_payment_status( $id, 'refunded' );
			}

			if ( 'set-status-revoked' === $this->current_action() ) {
				edd_update_payment_status( $id, 'revoked' );
			}

			if ( 'set-status-failed' === $this->current_action() ) {
				edd_update_payment_status( $id, 'failed' );
			}

			if ( 'set-status-abandoned' === $this->current_action() ) {
				edd_update_payment_status( $id, 'abandoned' );
			}

			if ( 'set-status-preapproval' === $this->current_action() ) {
				edd_update_payment_status( $id, 'preapproval' );
			}

			if ( 'set-status-cancelled' === $this->current_action() ) {
				edd_update_payment_status( $id, 'cancelled' );
			}

			if( 'resend-receipt' === $this->current_action() ) {
				edd_email_purchase_receipt( $id, false );
			}

			do_action( 'edd_payments_table_do_bulk_action', $id, $this->current_action() );
		}

	}

	/**
	 * Retrieve all the data for all the payments
	 *
	 * @since 1.0
	 *
	 * @return array $payment_data Array of all the data for the payments
	 */
	public function payments_data() {
		$payments_data = array();

		$page = isset( $_GET['paged'] ) ? $_GET['paged'] : 1;

		$per_page      = $this->per_page;
		$orderby       = isset( $_GET['orderby'] )     ? $_GET['orderby']                           : 'ID';
		$order         = isset( $_GET['order'] )       ? $_GET['order']                             : 'DESC';
		$order_inverse = $order == 'DESC'              ? 'ASC'                                      : 'DESC';
		$order_class   = strtolower( $order_inverse );
		$customer      = isset( $_GET['customer'] )    ? $_GET['customer']                          : null;
		$status        = isset( $_GET['status'] )      ? $_GET['status']                            : 'any';
		$meta_key      = isset( $_GET['meta_key'] )    ? $_GET['meta_key']                          : null;
		$year          = isset( $_GET['year'] )        ? $_GET['year']                              : null;
		$month         = isset( $_GET['m'] )           ? $_GET['m']                                 : null;
		$day           = isset( $_GET['day'] )         ? $_GET['day']                               : null;
		$search        = isset( $_GET['s'] )           ? sanitize_text_field( $_GET['s'] )          : null;
		$start_date    = isset( $_GET['start-date'] )  ? sanitize_text_field( $_GET['start-date'] ) : null;
		$end_date      = isset( $_GET['end-date'] )    ? sanitize_text_field( $_GET['end-date'] )   : $start_date;

		$args = array(
			'output'     => 'payments',
			'number'     => $per_page,
			'page'       => isset( $_GET['paged'] ) ? $_GET['paged'] : null,
			'orderby'    => $orderby,
			'order'      => $order,
			'meta_key'   => '_edd_maybe_is_fraud',
			'year'       => $year,
			'month'      => $month,
			'day'        => $day,
			's'          => $search,
			'start_date' => $start_date,
			'end_date'   => $end_date,
		);

		if ( ! empty( $customer ) ) {
			$customer = new EDD_Customer( $customer );

			$payment_ids = $customer->payment_ids;
			$payment_ids = explode( ',', $payment_ids );

			$args['post__in'] = $payment_ids;
		}

		$p_query  = new EDD_Payments_Query( $args );

		return $p_query->get_payments();

	}

	/**
	 * Get the total count of payments in moderation
	 *
	 * @since  1.0
	 *
	 * @return integer Number of pending payments for moderation
	 */
	private function get_total_count() {
		$args = array(
			'output'     => 'payments',
			'number'     => -1,
			'meta_key'   => '_edd_maybe_is_fraud',
		);

		$customer = isset( $_GET['customer'] ) ? $_GET['customer'] : null;

		if ( ! empty( $customer ) ) {
			$customer = new EDD_Customer( $customer );

			$payment_ids = $customer->payment_ids;
			$payment_ids = explode( ',', $payment_ids );

			$args['post__in'] = $payment_ids;
		}


		$p_query  = new EDD_Payments_Query( $args );

		$all_payments = $p_query->get_payments();

		$this->total_count = count( $all_payments );

		return $this->total_count;
	}

	/**
	 * Setup the final data for the table
	 *
	 * @since 1.0
	 *
	 * @uses EDD_Payment_Moderation_Table::get_columns()
	 * @uses EDD_Payment_Moderation_Table::get_sortable_columns()
	 * @uses EDD_Payment_Moderation_Table::process_bulk_action()
	 * @uses EDD_Payment_Moderation_Table::payments_data()
	 * @uses WP_List_Table::get_pagenum()
	 * @uses WP_List_Table::set_pagination_args()
	 * @return void
	 */
	public function prepare_items() {

		wp_reset_vars( array( 'action', 'payment', 'orderby', 'order', 's' ) );

		$columns  = $this->get_columns();
		$hidden   = array(); // No hidden columns
		$sortable = $this->get_sortable_columns();
		$data     = $this->payments_data();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$total_items = $this->get_total_count();

		$this->items = $data;

		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $this->per_page,
				'total_pages' => ceil( $total_items / $this->per_page ),
			)
		);
	}
}
