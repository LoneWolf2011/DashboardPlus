<?php
	
	class Home {
		protected $succesMessage;
		
		function __construct($db_conn) {
			$this->db_conn 	= $db_conn;
			$this->google	= new googleHelper(GOOGLE_API);
			$this->locale 	= json_decode(file_get_contents(URL_ROOT.'/Src/lang/'.APP_LANG.'.json'), true);
		}	

		public function getMarkers($getall = false, $updatetime=''){
			$conn = $this->db_conn;
			$lang = $this->locale;
			
			$datetime = date('YmdHis', strtotime('now'));

			// Initial query
			$sql= "SELECT
					scs_account_address.SCS_Account_Nmbr,
					scs_account_address.SCS_Account_Address_Name,
					scs_account_address.SCS_Account_Address_Address,
					scs_account_address.SCS_Account_Address_Zip,
					scs_account_address.SCS_Account_Address_City,
					scs_account_status.SCS_Account_Stat_Connection_Path,
					scs_account_status.SCS_Account_Stat_Last_Signal,
					scs_account_status.SCS_Account_Stat_Active,
					scs_account_info.SCS_Account_CallerID_1,
					scs_account_info.Latitude,
					scs_account_info.Longitude
				FROM scs_account_address
				INNER JOIN scs_account_status ON scs_account_address.SCS_Account_Nmbr = scs_account_status.SCS_Account_Nmbr
				LEFT JOIN scs_account_info ON scs_account_status.SCS_Account_Nmbr = scs_account_info.SCS_Account_Nmbr
				WHERE scs_account_address.SCS_Account_Address_Type = 2
				AND scs_account_status.SCS_Account_Stat_Active = 1
				AND scs_account_info.Latitude != '-1'
				AND scs_account_info.Longitude != '-1'";
				
			// Select all the rows in the app_location_data table
			if($getall){
				$com_query = $sql;
			} else{ 
				$com_query = $sql.' AND scs_account_status.SCS_Account_Stat_Last_Signal > '.$updatetime;
			}
			
			
			$result = $conn->query($com_query);
			if (!$result) {
				die('Invalid query: ' . $conn->connect_error);
			}
			
			//checkCoordinates($conn,$sql);
			
			$conn_local 	= new SafeMySQL();
			if($result){
				while ($row = $conn->fetch($result)) {
	
					$query = "SELECT
							Diag_Scan_ID,
							Diag_date,
							I_MAC_ETH0,
							S_DEVICE_no_1_STATE,
							S_DEVICE_no_1_STATUS_BATTERY,
							S_DEVICE_no_1_STATUS_230V
							FROM rms_status_db WHERE Diag_Scan_ID = (SELECT MAX(Diag_Scan_ID) FROM rms_status_db WHERE I_MAC_ETH0 = '".$row['SCS_Account_CallerID_1']."')";
							
					$rms_status = $conn_local->getRow($query);
			
					$path_status = getPathStatus($row['SCS_Account_Stat_Connection_Path']);
					
					if($path_status == 3){
						$conn_status 	= $lang['connection']['nopath'];
						$err_class 		= 'text-default';				
					} elseif($path_status == 0){
						$conn_status 	= $lang['connection']['diss'];
						$err_class 		= 'text-danger';
					} elseif($path_status == 2){
						$conn_status 	= $lang['connection']['back'];
						$err_class 		= 'text-warning';
					} else {
						$conn_status 	= $lang['connection']['conn'];
						$err_class 		= 'text-primary';
					}
									
					$device_status = '';
					if($rms_status['S_DEVICE_no_1_STATUS_BATTERY'] == 'false' || $rms_status['S_DEVICE_no_1_STATUS_230V'] == 'false'){
						if($rms_status['S_DEVICE_no_1_STATUS_BATTERY'] == 'false'){
							$device_status .= '<br><b>'.$lang['connection']['batt_err'].'</b>';
						}
						if($rms_status['S_DEVICE_no_1_STATUS_230V'] == 'false' ){
							$device_status .= '<br><b>'.$lang['connection']['230_err'].'</b>';	
						}
						$path_status = 2;		
					} 
					
					$is_letter = (ctype_alpha(substr(getCategory($row['SCS_Account_Nmbr']),0,1)) == true) ? strtoupper(substr(getCategory($row['SCS_Account_Nmbr']),0,1)): strtoupper('A');				
					$locs[$row['SCS_Account_Nmbr']] = array( 
						'info' 			=> '<div><b>'.$row['SCS_Account_Address_Name'].'</b><br>'.$row['SCS_Account_Address_Address'].'<br><a class="text-info '.$err_class.'" onclick="popupWindow(\''.URL_ROOT.'view/location/?'.$row['SCS_Account_Nmbr'].'\', \'location\', 1980, 1080 ); return false;">#'.$row['SCS_Account_Nmbr'].'</a><br><b>'.$conn_status.'</b>'.$device_status.'</div>', 
						'path_status' 	=> $path_status, 
						'first_char' 	=> $is_letter, 
						'lat' 			=> $row['Latitude'], 
						'lng' 			=> $row['Longitude'],
						'category' 		=> getCategory($row['SCS_Account_Nmbr']),
						'id' 			=> $row['SCS_Account_Nmbr']
					);
					
				}
				$locs['updatetime'] = date('YmdHis');
			} 
			
			
			jsonArr($locs);			
		}
		
		public function getList($state){
			$lang = $this->locale;
			$db = new \PDO('mysql:host='.SCS_DB_HOST.';dbname='.SCS_DB_NAME.';charset=utf8', SCS_DB_USER, SCS_DB_PASS, array(\PDO::ATTR_PERSISTENT => true));
			
			$clean_state = strtolower(preg_replace("/[^A-Za-z]/","", $state));
			
			define('CLEAN_STATE', $clean_state);
			
			if($clean_state == 'active'){
				$where = "SCS_Account_Stat_Active = 1 AND SCS_Account_Stat_Connection_Path LIKE '%1%'";
			} elseif($clean_state == 'inactive'){
				$where = "SCS_Account_Stat_Active = 1 AND SCS_Account_Stat_Connection_Path IN ('????????')";
			} elseif($clean_state == 'problem'){
				$where = "SCS_Account_Stat_Active = 1 AND SCS_Account_Stat_Connection_Path LIKE '%0%'";				
			}
			
			$columns = array(  
				array (
					'db' => "SCS_Account_Nmbr", 	
					'dt' => "DT_RowClass",
					'formatter' => function($d,$row){
						//return "issue-info";
					}		
				),		
				array (
					'db' => "SCS_Account_Stat_Connection_Path", 			
					'dt' => 0,
					'formatter' => function($d,$row){
						
						$lang = $this->locale;
						
						$path_status = getPathStatus($d);
						if($path_status == 3){
							$conn_status 	= '<i class="fa fa-circle text-default"></i> '.$lang['connection']['nopath'];
						} elseif($path_status == 0){
							$conn_status 	= '<i class="fa fa-circle text-danger"></i> '.$lang['connection']['diss'];
						} elseif($path_status == 2){
							$conn_status 	= '<i class="fa fa-circle text-warning"></i> '.$lang['connection']['back'];
						} else {
							$conn_status 	= '<i class="fa fa-circle text-navy"></i> '.$lang['connection']['conn'];
						}
						
						return $conn_status;
					}					
				),	
				array (
					'db' => "SCS_Account_Nmbr", 			
					'dt' => 1,
					'formatter' => function($d,$row){
						$problem_link = (CLEAN_STATE == 'problem' || CLEAN_STATE == 'inactive' )?'&err':'';
						$path_status = getPathStatus($row[1]);
						
						if($path_status == 3){
							$conn_class 	= 'text-default';
						} elseif($path_status == 0){
							$conn_class 	= 'text-danger';
						} elseif($path_status == 2){
							$conn_class 	= 'text-warning';
						} else {
							$conn_class 	= 'text-navy';
						}
						
						//return '<a class="text-info '.$conn_class.'" data-markerid="' .$d. '" href="/mdb/view/location/?'.$d.$problem_link.'">'.$d.'</a>';
						return '<a class="text-info '.$conn_class.'" data-markerid="' .$d. '" onclick="popupWindow(\''.URL_ROOT.'view/location/?'.$d.$problem_link.'\', \'location\', 1980, 1080 ); return false;">'.$d.'</a>';
					}
				),
				array (
					'db' => "SCS_Account_Name", 	
					'dt' => 2,
					'formatter' => function($d,$row){
						$conn = new SafeMySQL(SCS_DB_CONN);
						
						$latlong = $conn->getRow("SELECT
												scs_account_info.SCS_Account_Nmbr,
												scs_account_info.Latitude,
												scs_account_info.Longitude
												FROM scs_account_info
												WHERE scs_account_info.SCS_Account_Nmbr = '".$row[2]."'");	
						
						if($latlong['Latitude'] != '-1' && $latlong['Longitude'] != '-1'){
							return '<a onclick="selectMarker('.$row[2].');"><i class="fa fa-map-marker"></i></a> '.$d;							
						} else {
							return $d;	
						}			

					}
				),
				array (
					'db' => "SCS_Account_Stat_Last_Signal", 	
					'dt' => 3,
					'formatter' => function($d,$row){
						if(!empty($d)){
							$last ='<small class="block text-muted"><i class="fa fa-clock-o"></i> '. date('Y-m-d H:i:s', strtotime($d)).'</small>';					
						} else {
							$last = '';
						}			
						return $last;
					}	
				),
				array (
					'db' => "SCS_Account_Stat_Connection_Path", 	
					'dt' => 4,
					'formatter' => function($d,$row){
						
						$path_status = getPathStatus($d);

						return (int)$path_status;
					}					
				)				
			);

			
						
			// Return JSON array
			jsonArr(SSP::complex( $_GET, $db, 'scs_account_status', 'scs_account_nmbr', $columns, $whereResult=null, $whereAll=$where ));			
			
		}

		public function getListRms(){
			$db = new \PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS, array(\PDO::ATTR_PERSISTENT => true));
			
			$where = "S_DEVICE_no_1_STATUS_230V = 'false' OR  S_DEVICE_no_1_STATUS_BATTERY = 'false' GROUP BY D_account_code";
			
			$columns = array(  
				array (
					'db' => "Diag_scan_id", 	
					'dt' => "DT_RowClass",
					'formatter' => function($d,$row){
						//return "issue-info";
					}		
				),		
				array (
					'db' => "S_DEVICE_no_1_STATE", 			
					'dt' => 0,
					'formatter' => function($d,$row){
						
						$lang = $this->locale;
						
						if($d == '0' && ($row[6] == 'false' || $row[6] == '-')){
							$conn_status 	= '<i class="fa fa-circle text-danger"></i> '.$lang['connection']['multi_err'];
						} elseif(($row[5] == 'false' || $row[5] == '-') && ($row[6] == 'false' || $row[6] == '-')){
							$conn_status 	= '<i class="fa fa-circle text-danger"></i> '.$lang['connection']['multi_err'];
						} elseif($d == '0' && ($row[5] == 'false' || $row[5] == '-')){
							$conn_status 	= '<i class="fa fa-circle text-danger"></i> '.$lang['connection']['multi_err'];
						} elseif($row[6] == 'false' || $row[6] == '-'){
							$conn_status 	= '<i class="fa fa-circle text-warning"></i> '.$lang['connection']['230_err'];
						} elseif($row[5] == 'false' || $row[5] == '-'){
							$conn_status 	= '<i class="fa fa-circle text-warning"></i> '.$lang['connection']['batt_err'];
						} elseif($d == '0'){
							$conn_status 	= '<i class="fa fa-circle text-warning"></i> '.$lang['connection']['main_err'];							
						} else{
							$conn_status 	= '<i class="fa fa-circle text-danger"></i> '.$lang['connection']['multi_err'];
						}
											
						return $conn_status;
					}					
				),	
				array (
					'db' => "I_MAC_ETH0", 			
					'dt' => 1,
					'formatter' => function($d,$row){
						$conn = new SafeMySQL(SCS_DB_CONN);
						$account_code = $conn->getOne("SELECT SCS_Account_Nmbr FROM scs_account_info
													WHERE scs_account_info.SCS_Account_CallerID_1 LIKE '%".$d."%'");	
						//return $location_name
					
						return '<a class="text-info text-warning" data-markerid="' .$account_code. '" onclick="popupWindow(\''.URL_ROOT.'view/location/?'.$account_code.'&err\', \'location\', 1980, 1080, ); return false;">'.$account_code.'</a>';
					}
				),
				array (
					'db' => "I_MAC_ETH0", 	
					'dt' => 2,
					'formatter' => function($d,$row){
						$conn = new SafeMySQL(SCS_DB_CONN);
						
						$location_name = $conn->getOne("SELECT
														scs_account_address.SCS_Account_Address_Name,
														scs_account_address.SCS_Account_Address_Address
													FROM scs_account_address
													INNER JOIN scs_account_info ON scs_account_address.SCS_Account_Nmbr = scs_account_info.SCS_Account_Nmbr
													WHERE scs_account_address.SCS_Account_Address_Type = 2
													AND scs_account_info.SCS_Account_CallerID_1 LIKE '%".$d."%'");	
						return $location_name;
					}
				),
				array (
					'db' => "Diag_date", 	
					'dt' => 3,
					'formatter' => function($d,$row){
						if(!empty($d)){
							$last ='<small class="block text-muted"><i class="fa fa-clock-o"></i> '. date('Y-m-d H:i:s', strtotime($d)).'</small>';					
						} else {
							$last = '';
						}			
						return $last;
					}	
				),
				array (
					'db' => "S_DEVICE_no_1_STATUS_BATTERY", 	
					'dt' => 4					
				),
				array (
					'db' => "S_DEVICE_no_1_STATUS_230V", 	
					'dt' => 5				
				)					
			);
			// Return JSON array
			jsonArr(SSP::complex( $_GET, $db, 'rms_status_db', 'Diag_scan_id', $columns, $whereResult=null, $whereAll=$where ));				
			
		}
		
		public function getEventCount(){
			$year 	= date('Y');
			$week 	= date('W');
			$month 	= date('m');
			
			$db_name_this_week 	= 'events'.$year.'_'.$week;
			
			$scs_conn 	= $this->db_conn;
			$local_conn = new SafeMySQL();
			$conn 	= new SafeMySQL(array('host'=> SCS_DB_HOST, 'user'=> SCS_DB_USER, 'pass'=> SCS_DB_PASS, 'db'=> $db_name_this_week));
			
			function percent($nieuweWaarde, $oudeWaarde) {
				if($oudeWaarde != 0){
					$percentage = round((($nieuweWaarde - $oudeWaarde) / $oudeWaarde) * 100, 0);
						if($percentage > 0){
							return '<span class="text-danger">'.$percentage.'% <i class="fa fa-level-up"></i></span>';				
						} elseif($percentage == 0){  
							return '<span>'.$percentage.'% </span> Sinds laatste week';
						} else {
							return '<span class="text-navy">'.$percentage.'% <i class="fa fa-level-down"></i></span>';
						}
				} else {
					return '<span> 0% </span>';
				}
			}			
			
			$count_this_day 	= $conn->getOne("SELECT COUNT(*) FROM `event_received` WHERE DATE(`DateTime`) > DATE_SUB(CURDATE(), INTERVAL 1 DAY);");
			$count_past_day 	= $conn->getOne("SELECT COUNT(*) FROM `event_received` WHERE DATE(`DateTime`) > DATE_SUB(CURDATE(), INTERVAL 2 DAY);");
			
			$last_week 			= date("W", strtotime("-1 week"));
			$db_name_past_week 	= 'events'.$year.'_'.$last_week;
			
			$conn_past_week 	= new SafeMySQL(array('host'=> SCS_DB_HOST, 'user'=> SCS_DB_USER, 'pass'=> SCS_DB_PASS, 'db'=> $db_name_past_week));
			$count_this_week 	= $conn->getOne("SELECT COUNT(*) FROM `event_received`;");
			$count_past_week 	= $conn_past_week->getOne("SELECT COUNT(*) FROM `event_received`;");
			
			$response_array = array(
				'day' => [
					'count' 	=> $count_this_day,
					'past' 		=> $count_past_day - $count_this_day,
					'percent' 	=> percent($count_this_day, $count_past_day - $count_this_day)
				],				
				'week' => [
					'count' 	=> $count_this_week,
					'past' 		=> $count_past_week - $count_this_week,
					'percent' 	=> percent($count_this_week, $count_past_week - $count_this_week)
				]	
				//'scs_active_count' => $scs_conn->getOne("SELECT COUNT(*) FROM scs_account_status WHERE SCS_Account_Stat_Active = 1 AND SCS_Account_Stat_Connection_Path LIKE '%1%'"),
				//'scs_problem_count' => $scs_conn->getOne("SELECT COUNT(*) FROM scs_account_status WHERE SCS_Account_Stat_Active = 1 AND SCS_Account_Stat_Connection_Path LIKE '%0%'"),
				//'scs_inactive_count' => $scs_conn->getOne("SELECT COUNT(*) FROM scs_account_status WHERE SCS_Account_Stat_Active = 1 AND SCS_Account_Stat_Connection_Path IN ('????????','????')"),
				//'rms_storing_count' => $local_conn->getOne("SELECT COUNT(*) FROM rms_status_db  WHERE Diag_Scan_ID IN (SELECT MAX(Diag_Scan_ID) AS id FROM rms_status_db  GROUP BY `I_MAC_ETH0`) AND (S_DEVICE_no_1_STATUS_230V != 'true' OR S_DEVICE_no_1_STATUS_BATTERY != 'true')"),
			);
			
			jsonArr($response_array);
		}
		
	}