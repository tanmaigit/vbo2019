<?php
function wfu_remove_file_on_fe($upload_id) {
	global $wpdb;
	
	// Get file
	$filepath = $wpdb->get_var("SELECT filepath FROM ".$wpdb->prefix . "wfu_log"." WHERE uploadid='".$upload_id."'");
	$filepath = ABSPATH.$filepath;
	if(file_exists($filepath))
		@unlink($filepath);
	
	$wpdb->delete($wpdb->prefix . "wfu_userdata", ['uploadid' => $upload_id]);
	$wpdb->delete($wpdb->prefix . "wfu_log", ['uploadid' => $upload_id]);
	
	echo 1;
}
function wfu_download_file_on_fe($upload_id) {
	global $wpdb;
	
	$filepath = $wpdb->get_var("SELECT filepath FROM ".$wpdb->prefix . "wfu_log"." WHERE uploadid='".$upload_id."'");

	$filepath = ABSPATH.$filepath;
	
	@set_time_limit(0); // disable the time limit for this script
	$fsize = filesize($filepath);
	
	if ( $fd = @fopen ($filepath, "rb") ) {
		header('Content-Type: application/octet-stream');
		header("Content-Disposition: attachment; filename=\"".basename($filepath)."\"");
		header('Content-Transfer-Encoding: binary');
		header('Connection: Keep-Alive');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header("Content-length: $fsize");
		$failed = false;
		while( !feof($fd) ) {
			$buffer = @fread($fd, 1024*8);
			echo $buffer;
			ob_flush();
			flush();
			if ( connection_status() != 0 ) {
				$failed = true;
				break;
			}
		}
		fclose ($fd);
	}
}

function wfu_get_log_data_from_uploadid($uploadid, $get_log = true, $get_userdata = true){
	global $wpdb;
	$table_name1 = $wpdb->prefix . "wfu_log";
	$table_name2 = $wpdb->prefix . "wfu_userdata";
	
	$log_data = $user_datas = [];
	if($get_log){
		$log =  $wpdb->get_results('SELECT * FROM '.$table_name1." WHERE uploadid='".$uploadid."'");
		if(!empty($log[0])){
			$log_data = $log[0];
		}
	}
	
	if($get_userdata){
		$userdata =  $wpdb->get_results('SELECT * FROM '.$table_name2." WHERE uploadid='".$uploadid."'");
		foreach($userdata as $_userdata){
			$user_datas[$_userdata->property] = $_userdata->propvalue;
		}
	}
	
	return ['log_data' => $log_data, 'user_datas' => $user_datas];
}

function wfu_datatables_get_data(
	$filters = [], 
	$pagination = [], 
	$orderings = [], 
	$get_header = false, 
	$table_id = ""
	) {
	global $wpdb;
	$table_name1 = $wpdb->prefix . "wfu_log";
	$table_name2 = $wpdb->prefix . "wfu_userdata";
	
	// Get defaut value
	$current_user = wp_get_current_user();
	
	$is_admin_user = in_array("administrator", $current_user->roles) || in_array("author", $current_user->roles);
	if(empty($pagination)){
		$pagination = ['limit' => 10, 'offset' => 0];
	}
	if(!$is_admin_user){
		$filters['log.userid'] = $current_user->ID;
	}
	if(empty($orderings)){
		$orderings[] = ['field'=>'log.date_from', 'type'=>'desc'];
	}
	
	$userdata_fields = ['Paper Title', 'Abstract', 'Keywords', 'Message'];
	
	$conditions = "";
	$has_userdata = false;
	foreach($filters as $key=>$value){
		if(!empty($value)){
			if(in_array($key, $userdata_fields)){
				$has_userdata = true;
				$conditions .= ($conditions == "" ? " WHERE " : " AND ").
					"(userdata.property='".$key."' AND LOWER(userdata.propvalue) LIKE '%".strtolower($value)."%')";
			}else{
				$a_condition = "";
				if($key == 'log.filepath')
					$a_condition = "LOWER(SUBSTRING_INDEX(".$key.", '/', -1)) LIKE '%".strtolower($value)."%'";
				elseif($key == 'log.userid')
					$a_condition = $key."=".$value;
				elseif($key == 'log.date_from'){
					$date_from = explode("$", $value);
					if(count($date_from) == 2){
						$a_condition = $key." >= '".$date_from[0]."' AND ".$key." <= '".$date_from[1]."'";
					}
				}
				else
					$a_condition = "LOWER(".$key.") LIKE '%".strtolower($value)."%'";
				if(!empty($a_condition))
					$conditions .= ($conditions == "" ? " WHERE " : " AND ").$a_condition;
			}
		}
	}
	
	$order_by = "";
	foreach($orderings as $idx=>$ordering){
		$order_by .= ($order_by == "" ? " ORDER BY " : " , ").$ordering['field']." ".$ordering['type'];
	}
	
	$select = "SELECT users.display_name, log.uploadid, log.date_from, log.date_to, log.action, log.filepath, log.filesize";
	$from = " FROM ".$table_name1." log JOIN wp_users users ON log.userid=users.id";
	$group = " GROUP BY log.uploadid, users.id";
	
	$limit = "";
	if(isset($pagination['limit'])){
		$limit = " LIMIT ".$pagination['limit'];
	}
	
	$offset = "";
	if(isset($pagination['offset'])){
		$offset = " OFFSET ".$pagination['offset'];
	}
	
	if($has_userdata){
		$from .= " JOIN ".$table_name2." userdata on userdata.uploadid=log.uploadid";
	}
	
	$files_total = $wpdb->get_var('SELECT COUNT(*) FROM (SELECT COUNT(log.uploadid) '.$from.$conditions.$group.') AS all_count');
	$filerecs = $wpdb->get_results($select.$from.$conditions.$group.$order_by.$limit.$offset);
	
	/*echo '<pre>';
	print_r($select.$from.$conditions.$group.$order_by.$limit.$offset);
	exit;*/

	$userdatarecs = $wpdb->get_results(
		'SELECT uploadid, property, propvalue FROM '.$table_name2." 
		WHERE uploadid IN(SELECT log.uploadid".$from.$conditions.$group.$order_by.")"
	);
	
	$userdatarecs_keys = [];
	foreach ( $userdatarecs as $userdata ) {
		if(isset($userdatarecs_keys[$userdata->uploadid])){
			$userdatarecs_keys[$userdata->uploadid][] = $userdata;
		}else{
			$userdatarecs_keys[$userdata->uploadid] = [$userdata];
		}
	}
	
	$i = isset($pagination['offset']) ? $pagination['offset'] : 0;
	
	$all_data = [];
	$meta_data = [];
	$col_widths = [2, 13, 15, 15, 12, 13, 10, 12, 8];
	$got_meta = false;
	foreach ( $filerecs as $filerec ) {
		$i ++;
		
		$_data = ['seq' => $i];
		if($get_header && !$got_meta)
			$meta_data[] = ['data' => 'seq', 'name' => 'seq', 'sortable' => false, 'width' => $col_widths[count($meta_data)].'%'];
		
		// User
		$_data['user'] = $filerec->display_name;
		if($get_header && !$got_meta)
			$meta_data[] = ['data' => 'user', 'name' => 'log.userid', 'width' => $col_widths[count($meta_data)].'%'];
		
		$_data['date'] = date('m/d/Y H:i:s', strtotime($filerec->date_from));;
		if($get_header && !$got_meta)
			$meta_data[] = ['data' => 'date', 'name' => 'log.date_from', 'width' => $col_widths[count($meta_data)].'%'];
		
		// File
		$_data['filepath'] = basename($filerec->filepath);
		if($get_header && !$got_meta)
			$meta_data[] = ['data' => 'filepath', 'name' => 'log.filepath', 'width' => $col_widths[count($meta_data)].'%'];
		
		// Remarks
		if(!empty($userdatarecs_keys[$filerec->uploadid])){
			foreach ( $userdatarecs_keys[$filerec->uploadid] as $userdata ) {
				if(strlen($userdata->propvalue) > 160)
					$userdata->propvalue = substr($userdata->propvalue, 0, 160) . '...';
				
				$_data[$userdata->property] = $userdata->propvalue;
				if($get_header && !$got_meta)
					$meta_data[] = ['data' => $userdata->property, 'name' => $userdata->property, 'sortable' => false, 'width' => $col_widths[count($meta_data)].'%'];
			}
		}
		
		$_data['uploadid'] = $filerec->uploadid;
		if($get_header && !$got_meta)
			$meta_data[] = ['data' => 'uploadid', 'name' => 'log.uploadid', 'sortable' => false, 'width' => $col_widths[count($meta_data)].'%', 'className' => 'middle-center'];
		
		$got_meta = true;
		
		$all_data[] = $_data;
	}
	
	$header_html = "";
	if($get_header){
		$all_column_titles = ['seq', 'user', 'date', 'filepath', 'Paper Title', 'Abstract', 'Keywords', 'Message', 'uploadid'];
		$header_html = wfu_get_table_header($all_column_titles, $table_id, $is_admin_user);
	}
	
	return ['header_html' => $header_html, 'data' => $all_data, 'meta_data' => $meta_data, 'files_total' => $files_total];
}

function wfu_get_table_header($all_columns, $table_id, $is_admin_user){
	
	$all_users = null;
	if($is_admin_user){
		// Get all users
		global $wpdb;
		$all_users = $wpdb->get_results("SELECT id, display_name as name FROM ".$wpdb->prefix . "users ORDER BY display_name");
	}
	
	// headerTitles
	$headerTitles = [
		'seq' => ['title' => '#', 'align' => 'center', 'filter' => false],
		'uploadid' => ['title' => 'Action', 'align' => 'center', 'filter' => false],
		'user' => ['title' => 'User', 'align' => 'center', 'filter' => 'select', 'data' => $all_users],
		'date' => ['title' => 'Date', 'align' => 'left', 'filter' => 'date_range'],
		'filepath' => ['title' => 'File', 'align' => 'left', 'filter' => 'text'],
	];
	
	$head_th = $filter_th = "";
	$remark_idx = $shown_idx = 0;
	
	foreach($all_columns as $idx=>$column_header){
		if(!isset($headerTitles[$column_header]) || $headerTitles[$column_header] !== false){
			if(isset($headerTitles[$column_header])){
				$a_th = "\n\t\t\t\t\t".'<th scope="col"'. 
				'style="text-align:'.$headerTitles[$column_header]['align'].'"'.
				"\n\t\t\t\t\t\t".'<label>'.$headerTitles[$column_header]['title'].'</label>';
				$a_th .= "\n\t\t\t\t\t".'</th>';
			}else{
				$a_th = "\n\t\t\t\t\t".'<th scope="col" style="text-align:left"';
				$a_th .= "\n\t\t\t\t\t\t".'<label>'.$column_header.'</label>';
				$a_th .= "\n\t\t\t\t\t".'</th>';
				$remark_idx++;
			}
			$head_th .= $a_th;
			$filter_input = "";
			if(isset($headerTitles[$column_header]) && $headerTitles[$column_header]['filter'] !== false){
				// select
				if($headerTitles[$column_header]['filter'] == 'select' && !empty($headerTitles[$column_header]['data'])){
					$filter_input = "<select style='width:100%' class='filter_by_select' data-index='".($shown_idx)."' ><option value=''>--All--</option>";
					foreach($headerTitles[$column_header]['data'] as $entry)
						$filter_input .= "<option value='".$entry->id."'>".$entry->name."</option>";
					$filter_input .= "</select>";	
				}elseif($headerTitles[$column_header]['filter'] == 'text'){
					$filter_input = '<input style="width:100%" class="filter_by_text" data-index="'.($shown_idx).'" >';
				}elseif($headerTitles[$column_header]['filter'] == 'date_range'){
					$filter_input = '<input style="width:100%" class="filter_by_date_range" data-index="'.($shown_idx).'" >';
				}
			}elseif(!isset($headerTitles[$column_header])){
				$filter_input = '<input style="width:100%" class="filter_by_text" data-index="'.($shown_idx).'" >';
			}
				
			$filter_th .= "\n\t\t\t\t\t".'<th style="text-align: left">'.$filter_input.'</th>';
			$shown_idx++;
		}
	}
	$header = '<table id="'.$table_id.'" style="width: 100%; max-width: 100%" class="display dataTable dtr-inline">';
	$header .= "\n\t\t\t".'<thead>';
	
	$header .= "\n\t\t\t\t".'<tr>';
	$header .= $head_th;
	$header .= "\n\t\t\t\t".'</tr>';
	
	$header .= "\n\t\t\t\t".'<tr class="main-filter">';
	$header .= $filter_th;
	$header .= "\n\t\t\t\t".'</tr>';
	
	$header .= "\n\t\t\t".'</thead>';
	$header .= "\n\t\t".'</table>';
	
	return $header;
}

/*
 * Get all sent datatables parameters each AJAX request
 */
function wfu_datatables_get_params()
{
	// Get draw security
	$draw = isset($_POST['draw']) ? $_POST['draw'] : 0;
	
	// Get column info
	$columns = isset($_POST['columns']) ? $_POST['columns'] : [];
	
	// Get global search
	$gSearch = !empty($_POST['search']['value']) ? $_POST['search']['value'] : "";
	
	// Get column search
	$filters = [];
	foreach($columns as $idx=>$column){
		if($column['searchable'] && !empty($column['search']) && !empty($column['name'])){
			if($column['search']['value'] != "")
				$filters[$column['name']] = $column['search']['value'];
		}
	}
	
	// Get pagination
	$limit =  isset($_POST['length']) ? $_POST['length'] : 0;
	$offset =  isset($_POST['start']) ? $_POST['start'] : 0;
	$pagination = ['limit' => $limit, 'offset' => $offset];
	
	// Get ordering
	$orders =  isset($_POST['order']) ? $_POST['order'] : [];
	$ordering = [];
	foreach($orders as $idx=>$order){
		if(isset($columns[$order['column']]) && $columns[$order['column']]['name'] != "")
			$ordering[] = ['field' => $columns[$order['column']]['name'], 'type' => $order['dir']];
	}
	
	return array(
		'draw' 			=> $draw, 
		'columns' 		=> $columns,
		'gSearch' 		=> $gSearch,
		'filters' 		=> $filters,
		'pagination' 	=> $pagination, 
		'ordering' 		=> $ordering
	);
}

function wfu_view_log($page = 1, $only_table_rows = false) {
	global $wpdb;
	$siteurl = site_url();
	$table_name1 = $wpdb->prefix . "wfu_log";
	$table_name2 = $wpdb->prefix . "wfu_userdata";
	$plugin_options = wfu_decode_plugin_options(get_option( "wordpress_file_upload_options" ));

	if ( !current_user_can( 'manage_options' ) ) return;
	//get log data from database
	$files_total = $wpdb->get_var('SELECT COUNT(idlog) FROM '.$table_name1);
	$filerecs = $wpdb->get_results('SELECT * FROM '.$table_name1.' ORDER BY date_from DESC'.( WFU_VAR("WFU_HISTORYLOG_TABLE_MAXROWS") > 0 ? ' LIMIT '.WFU_VAR("WFU_HISTORYLOG_TABLE_MAXROWS").' OFFSET '.(($page - 1) * (int)WFU_VAR("WFU_HISTORYLOG_TABLE_MAXROWS")) : '' ));

	$echo_str = "";
	if ( !$only_table_rows ) {
		$echo_str .= "\n".'<div class="wrap">';
		$echo_str .= "\n\t".'<h2>Wordpress File Upload Control Panel</h2>';
		$echo_str .= "\n\t".'<div style="margin-top:20px;">';
		$echo_str .= wfu_generate_dashboard_menu("\n\t\t", "View Log");
		$echo_str .= "\n\t".'<div style="position:relative;">';
		$echo_str .= wfu_add_loading_overlay("\n\t\t", "historylog");
		$echo_str .= "\n\t\t".'<div class="wfu_historylog_header" style="width: 100%;">';
		if ( WFU_VAR("WFU_HISTORYLOG_TABLE_MAXROWS") > 0 ) {
			$pages = ceil($files_total / WFU_VAR("WFU_HISTORYLOG_TABLE_MAXROWS"));
			$echo_str .= wfu_add_pagination_header("\n\t\t\t", "historylog", 1, $pages);
		}
		$echo_str .= "\n\t\t".'</div>';
		$echo_str .= "\n\t\t".'<table id="wfu_historylog_table" class="wp-list-table widefat fixed striped">';
		$echo_str .= "\n\t\t\t".'<thead>';
		$echo_str .= "\n\t\t\t\t".'<tr>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="5%" style="text-align:center;">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>#</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="15%" style="text-align:left;">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>Date</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="10%" style="text-align:center;">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>Action</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="30%" style="text-align:left;">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>File</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="15%" style="text-align:center;">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>User</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t\t".'<th scope="col" width="25%" style="text-align:left;">';
		$echo_str .= "\n\t\t\t\t\t\t".'<label>Remarks</label>';
		$echo_str .= "\n\t\t\t\t\t".'</th>';
		$echo_str .= "\n\t\t\t\t".'</tr>';
		$echo_str .= "\n\t\t\t".'</thead>';
		$echo_str .= "\n\t\t\t".'<tbody>';
	}

	$userdatarecs = $wpdb->get_results('SELECT * FROM '.$table_name2);
	$deletedfiles = array();
	$filecodes = array();
	$time0 = strtotime("0000-00-00 00:00:00");
	$i = ($page - 1) * (int)WFU_VAR("WFU_HISTORYLOG_TABLE_MAXROWS");
	foreach ( $filerecs as $filerec ) {
		$remarks = '';
		$filepath = ABSPATH;
		if ( substr($filepath, -1) == '/' ) $filepath = substr($filepath, 0, -1);
		$filepath .= $filerec->filepath;
		$enc_file = wfu_plugin_encode_string($filepath.'[[name]]');
		if ( $filerec->action == 'delete' ) array_push($deletedfiles, $filerec->linkedto);
		elseif ( $filerec->action == 'rename' ) {
			$prevfilepath = '';
			$prevfilerec = wfu_get_file_rec_from_id($filerec->linkedto);
			if ( $prevfilerec != null ) $prevfilepath = $prevfilerec->filepath;
			if ( $prevfilepath != '' )
				$remarks = "\n\t\t\t\t\t\t".'<label>Previous filepath: '.$prevfilepath.'</label>';
		}
		elseif ( $filerec->action == 'upload' || $filerec->action == 'modify' ) {
			foreach ( $userdatarecs as $userdata ) {
				if ( $userdata->uploadid == $filerec->uploadid ) {
					$userdata_datefrom = strtotime($userdata->date_from);
					$userdata_dateto = strtotime($userdata->date_to);
					$filerec_datefrom = strtotime($filerec->date_from);
					if ( $filerec_datefrom >= $userdata_datefrom && ( $userdata_dateto == $time0 || $filerec_datefrom < $userdata_dateto ) )
						$remarks .= "\n\t\t\t\t\t\t\t".'<option>'.$userdata->property.': '.$userdata->propvalue.'</option>';
				}
			}
			if ( $remarks != '' ) {
				$remarks = "\n\t\t\t\t\t\t".'<select multiple="multiple" style="width:100%; height:40px; background:none; font-size:small;">'.$remarks;
				$remarks .= "\n\t\t\t\t\t\t".'</select>';
			}
		}
		elseif ( $filerec->action == 'changeuser' ) {
			$prevuploaduserid = '';
			$prevfilerec = wfu_get_file_rec_from_id($filerec->linkedto);
			if ( $prevfilerec != null ) $prevuploaduserid = $prevfilerec->uploaduserid;
			if ( $prevuploaduserid != '' ) {
				$prevuploaduser = wfu_get_username_by_id($prevuploaduserid);
				$remarks = "\n\t\t\t\t\t\t".'<label>Previous upload user: '.$prevuploaduser.'</label>';
			}
		}
		elseif ( $filerec->action == 'other' ) {
			$info = $filerec->filepath;
			$filerec->filepath = '';
			$remarks = "\n\t\t\t\t\t\t".'<textarea style="width:100%; resize:vertical; background:none;" readonly="readonly">'.$info.'</textarea>';
		}
		$i ++;
		$otheraction = ( $filerec->action == 'other' );
		$echo_str .= "\n\t\t\t\t".'<tr>';
		$echo_str .= "\n\t\t\t\t\t".'<td style="padding: 5px 5px 5px 10px; text-align:center;">'.$i.'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td style="padding: 5px 5px 5px 10px; text-align:left;">'.$filerec->date_from.'</td>';
		$echo_str .= "\n\t\t\t\t\t".'<td style="padding: 5px 5px 5px 10px; text-align:center;">'.$filerec->action.'</td>';
		if ( !$otheraction ) {	
			$echo_str .= "\n\t\t\t\t\t".'<td style="padding: 5px 5px 5px 10px; text-align:left;">';
			if ( in_array($filerec->linkedto, $deletedfiles) || in_array($filerec->idlog, $deletedfiles) ) $echo_str .= "\n\t\t\t\t\t\t".'<span>'.$filerec->filepath.'</span>';
			else {
				$lid = 0;
				if ( $filerec->action == 'upload' || $filerec->action == 'include' ) $lid = $filerec->idlog;
				elseif ( $filerec->linkedto > 0 ) $lid = $filerec->linkedto;
				if ( $lid > 0 ) {
					if ( !isset($filecodes[$lid]) ) $filecodes[$lid] = wfu_safe_store_filepath($filerec->filepath);
					$echo_str .= "\n\t\t\t\t\t\t".'<a class="row-title" href="'.$siteurl.'/wp-admin/options-general.php?page=wordpress_file_upload&action=file_details&file='.$filecodes[$lid].'" title="View and edit file details" style="font-weight:normal;">'.$filerec->filepath.'</a>';
				}
				else $echo_str .= "\n\t\t\t\t\t\t".'<span>'.$filerec->filepath.'</span>';
			}
			$echo_str .= "\n\t\t\t\t\t".'</td>';
			$echo_str .= "\n\t\t\t\t\t".'<td style="padding: 5px 5px 5px 10px; text-align:center;">'.wfu_get_username_by_id($filerec->userid).'</td>';
		}
		$echo_str .= "\n\t\t\t\t\t".'<td style="padding: 5px 5px 5px 10px; text-align:left;"'.( $otheraction ? ' colspan="3"' : '' ).'>';
		$echo_str .= $remarks;
		$echo_str .= "\n\t\t\t\t\t".'</td>';
		$echo_str .= "\n\t\t\t\t".'</tr>';
	}
	if ( !$only_table_rows ) {
		$echo_str .= "\n\t\t\t".'</tbody>';
		$echo_str .= "\n\t\t".'</table>';
		$echo_str .= "\n\t".'</div>';
		$echo_str .= "\n".'</div>';
	}

	return $echo_str;
}

?>
