<?php
/**
 * Report Generation
 *
 * @package         EDD\EDD_Advanced_Reports
 * @author          Manuel Vicedo
 * @copyright       Copyright (c) Manuel Vicedo
 * @since     		1.0.0
 *
 */
 

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;


/**
 * Add Advanced Reports tab to the Reports page
 *
 * @access      public
 * @since       1.0.0
 */
function edd_advanced_reports_tab( ){
	$current_page = admin_url( 'edit.php?post_type=download&page=edd-reports' );
	$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'reports';
	?>
	<a href="<?php echo add_query_arg( array( 'tab' => 'advanced_reports', 'settings-updated' => false ), $current_page ); ?>" class="nav-tab <?php echo $active_tab == 'advanced_reports' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Advanced Reports', 'eddar' ); ?></a>
	<?php
}


/**
 * Create reports page for advanced reports, inside EDD Reports section
 *
 * @access      public
 * @since       1.0.0
 */
function edd_advanced_reports_page( ){
	
	if( ! current_user_can( 'view_shop_reports' ) ) {
		wp_die( __( 'You do not have permission to access this report', 'edd'  ), __( 'Error', 'edd' ), array( 'response' => 403 ) );
	}
	
	$report_list = new WP_Query('post_type=edd_advanced_report&posts_per_page=-1');
	if($report_list->posts){
		$report_id = isset($_GET['view']) ? $_GET['view'] : false; 
		//Get last element if no valid report given
		if(!$report_id){
			foreach($report_list->posts as $current_report){
				$report_id = $current_report->ID; break;
			}
		}
		?>
		<div class="postbox" style="margin:20px 0;">
			<div class="inside">
				<h3 class="alignleft" style="margin:0 0 20px;">
					<?php _e('Advanced Reports', 'eddar'); ?>
					<a class="add-new-h2" href="edit.php?post_type=edd_advanced_report"><?php _e('Manage Reports', 'eddar'); ?></a>
				</h3>
				<div class="alignright actions"><?php edd_advanced_reports_selector($report_list, $report_id); ?></div>
				<div style="clear:both;"></div>
				<?php edd_advanced_reports_dates($report_id); ?>
				<?php $data = edd_advanced_reports_generate($report_id); ?>
				<?php edd_advanced_reports_graph($data); ?>
				<?php edd_advanced_reports_total($data); ?>
				<?php edd_advanced_reports_table($data); ?>
				<strong style="margin:20px 0 0; text-align:right;">
					<a href="post.php?action=edit&post=<?php echo $report_id; ?>"><?php _e('Edit Report', 'eddar'); ?></a>
				</strong>
			</div>
		</div>
		<?php
	}else{
		?>
		<div class="postbox" style="margin:20px 0;">
			<div class="inside">
				<h3 class="alignleft" style="margin:0 0 20px;">
					<?php _e('Advanced Reports', 'eddar'); ?>
				</h3>
				<p style="clear:both;"><?php _e('No advanced reports have been created. Please create one first.', 'eddar'); ?></p>
				<a class="button" href="post-new.php?post_type=edd_advanced_report"><?php _e('Create Your First Report', 'eddar'); ?></a>
			</div>
		</div>
		<?php
	}
}


/**
 * Renders the report dropdown selector
 *
 * @access      public
 * @since       1.0.0
 */
function edd_advanced_reports_selector($report_list, $report_id) {
	if(!current_user_can('view_shop_reports')) return;
	?>
	<form id="edd-reports-filter" method="get" style="margin:0 0 40px;">
		<select id="edd-reports-view" name="view">
			<?php foreach($report_list->posts as $current_report) : ?>
				<option value="<?php echo $current_report->ID; ?>" <?php selected( $current_report->ID, $report_id ); ?>><?php echo $current_report->post_title; ?></option>
			<?php endforeach; ?>
		</select>
		<input type="hidden" name="post_type" value="download"/>
		<input type="hidden" name="page" value="edd-reports"/>
		<input type="hidden" name="tab" value="advanced_reports"/>
		<?php submit_button(__('Show Report', 'eddar'), 'primary', 'submit', false); ?>
	</form>
	<?php	
}


/**
 * Renders the report date filter
 *
 * @access      public
 * @since       1.0.0
 */
function edd_advanced_reports_dates($report_id){
	if(!current_user_can('view_shop_reports')) return;
	$report_date = get_post_meta($report_id, 'edd_report_date', true);
	$start_date = isset($_GET['report_from']) && $_GET['report_from'] != '' ? $_GET['report_from'] : date('Y-m-d', strtotime(date('Y-m-d')) - $report_date * 86400);
	$end_date = isset($_GET['report_to']) && $_GET['report_to'] != '' ? $_GET['report_to'] : date('Y-m-d');
	?>
	<a class="alignright button" href="<?php echo add_query_arg(array('advanced_report_csv' => $report_id)); ?>">
		<span class="dashicons dashicons-share-alt2" style="line-height:inherit;"></span> <?php _e('Export To CSV', 'eddar'); ?>
	</a>
	<form id="edd-reports-filter" method="get" style="margin:0 0 15px;">
		<input type="text" name="report_from" class="eddar-dateselector" value="<?php echo $start_date; ?>"/>
		<input type="text" name="report_to" class="eddar-dateselector" value="<?php echo $end_date; ?>"/>
		<input type="hidden" name="report" value="<?php if(isset($_GET['report'])) echo esc_attr($_GET['report']); ?>"/>
		<input type="hidden" name="post_type" value="download"/>
		<input type="hidden" name="page" value="edd-reports"/>
		<input type="hidden" name="tab" value="advanced_reports"/>
		<input type="hidden" name="view" value="<?php echo esc_attr($report_id); ?>"/>
		<?php submit_button(__( 'Filter Date', 'eddar' ), 'secondary', 'submit', false); ?>
	</form>
	<?php
}


/**
 * Returns the dataset for the specified report ID
 *
 * @access      public
 * @since       1.0.0
 */
function edd_advanced_reports_generate($report) {
	
	$report_id = $report;
	
	$report_date = get_post_meta($report_id, 'edd_report_date', true);
	$report_series = get_post_meta($report_id, 'edd_report_series', true);
	if(!is_numeric($report_date)) $report_date = 30;
	if(!is_array($report_series)) return;
	
	
	//Calculate date range
	$start_date = isset($_GET['report_from']) && $_GET['report_from'] != '' ? $_GET['report_from'] : date('Y-m-d', strtotime(date('Y-m-d')) - $report_date * 86400);
	$end_date = isset($_GET['report_to']) && $_GET['report_to'] != '' ? $_GET['report_to'] : date('Y-m-d');
	$start_time = strtotime($start_date);
	$end_time = strtotime($end_date);
	if($start_time > $end_time){
		$start_time = $end_time;
		$start_date = $end_date;
	}
	$time_increment = 86400;
	
	//Loop through all series to add them to the graph
	$result_data = array();
	$series_count = 0;
	$downloads_list = edd_advanced_reports_metadata_downloads();
	foreach($report_series as $current_series){
		$result_series = array();
		$series_count++;
		$series_data = array();
		$series_context = $current_series['context'];
		$series_status = $current_series['status'];
		$series_download = $current_series['download'];
		
		
		//Dynamically create the series name (it must be unique)
		if($series_download != 0 && isset($downloads_list[$series_download])){
			$series_name = sprintf(__('%s for %s', 'eddar'), edd_advanced_reports_metadata_context($series_context), $downloads_list[$series_download]);
		}else{
			$series_name = edd_advanced_reports_metadata_context($series_context);
		}
		if($series_status != '' && $series_status != 'all')
			$series_name .= ' ('.edd_advanced_reports_metadata_statuses($series_status).')';
		$series_name = $series_count.'. '.$series_name;		
		
		//Retrieve the proper data according to the type of series
		switch($series_context){
			case 'earnings_gross': 
				$series_data = edd_advanced_reports_get_earnings_gross($series_download, $series_status, $start_date, $end_date); 
				$result_series['type'] = 'currency';
			break;
			case 'earnings_net': 
				$series_data = edd_advanced_reports_get_earnings_net($series_download, $series_status, $start_date, $end_date); 
				$result_series['type'] = 'currency';
			break;
			case 'earnings_tax': 
				$series_data = edd_advanced_reports_get_earnings_tax($series_download, $series_status, $start_date, $end_date); 
				$result_series['type'] = 'currency';
			break;
			case 'sales': 
				$series_data = edd_advanced_reports_get_sales($series_download, $series_status, $start_date, $end_date); 
				$result_series['type'] = 'number';
			break;
		}
		
		$total_value = 0;		
		$current_date = $start_date;
		$current_time = $start_time;
		$count = 0;
		//Loop through each data point for the entire date range
		$plot_data = array();
		while($current_time <= $end_time){
			$result_data['points'][$count] = $current_time;
			$count++;
			$current_date = date('Y-m-d', $current_time);
			
			//Add datapoints in selected date range
			$current_value = 0;
			foreach($series_data as $current_data){
				$data_time = strtotime($current_data['date']);
				if($data_time >= $current_time && $data_time < $current_time + $time_increment){
					$current_value += $current_data['value'];
					$total_value += $current_data['value'];
				}
			}
			$plot_data[] = array($current_time * 1000, $current_value);
			$cell_data[$current_time][$series_name] = $current_value;
			$current_time += $time_increment;
		}
		
		$result_series['dataset'] = $plot_data;
		$result_series['context'] = edd_advanced_reports_metadata_context($series_context);
		$result_series['status'] = edd_advanced_reports_metadata_statuses($series_status);
		$result_series['product'] = isset($downloads_list[$series_download]) ? $downloads_list[$series_download] : $downloads_list[0];
		$result_series['total'] = $total_value;
		
		//Create resulting dataset
		$result_data['series'][$series_name] = $result_series;
	}
	return $result_data;
}


/**
 * Renders a graph for the specified report dataset
 *
 * @access      public
 * @since       1.0.0
 */
function edd_advanced_reports_graph($data) {
	
	//Retrieve the datasets from each series
	$dataset = array();
	foreach($data['series'] as $current_key => $current_series){
		$dataset[$current_key] = $current_series['dataset'];
	}
	
	//Generate the graph
	$graph = new EDD_Graph($dataset);
	$graph->set('x_mode', 'time');
	$graph->set('multiple_y_axes', false);
	if(count($data['points']) > 95) $graph->set('points', false);
	$graph->display();
}


/**
 * Renders a table for the specified report dataset
 *
 * @access      public
 * @since       1.0.0
 */
function edd_advanced_reports_table($data) {
	echo '<div class="eddar-table">';
	echo '<table style="width:100%;text-align:left;border:1px solid #eee; border-spacing:0; border-collapse: collapse;">';
	echo '<thead>';
	echo '<tr style="border-bottom:1px solid #eee;">';
	echo '<th style="padding:10px;">Date</th>';
	
	foreach($data['series'] as $current_series_key => $current_series){
		echo '<th style="padding:10px; ">';
		echo $current_series['context'].' ('.$current_series['status'].')<br>'.$current_series['product'];
		echo '</th>';
	}
	echo '</tr>';
	echo '</thead>';
	foreach($data['points'] as $current_time_key => $current_time){
		echo '<tr>';
		echo '<td style="padding:10px;border:1px solid #eee;">'.date('Y-m-d', $current_time).'</td>';
		foreach($data['series'] as $current_data){
			echo '<td style="padding:10px;border:1px solid #eee;">'.$current_data['dataset'][$current_time_key][1].'</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
	echo '<div style="clear:both;"></div>';
	echo '</div>';
}


/**
 * Returns a CSV file for the specified report
 *
 * @access      public
 * @since       1.0.0
 */
add_action('admin_init', 'edd_advanced_reports_csv');
function edd_advanced_reports_csv($data) {
	if(isset($_GET['advanced_report_csv'])){
		if(!current_user_can('view_shop_reports')) die;
		
		$report_id = intval($_GET['advanced_report_csv']);
		$data = edd_advanced_reports_generate($report_id);
		
		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=edd-advanced-report-".date('YmdHis').".csv");
		header("Content-Transfer-Encoding: binary");
		ob_start();
		$output = fopen('php://output', 'w');
		
		//Print header
		$output_text = array();
		$output_text[] = 'Date';	
		foreach($data['series'] as $current_series_key => $current_series){
			$output_text[] = $current_series_key;
		}
		fputcsv($output, $output_text);
		
		//Print the body
		foreach($data['points'] as $current_time_key => $current_time){
			$output_text = array();
			$output_text[] = date('Y-m-d', $current_time);
			foreach($data['series'] as $current_data){
				$output_value = $current_data['dataset'][$current_time_key][1];
				if($output_value == '') 
					$output_value = 0;	
				$output_text[] = $output_value;
			}
			fputcsv($output, $output_text);
		}
		
		fclose($output);
		echo ob_get_clean();
		die;
	}
}


/**
 * Renders the total line for the specified dataset
 *
 * @access      public
 * @since       1.0.0
 */
function edd_advanced_reports_total($data) {
	echo '<div class="eddar-totals">';
	foreach($data['series'] as $current_total){
		if($current_total['type'] == 'currency') $current_total['total'] = edd_currency_filter($current_total['total']);
		echo '<div style="float:left; margin:20px 30px 20px 0;">';
		echo '<div style="font-size:1.8em; font-weight:bold; margin:0 0 5px;">'.$current_total['total'].'</div>';
		echo '<div style="text-transform:uppercase;">'.$current_total['product'].'</div>';
		echo '<div style="opacity:0.6;">'.$current_total['context'].'</div>';
		echo '</div>';
	}
	echo '<div style="clear:both;"></div>';
	echo '</div>';
}