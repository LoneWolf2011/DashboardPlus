<?php
	
	class Tools {
		protected $succesMessage;
		
		function __construct($db_conn) {
			$this->db_conn 	= $db_conn;
			$this->locale 	= json_decode(file_get_contents(URL_ROOT.'Src/lang/'.APP_LANG.'.json'), true);
		}
		
		public function getThreshold($post_val){
			$conn_scs = $this->db_conn;

			$priority = preg_replace("/[^0-9]/","", $post_val['prio']);
			
			$rms_res = $conn_scs->query("SELECT AVG(`Event_First_Action`) AS average, 
									COUNT(IF(`Event_First_Action` <= '60' ,1,NULL)) AS green, 
									COUNT(IF((`Event_First_Action` <= '120' AND `Event_First_Action` > '60') ,1,NULL)) AS orange, 
									COUNT(IF(`Event_First_Action` > '120' ,1,NULL)) AS red FROM 
									((SELECT `Event_First_Action` 
									FROM `events2018_10`.`event_received` 
									WHERE `Event_Priority` =  ".$priority." 
									AND `Event_First_Action` != -1 
									ORDER BY DATETIME DESC LIMIT 100) 
									UNION (SELECT `Event_First_Action` 
									FROM `events2018_09`.`event_received` 
									WHERE `Event_Priority` =  ".$priority." 
									AND `Event_First_Action` != -1 
									ORDER BY DATETIME DESC LIMIT 100) 
									LIMIT 100) AS First_Action");
				
				$green = array('green');
				$orange = array('orange');
				$red = array('red');
				$avg = array();
				while ($row = $conn_scs->fetch($rms_res)) {			
					$green[]		= $row['green'];				
					$orange[]		= $row['orange'];				
					$red[]			= $row['red'];											
					$avg			= $row['average'];											
				};			
				
				$response_array = array(
					'status'		=> 1,
					'green'			=> $green,
					'orange'		=> $orange,
					'red'			=> $red,
					'avg'			=> round($avg) . ' sec',
				);
				
				if($priority == 1){
					$response_array['prio'] = 1;
				} else {
					$response_array['prio'] = 2;
				}
				
			// Return JSON array
			jsonArr($response_array);				
				
		}

		public function getResponseTime(){
			$conn_scs = $this->db_conn;

			$rms_res = $conn_scs->query("SELECT DATE_FORMAT 
				(NOW() - INTERVAL 1 DAY,'%Y%m%d%H%i%s'), 
				DATE_FORMAT (NOW() - INTERVAL 1 DAY,'%H') AS starthour, 
				
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 24 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 23 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour24, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 23 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 22 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour23, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 22 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 21 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour22, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 21 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 20 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour21, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 20 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 19 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour20, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 19 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 18 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour19, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 18 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 17 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour18, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 17 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 16 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour17, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 16 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 15 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour16, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 15 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 14 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour15, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 14 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 13 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour14, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 13 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 12 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour13, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 12 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 11 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour12, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 11 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 10 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour11, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 10 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 9 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour10, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 9 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 8 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour09, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 8 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 7 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour08, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 7 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 6 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour07, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 6 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 5 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour06, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 5 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 4 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour05, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 4 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 3 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour04, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 3 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 2 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour03, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 2 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 1 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour02, 
				IFNULL(AVG(IF((T.DateTime > DATE_FORMAT(NOW() - INTERVAL 1 HOUR,'%Y%m%d%H%i%s'))&&(T.DateTime < DATE_FORMAT(NOW() - INTERVAL 0 HOUR,'%Y%m%d%H%i%s')),`Event_First_Action`,NULL)),0) AS hour01 
				FROM 
				((SELECT `DateTime`, 
				`Event_Priority`, 
				`Event_First_Action`
				FROM events2018_10.event_received 
				WHERE `Event_Priority` IN ('1','2') 
				AND `DateTime` >= NOW() - INTERVAL 1 DAY 
				ORDER BY `DateTime` DESC) UNION 
				(SELECT `DateTime`, 
				`Event_Priority`, 
				`Event_First_Action` 
				FROM events2018_09.event_received 
				WHERE `Event_Priority` IN ('1','2') 
				AND `DateTime` >= NOW() - INTERVAL 1 DAY ORDER BY `DateTime` DESC)) 
				AS T ORDER BY MID(`DateTime`, 1, 10)");
				
				$avg 	= array('avg');
				$hours 	= array('hours');
				
				while ($row = $conn_scs->fetch($rms_res)) {			
					$avg	= array(
						'avg',
						$row['hour24'],
						$row['hour23'],
						$row['hour22'],
						$row['hour21'],
						$row['hour20'],
						$row['hour19'],
						$row['hour18'],
						$row['hour17'],
						$row['hour16'],
						$row['hour15'],
						$row['hour14'],
						$row['hour13'],
						$row['hour12'],
						$row['hour11'],
						$row['hour10'],
						$row['hour09'],
						$row['hour08'],
						$row['hour07'],
						$row['hour06'],
						$row['hour05'],
						$row['hour04'],
						$row['hour03'],
						$row['hour02'],
						$row['hour01']

					);					
					$hours	= array(
						'x',
						'24:00',
						'23:00',
						'22:00',
						'21:00',
						'20:00',
						'19:00',
						'18:00',
						'17:00',
						'16:00',
						'15:00',
						'14:00',
						'13:00',
						'12:00',
						'11:00',
						'10:00',
						'09:00',
						'08:00',
						'07:00',
						'06:00',
						'05:00',
						'04:00',
						'03:00',
						'02:00',
						'01:00'
					);										
				};			
				
				$response_array = array(
					'status'	=> 1,
					'avg'		=> $avg,
					'hours'		=> $hours
				);
				
			// Return JSON array
			jsonArr($response_array);				
				
		}

		public function getSignalLoad(){
			$conn_scs = $this->db_conn;
			
			$year = date('Y');
			
			// Get weeknumber from today
			$now 			= date('YmdHis', strtotime('tomorrow'));
			$now_week_no 	= date('W');
			$table_now 		= 'signals_'.$year.'_'.$now_week_no;
			
			// Get weeknumber from past 24 hours
			$past 			= date('YmdHis', strtotime('-24 hours'));
			$past_week_no 	= date('W', strtotime('past week'));
			$table_past 	= 'signals_'.$year.'_'.$past_week_no;
			
			// Check if both dates are in the same week and specify query
			if($past_week_no == $now_week_no){
				$query = "SELECT PreProcessor_Signal_DateTime AS signalDate, COUNT(*) AS signal
										FROM scs_fep.".$table_now."
										WHERE PreProcessor_Signal_DateTime 
										BETWEEN '".$past."' AND '".$now."' 
										GROUP BY MID(PreProcessor_Signal_DateTime, 5, 6) 
										ORDER BY PreProcessor_Signal_DateTime";
			} else {
				$query = "(SELECT PreProcessor_Signal_DateTime AS signalDate, COUNT(*) AS signal
										FROM scs_fep.".$table_past."
										WHERE PreProcessor_Signal_DateTime 
										BETWEEN '".$past."' AND '".$now."' 
										GROUP BY MID(PreProcessor_Signal_DateTime, 5, 6) 
										ORDER BY PreProcessor_Signal_DateTime) 
										UNION 
										(SELECT PreProcessor_Signal_DateTime AS signalDate, COUNT(*) AS signal
										FROM scs_fep.".$table_now." 
										WHERE PreProcessor_Signal_DateTime 
										BETWEEN '".$past."' AND '".$now."' 
										GROUP BY MID(PreProcessor_Signal_DateTime, 5, 6) 
										ORDER BY PreProcessor_Signal_DateTime)";
			}
			
			$res = $conn_scs->query($query);
				
				$signal = array('signal');
				$hours 	= array('x');
				
				while ($row = $conn_scs->fetch($res)) {			
					$signal[]	= $row['signal'];				
					$hours[]	= date('H:i', strtotime($row['signalDate']));													
				};			
				
				$response_array = array(
					'status'	=> 1,
					'signal'	=> $signal,
					'hours'		=> $hours,
					'avg_last'	=> $this->getSignalLoadWeeklyAvg($past_week_no),
					'avg_now'	=> $this->getSignalLoadWeeklyAvg($now_week_no)
				);
				
			// Return JSON array
			jsonArr($response_array);				
				
		}
		
		public function getPendingEvents($event_type = ''){
			$conn_scs = $this->db_conn;
			
			$year = date('Y');
			
			// Get weeknumber from today
			$now 			= date('YmdHis', strtotime('tomorrow'));
			$now_week_no 	= date('W');
			$table_now 		= 'signals_'.$year.'_'.$now_week_no;

			
			
			if($event_type == 'TASK'){
				$where = "WHERE Event_Type IN ('TASK')";
			} else {
				$where = "WHERE Event_Type NOT IN ('TRTS')";
			}
		
			$query = "SELECT
					`Account_Nmbr`,
					`DateTime`,
					`Account_Name`,
					`Account_Group`,
					`Alarm_Priority`,
					`Alarm_Priority_Original`,
					`Alarm_Description`,
					`Event_Code`,
					`Event_Zone`,
					`Event_Description`,
					`Event_Operator`,
					`Event_Operator_First`,
					`Event_Reaction_Time`
					FROM `scs`.`scs_pending_events`
					".$where."
					ORDER BY `Alarm_Priority` ASC, `DateTime` ASC";
			
			$res = $conn_scs->query($query);
			
			$count = $conn_scs->getAll("SELECT
					COUNT(Event_Zone) as aantal,
					Event_Zone
					FROM `scs`.`scs_pending_events`
					GROUP BY Event_Zone");
			$count_arr = array();
			foreach($count as $event){
				$count_arr[$event['Event_Zone']] = $event['aantal'];
			}
						
			$rows = '<thead>
						<th>#</th>
						<th>Datum tijd</th>
						<th>Account nr</th>
						<th>Account naam</th>
						<th>Acount groep</th>
						<th>Zone</th>
						<th>Beschrijving</th>
						<th>In gebruik</th>
						<th>Eerste</th>
						<th>Reactie tijd</th>
						<th class="hidden"></th>
					</thead>';
					
			while ($row = $conn_scs->fetch($res)) {	
				if($row['Alarm_Priority'] == '1'){
					$class = 'bg-danger';
					if($row['Event_Operator'] == null){
						$audio_source = URL_ROOT. 'src/libs/scs_sounds/P1.wav';	
					} else {
						$audio_source = '';
					}
				} elseif($row['Alarm_Priority'] == '2'){
					$class = 'bg-danger';
					if($row['Event_Operator'] == null){
						$audio_source = URL_ROOT. 'src/libs/scs_sounds/P2.wav';	
					} else {
						$audio_source = '';
					}
				} elseif($row['Alarm_Priority'] == '3'){
					$class = 'bg-danger';
					if($row['Event_Operator'] == null){
						$audio_source = URL_ROOT. 'src/libs/scs_sounds/P3.wav';	
					} else {
						$audio_source = '';
					}
				} elseif($row['Alarm_Priority'] == '4'){
					$class = 'bg-warning';
					if($row['Event_Operator'] == null){
						$audio_source = URL_ROOT. 'src/libs/scs_sounds/P4.wav';	
					} else {
						$audio_source = '';
					}
				} elseif($row['Alarm_Priority'] == '5'){
					$class = 'bg-yellow';
					if($row['Event_Operator'] == null){
						$audio_source = URL_ROOT. 'src/libs/scs_sounds/P5.wav';	
					} else {
						$audio_source = '';
					}				
				} elseif($row['Alarm_Priority'] == '7'){
					$class = 'bg-primary';
					$audio_source = '';
				} elseif($row['Alarm_Priority'] == '8'){
					$class = 'bg-dark-green';
					$audio_source = '';
				} elseif($row['Alarm_Priority'] == '9'){
					$class = 'bg-white';	
					$audio_source = '';					
				} elseif($row['Alarm_Priority'] == '91'){
					$class = 'bg-gray';	
					$audio_source = '';					
				} else {
					$class = '';
					$audio_source = '';
				}
				
				$reaction_time = $row['Event_Reaction_Time'] == '-1' ? '' : $row['Event_Reaction_Time'].' sec';

					$rows	.= '<tr class="'.$class.'" >
					<td>'.$row['Alarm_Priority'].'</td>
					<td>'.date('d-m-Y H:i:s', strtotime($row['DateTime'])).'</td>
					<td>'.$row['Account_Nmbr'].'</td>
					<td>'.$row['Account_Name'].'</td>
					<td>'.$row['Account_Group'].'</td>
					<td>'.$row['Event_Code'].' '.$row['Event_Zone'].'</td>
					<td>'.$row['Event_Description'].'</td>
					<td>'.$row['Event_Operator'].'</td>
					<td>'.$row['Event_Operator_First'].'</td>
					<td>'.$reaction_time.'</td>
					<td class="hidden"><audio controls autoplay loop>
							<source src="'.$audio_source. '" type="audio/wav" >
						</audio></td>
					</tr>';	
				
			};			
			
			$response_array = array(
				'status'	=> 1,
				'rows'		=> $rows				
			);
				
			// Return JSON array
			jsonArr($response_array);				
		}
		
		public function getLocationSignalCount(){
			$conn_scs = $this->db_conn;
			
			$year = date('Y');
			
			// Get weeknumber from today
			$now 			= date('YmdHis', strtotime('tomorrow'));
			$now_week_no 	= date('W');
			$table_now 		= 'signals_'.$year.'_'.$now_week_no;
			
			$query = "SELECT COUNT(*) AS signal ,PreProcessor_Account_Nmbr
					FROM scs_fep.".$table_now."
					WHERE LEFT(PreProcessor_Account_Nmbr, 6) IN (
					'010013',
					'010018',
					'020205',
					'010500',
					'010109',
					'010009',
					'010114',
					'010014',
					'010125',
					'010025',
					'010022',
					'600100',
					'800100',
					'020000',
					'010400',
					'010278',
					'010276',
					'010274',
					'010099',
					'010098',
					'010273',
					'010100',
					'010300'
					)
					GROUP BY PreProcessor_Account_Nmbr 
					ORDER BY signal DESC
					LIMIT 15";
			
			$res = $conn_scs->query($query);
			
			$rows = '<thead>
						<th>Account nr</th>
						<th>Dienst</th>
						<th>Signalen</th>
					</thead>';
					
			while ($row = $conn_scs->fetch($res)) {			
				$rows	.= '<tr><td>'.$row['PreProcessor_Account_Nmbr'].'</td><td>'.$this->getLocationService($row['PreProcessor_Account_Nmbr']).'</td><td>'.$row['signal'].'</td></tr>';																
			};			
			
			$response_array = array(
				'status'	=> 1,
				'rows'		=> $rows
			);
				
			// Return JSON array
			jsonArr($response_array);			
		}

		protected function getLocationService($account_nr){
			if(substr($account_nr, 0, 6) == "010013"){
				$regio = "VRAA";
				$dienst = "Brand";
			} elseif(substr($account_nr, 0, 6) == "010018"){
				$regio = "ZHZ";    
				$dienst = "Brand";				
			} elseif(substr($account_nr, 0, 6) == "020205"){
				$regio = "NHN";
				$dienst = "Brand";				
			} elseif(substr($account_nr, 0, 6) == "010500") {
				$regio = "VRH";    
				$dienst = "Brand";					
			} elseif(substr($account_nr, 0, 6) == "010109"){
				$regio = "VRU_ASB";     
				$dienst = "Brand";					
			} elseif(substr($account_nr, 0, 6) == "010009"){
				$regio = "VRU_KPN";   
				$dienst = "Brand";					
			} elseif(substr($account_nr, 0, 6) == "010114"){
				$regio = "VRGV_ASB";   
				$dienst = "Brand";					
			} elseif(substr($account_nr, 0, 6) == "010014"){
				$regio = "VRGV_KPN"; 
				$dienst = "Brand";					
			} elseif(substr($account_nr, 0, 6) == "010125"){
				$regio = "VRF_ASB"; 
				$dienst = "Brand";					
			} elseif(substr($account_nr, 0, 6) == "010025"){
				$regio = "VRF_KPN";
				$dienst = "Brand";	
			} elseif(substr($account_nr, 0, 6) == "010022") {
				$regio = "EHV";    
				$dienst = "Brand";	
			} elseif(substr($account_nr, 0, 6) == "600100") {
				$regio = "";    
				$dienst = "Brand";						
			} elseif(substr($account_nr, 0, 6) == "800100"){
				$regio = "";
				$dienst = "DIGI";	
			} elseif(substr($account_nr, 0, 6) == "020000"){
				$regio = "";
				$dienst = "RAC";					
			} elseif(substr($account_nr, 0, 6) == "010400"){
				$regio = "";
				$dienst = "ING";		
			} elseif(substr($account_nr, 0, 6) == "010278"){
				$regio = "";
				$dienst = "IPC_ADT";
			} elseif(substr($account_nr, 0, 6) == "010276"){
				$regio = "";
				$dienst = "IPC_SMC";
			} elseif(substr($account_nr, 0, 6) == "010274"){
				$regio = "";
				$dienst = "IPC";				
			} elseif(substr($account_nr, 0, 6) == "010099"){
				$regio = "";
				$dienst = "PAC";						
			} elseif(substr($account_nr, 0, 6) == "010098"){
				$regio = "";					
				$dienst = "VERIFIRE";	
			} elseif(substr($account_nr, 0, 6) == "010273"){
				$regio = "";					
				$dienst = "S&E";
			} elseif(substr($account_nr, 0, 6) == "010100"){
				$regio = "";					
				$dienst = "MIST";
			} elseif(substr($account_nr, 0, 6) == "010300"){
				$regio = "";					
				$dienst = "BNOT";				
			} else {
				$regio = "";
				$dienst = "";					
			}
			return $dienst;
		}

		protected function getSignalLoadWeeklyAvg($week_nr){
			$conn_scs = $this->db_conn;
			
			$year = date('Y');
			
			// Get weeknumber from today
			$table_now 		= 'signals_'.$year.'_'.$week_nr;	
			
			$query = "SELECT PreProcessor_Signal_DateTime AS signalDate, COUNT(*) AS signal
									FROM scs_fep.".$table_now."
									WHERE PreProcessor_Signal_DateTime 
									GROUP BY MID(PreProcessor_Signal_DateTime, 5, 6) 
									ORDER BY PreProcessor_Signal_DateTime";			
			$res = $conn_scs->query($query);
			$i = 0;
			while ($row = $conn_scs->fetch($res)) {			
				$i += $row['signal'];												
			};
			
			$row_cnt = $res->num_rows;
			
			$avg = $i / $row_cnt;
			
			return round($avg);
		}

	}