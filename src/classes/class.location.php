<?php
	
	class Location {
		protected $succesMessage;
		
		function __construct($db_conn) {
			$this->db_conn 	= $db_conn;
			$this->locale 	= json_decode(file_get_contents(URL_ROOT.'Src/lang/'.APP_LANG.'.json'), true);
		}	

		public function getLocation($post_val){
			$conn = $this->db_conn;
			$lang = $this->locale;
			
			$account_nr = preg_replace("/[^0-9]/","", $post_val['ID']);
			
			$result = $conn->query("SELECT
					scs_account_address.SCS_Account_Nmbr,
					scs_account_address.SCS_Account_Address_Name,
					scs_account_address.SCS_Account_Address_Address,
					scs_account_address.SCS_Account_Address_Zip,
					scs_account_address.SCS_Account_Address_City,
					scs_account_status.SCS_Account_Stat_Connection_Path,
					scs_account_status.SCS_Account_Stat_Active_DateTime,
					scs_account_status.SCS_Account_Stat_Last_Signal,
					scs_account_info.SCS_Account_CallerID_1,
					scs_account_info.SCS_Account_Receiver_1,
					scs_account_info.SCS_Account_Surveilance_Code,
					scs_account_info.SCS_Account_All_Okay_Word,
					scs_account_info.SCS_Account_serial,
					scs_account_info.SCS_account_SIM_card
				FROM scs_account_address
				INNER JOIN scs_account_status ON scs_account_address.SCS_Account_Nmbr = scs_account_status.SCS_Account_Nmbr
				LEFT JOIN scs_account_info ON scs_account_status.SCS_Account_Nmbr = scs_account_info.SCS_Account_Nmbr
				WHERE scs_account_address.SCS_Account_Address_Type = 2
				AND scs_account_status.SCS_Account_Stat_Active = 1
				AND scs_account_address.SCS_Account_Nmbr LIKE '%".$account_nr."%'");
			
			while ($line = $conn->fetch($result)) {			
				$row = $line;
			};
			
			$path_arr = str_split(substr($row['SCS_Account_Stat_Connection_Path'],0,4));

			$out_of_service = getOutOfService($conn, $account_nr);
			$path_status = getPathStatus($row['SCS_Account_Stat_Connection_Path']);
			
			if($path_status == 0){
				$conn_status 	= '<span class="label label-danger">'.$lang['connection']['diss'].'</span>';
			} elseif($path_status == 2){
				$conn_status 	= '<span class="label label-warning">'.$lang['connection']['back'].'</span>';
			} else {
				$conn_status 	= '<span class="label label-primary">'.$lang['connection']['conn'].'</span>';
			}
			
			$response_array = array(
				'location_name' 	=> $row['SCS_Account_Address_Name'],
				'location_address' 	=> $row['SCS_Account_Address_Address'],
				'location_zip' 		=> $row['SCS_Account_Address_Zip'],
				'location_city' 	=> $row['SCS_Account_Address_City'],
				'location_first' 	=> date('Y-m-d H:i:s', strtotime($row['SCS_Account_Stat_Last_Signal'])),
				'location_last'		=> date('Y-m-d H:i:s', strtotime($row['SCS_Account_Stat_Active_DateTime'])),
				'location_mac'		=> $row['SCS_Account_CallerID_1'],
				'location_udid'		=> $row['SCS_Account_Receiver_1'],
				'location_serie'	=> $row['SCS_Account_serial'],
				'location_sim'		=> $row['SCS_account_SIM_card'],
				'location_lijn'		=> $row['SCS_Account_Surveilance_Code'],
				'location_serviceid'=> $row['SCS_Account_All_Okay_Word'],
				'conn_status' 		=> $conn_status,
				'path_status' 		=> $path_arr		
			);
			
			if($out_of_service['status'] == 1){
				$response_array['oos_id'] 		= $out_of_service['scs_id'];
				//$response_array['oos_begin'] 	= $out_of_service['start'];
				//$response_array['oos_end'] 		= $out_of_service['end'];
				$response_array['oos_icon'] 	= '<span class="label label-warning">'.$lang['connection']['oos'].' '.date('Y-m-d H:i:s', strtotime($out_of_service['end'])) .'</span>';
			};	
	
			// Return JSON array
			jsonArr($response_array);	
		}

		public function getLocationRmsComp($post_val){
			$conn_scs = $this->db_conn;
			$conn = new SafeMySQL();
			$lang = $this->locale;
			
			$account_nr = preg_replace("/[^0-9]/","", $post_val['ID']);
			// GET MAC ADDRESS
			$path_status = $conn_scs->query("SELECT
					scs_account_status.SCS_Account_Stat_Connection_Path,
					scs_account_status.SCS_Account_Nmbr,
					scs_account_info.SCS_Account_CallerID_1					
				FROM scs_account_status
				INNER JOIN scs_account_info ON scs_account_status.SCS_Account_Nmbr = scs_account_info.SCS_Account_Nmbr
				WHERE scs_account_status.SCS_Account_Stat_Active = 1
				AND scs_account_status.SCS_Account_Nmbr LIKE '%".$account_nr."%'");
			
			while ($line = $conn_scs->fetch($path_status)) {			
				$row_path = $line;
			};
			
			$rms_res = $conn->query("SELECT 
				Diag_date,
				Diag_scan_id, 
				D_ACCOUNT_CODE, 
				S_DEVICE_no_1_BATTERY_CHARGE_CURRENT, 
				S_DEVICE_no_1_VOLTAGE 
				FROM rms_status_db WHERE I_MAC_ETH0 = '".$row_path['SCS_Account_CallerID_1']."' ORDER BY Diag_scan_id DESC Limit 20");

			$ups_bat_arr = array();
			$fluid_arr = array();
			$date_arr = array();
			
			if($rms_res){
				while ($row = $conn->fetch($rms_res)) {			
					$ups_bat_arr[]		= $row['S_DEVICE_no_1_BATTERY_CHARGE_CURRENT'];				
					$fluid_arr[]		= $row['S_DEVICE_no_1_VOLTAGE'];				
					$date_arr[]			= date('Y-m-d H:i', strtotime($row['Diag_date']));								
				};
				
				$rev_ups = array_reverse($ups_bat_arr);
				array_unshift($rev_ups, 'Bat charge');
	
				$rev_fluid = array_reverse($fluid_arr);
				array_unshift($rev_fluid, 'Voltage');
	
				$rev_date = array_reverse($date_arr);
				array_unshift($rev_date, 'x');
				
				$response_array = array(
					'status'		=> 1,
					'ups'			=> $rev_ups,
					'fluid'			=> $rev_fluid,
					'date'			=> $rev_date
				);
			} else {
				$response_array = array(
					'status'		=> 0
				);	
			}
			// Return JSON array
			jsonArr($response_array);	
			
		}
		
		public function getLocationRmsPath($post_val){
			$conn_scs = $this->db_conn;
			$conn = new SafeMySQL();
			
			$lang = $this->locale;
			$account_nr = preg_replace("/[^0-9]/","", $post_val['ID']);
			
			
			$path_status = $conn_scs->query("SELECT
					scs_account_status.SCS_Account_Stat_Connection_Path,
					scs_account_status.SCS_Account_Nmbr,
					scs_account_info.SCS_Account_CallerID_1					
				FROM scs_account_status
				INNER JOIN scs_account_info ON scs_account_status.SCS_Account_Nmbr = scs_account_info.SCS_Account_Nmbr
				WHERE scs_account_status.SCS_Account_Stat_Active = 1
				AND scs_account_status.SCS_Account_Nmbr LIKE '%".$account_nr."%'");
			
			while ($line = $conn_scs->fetch($path_status)) {			
				$row_path = $line;
			};
			
			$path_arr = str_split(substr(@$row_path['SCS_Account_Stat_Connection_Path'],0,4));			
			$path_arr_new = array_diff($path_arr, array('?'));
			$path_arr_count = count($path_arr_new)+1;
			
			$path = array();
			$pane = '';
			$ip_query = '';
			//$sparkline = '';
			$j = 0;
			for($i=1; $i<$path_arr_count; $i++){
				$ip_diag = $conn->query("SELECT Diag_date, S_PAC_no_".$i."_STATE FROM rms_status_db WHERE I_MAC_ETH0 LIKE '%".$row_path['SCS_Account_CallerID_1']."%' ORDER BY Diag_scan_id DESC limit 25;");
				$ip_arr = array();
				$ip_arr_date = array();
				$ip_arr_count = array();
				while ($res = $conn->fetch($ip_diag)) {
					if($res['S_PAC_no_'.$i.'_STATE'] == '1'){
						$ip_val = '1';
					} else {
						$ip_val ='2';
					}
					$ip_arr['val'][]		= $ip_val;
					$ip_arr['date'][] 		= $res['Diag_date'];
					//$ip_arr_count[] 	= $res['ip_count'];
					
				};					
				$path[]['path'.$i] = $ip_arr;
				
				
				$pane .= ' <div class="col-xs-3">
                                    <h3 class="m-b-xs">'.$lang['location']['tab']['tab1']['path_name'].' '.$i.'</h3>
                            </div>
							<div class="col-lg-9">
									<div id="ip_line'.$j.'" class="m-b-sm"></div>
							</div>';
				$j++;
				
				$ip_query .= "S_PAC_no_".$i."_STATE,";
				
			}
			
			$ip_total = $conn->query("SELECT Diag_scan_id,
										Diag_date,
										".$ip_query."
										D_ACCOUNT_CODE
										FROM rms_status_db 
										WHERE I_MAC_ETH0 LIKE '%".$row_path['SCS_Account_CallerID_1']."%' ORDER BY Diag_scan_id DESC limit 25;");
			
			$ip_arr_total = array();
			//$ip_arr_total['ip_total'] = array();
			while ($res = $conn->fetch($ip_total)) {
				$k = 0;	

				$primair = array( @$res['S_PAC_no_1_STATE'],  @$res['S_PAC_no_3_STATE']);
				$secundair = array( @$res['S_PAC_no_2_STATE'],  @$res['S_PAC_no_4_STATE']);
				
				if(in_array( '1', $secundair ) && !in_array( '1', $primair ) ){
					// Backup conn
					$k=1;
				} elseif(!in_array( '1', $primair ) && !in_array( '1', $secundair )){
					// Disconnected
					$k=1+1;
				} else {
					// Connected
					$k=1;
				}				
				
				$ip_arr_total['total'][] = $k;
				//array_push($ip_arr_total, $k);
				
			};
			$ip_arr_total['total_d'] = @$ip_arr['date'];
			
			$pane_ip_total = '<div class="col-xs-3">
                                    <h3 class="m-b-xs" >'.$lang['location']['tab']['tab1']['path_conn'].'</h3>
								</div>
								<div class="col-lg-9">
										<div id="ip_line_total" class="m-b-sm"></div>
								</div>';

			$pane_complete = '<div class="row m-t-xs">'.$pane.'</div><div class="row m-t-xs">'.$pane_ip_total.'</div>';	
			
			$response_array = array(
				'ip'		=> $path,
				'ip_total'	=> $ip_arr_total,
				'pane'		=> $pane_complete
			);

						
			// Return JSON array
			jsonArr($response_array);	
				
		}

		public function getLocationRmsVoeding($post_val){
			$conn_scs = $this->db_conn;
			$conn = new SafeMySQL();
			
			$lang = $this->locale;
			$account_nr = preg_replace("/[^0-9]/","", $post_val['ID']);
			
			
			$path_status = $conn_scs->query("SELECT
					scs_account_status.SCS_Account_Stat_Connection_Path,
					scs_account_status.SCS_Account_Nmbr,
					scs_account_info.SCS_Account_CallerID_1					
				FROM scs_account_status
				INNER JOIN scs_account_info ON scs_account_status.SCS_Account_Nmbr = scs_account_info.SCS_Account_Nmbr
				WHERE scs_account_status.SCS_Account_Stat_Active = 1
				AND scs_account_status.SCS_Account_Nmbr LIKE '%".$account_nr."%'");
			
			while ($line = $conn_scs->fetch($path_status)) {			
				$row_path = $line;
			};
			
			
			$stat = $conn->query("SELECT D_ACCOUNT_CODE,
										`Diag_Scan_ID`,
										Diag_date,
										I_MAC_ETH0,
										`S_DEVICE_no_1_STATUS_BATTERY`,
										`S_DEVICE_no_1_STATUS_230V`
										FROM rms_status_db
										WHERE I_MAC_ETH0 LIKE '%".$row_path['SCS_Account_CallerID_1']."%' ORDER BY Diag_scan_id DESC limit 25;");
			
			$st_voeding_arr = array();
			$st_battery_arr = array();
			while ($res = $conn->fetch($stat)) {

				if($res['S_DEVICE_no_1_STATUS_230V'] == 'false' || $res['S_DEVICE_no_1_STATUS_BATTERY'] == 'false'){
					$status_bat = 2;
				} elseif($res['S_DEVICE_no_1_STATUS_230V'] == 'false' || $res['S_DEVICE_no_1_STATUS_BATTERY'] == 'true'){
					$status_bat = 1;
				} else {
					$status_bat = 0;
				}
				
				$st_voeding_arr[]	= $status_bat;							
				$st_battery_arr[]	= $status_bat;							
				$date_arr[]			= $res['Diag_date'];				

			};

				$pane = ' <div class="col-xs-3">
                                  <h3 class="m-b-xs" >'.$lang['location']['tab']['tab1']['power'].'</h3>
                            </div>
							<div class="col-lg-9">
									<div id="status_voeding" class="m-b-sm"></div>
							</div>
							<div class="col-xs-3" >
                                <h3 class="m-b-xs">'.$lang['location']['tab']['tab1']['battery'].'</h3>
							</div>
							<div class="col-lg-9">
									<div id="status_battery" class="m-b-sm"></div>
							</div>';

			$pane_complete = '<div class="row m-t-xs">'.$pane.'</div>';	
			
			$response_array = array(
				'st_voeding'	=> $st_voeding_arr,
				'st_battery'	=> $st_battery_arr,
				'st_date'		=> @$date_arr,
				'pane'			=> $pane_complete
			);

						
			// Return JSON array
			jsonArr($response_array);	
				
		}		
		
		public function getLocationRmsPoll($post_val){
			$conn_scs = $this->db_conn;
			$conn = new SafeMySQL();
			
			$lang = $this->locale;
			$account_nr = preg_replace("/[^0-9]/","", $post_val['ID']);
			
			
			$path_status = $conn_scs->query("SELECT
					scs_account_status.SCS_Account_Stat_Connection_Path,
					scs_account_status.SCS_Account_Nmbr,
					scs_account_info.SCS_Account_CallerID_1					
				FROM scs_account_status
				INNER JOIN scs_account_info ON scs_account_status.SCS_Account_Nmbr = scs_account_info.SCS_Account_Nmbr
				WHERE scs_account_status.SCS_Account_Stat_Active = 1
				AND scs_account_status.SCS_Account_Nmbr LIKE '%".$account_nr."%'");
			
			while ($line = $conn_scs->fetch($path_status)) {			
				$row_path = $line;
			};
			
			$path_arr = str_split(substr(@$row_path['SCS_Account_Stat_Connection_Path'],0,4));			
			$path_arr_new = array_diff($path_arr, array('?'));
			$path_arr_count = count($path_arr_new)+1;
			
			$path_st_all = array();	
			$date_arr = array();

			for($i=1; $i<$path_arr_count; $i++){
				
				$poll_diag = $conn->query("SELECT Diag_date, S_PAC_no_".$i."_POLL_DELAY_COUNT  FROM rms_status_db WHERE I_MAC_ETH0 LIKE '%".$row_path['SCS_Account_CallerID_1']."%' ORDER BY Diag_scan_id DESC limit 25;");

				$path_st_arr = array();
			
				while ($res = $conn->fetch($poll_diag)) {
				
					$path_st_arr[]	= (int)$res['S_PAC_no_'.$i.'_POLL_DELAY_COUNT'];							
					$date_arr[]		= date('Y-m-d H:i', strtotime($res['Diag_date']));						
				};	
				$reverse = array_reverse($path_st_arr);
				array_unshift($reverse, 'Path'.$i);
				array_push($path_st_all, $reverse);
				
				
			}
			$rev_date = (array_reverse($date_arr));
			array_unshift($rev_date, 'x');
			array_unshift($path_st_all, $rev_date);
			
			$response_array = array(
				'poll'			=> $path_st_all
			);						
			// Return JSON array
			jsonArr($response_array);			
		}
		
		public function getLocationSCSValues($post_val){

			$db = new \PDO('mysql:host='.SCS_DB_HOST.';dbname='.SCS_DB_NAME.';charset=utf8', SCS_DB_USER, SCS_DB_PASS, array(\PDO::ATTR_PERSISTENT => true));	
			
			$account_nr = preg_replace("/[^0-9]/","", $post_val);
			
			$columns = array(  
				array (
					'db' => "scs_account_nmbr", 	
					'dt' => "DT_RowClass",
					'formatter' => function($d,$row){
						return "issue-info";
					}		
				),		
				array (
					'db' => "custom_data_index", 			
					'dt' => 0,
					'formatter' => function($d,$row){
						$db_conn = new SafeMySQL(SCS_DB_CONN);
						$data = $db_conn->getAll("SELECT `index`, `name` FROM scs_account_custom_data");
								
						foreach($data as $arr){
							if($arr['index'] == $d){
								return $arr['name'];
							}
						}		
					}					
				),	
				array (
					'db' => "custom_data_value", 			
					'dt' => 1
				)		
			);
	
						
			// Return JSON array
			jsonArr(SSP::complex( $_GET, $db, 'scs_account_custom_data_values', 'scs_account_nmbr', $columns, $whereResult=null, $whereAll="scs_account_nmbr LIKE '%".$account_nr."%'" ));				
		}
		
		public function getLocationSCSComp($post_val){

			$db = new \PDO('mysql:host='.SCS_DB_HOST.';dbname='.SCS_DB_NAME.';charset=utf8', SCS_DB_USER, SCS_DB_PASS, array(\PDO::ATTR_PERSISTENT => true));	
			
			$account_nr = preg_replace("/[^0-9]/","", $post_val);
			
			$columns = array(  
				array (
					'db' => "scs_component_description", 	
					'dt' => "DT_RowClass",
					'formatter' => function($d,$row){
						return "issue-info";
					}		
				),		
				array (
					'db' => "scs_component_description", 			
					'dt' => 0		
				),	
				array (
					'db' => "scs_component_quantity", 			
					'dt' => 1
				),
				array (
					'db' => "scs_component_part_number", 	
					'dt' => 2				
				),
				array (
					'db' => "scs_component_installed_datetime", 	
					'dt' => 3,
					'formatter' => function($d,$row){
						return date('Y-m-d', strtotime($d));
					}					
				),
				array (
					'db' => "scs_component_installed_datetime", 	
					'dt' => 4,
					'formatter' => function($d,$row){
						$dateString = $d;
						$dt = new DateTime($dateString);
						$dt->modify('+3 years');
						$expire_date = $dt->format('Y-m-d');
						
						$date1 = new DateTime(date('Y-m-d'));
						$date2 = new DateTime($expire_date);
						
						$diff = $date1->diff($date2);					
						
						return '<span class="pie">'.$diff->days.',1095</span> '.$expire_date;
					}					
				)		
			);
	
						
			// Return JSON array
			jsonArr(SSP::complex( $_GET, $db, 'scs_account_components', 'scs_account_nmbr', $columns, $whereResult=null, $whereAll="scs_account_nmbr LIKE '%".$account_nr."%'" ));	
		}
	
		public function getLocationSCSEvents($post_val){
			$conn = $this->db_conn;
			$lang = $this->locale;
			
			$account_nr = preg_replace("/[^0-9]/","", $post_val['ID']);
			
			// Get week number based on current date
			$ddate = date('Y-m-d');
			$date = new DateTime($ddate);
			$week_nr = $date->format("W");			
			
			$result = $conn->query("SELECT 	COUNT(event_zone) AS count_events,
									event_code,
									event_zone,
									Event_Text
										
									FROM 
									events".date('Y')."_".$week_nr.".event_received 
									WHERE scs_account_nmbr LIKE '%".$account_nr."%'
									AND Event_Text NOT LIKE 'Herstel:%'
									GROUP BY event_zone");
									
			$response_array	= array();
			
			$response_array['events'] = array();
			while ($row = $conn->fetch($result)) {			
  
				array_push($response_array['events'],  
					$test =	array(
						'value' => $row['count_events'], 
						'name' => $row['Event_Text']
					)
				);
				
			};
			
			
			$response_array['count'] = $conn->getOne("SELECT COUNT(event_zone) FROM events".date('Y')."_".$week_nr.".event_received WHERE scs_account_nmbr LIKE '%".$account_nr."%' ");
			
			jsonArr($response_array);
		}
		
	}