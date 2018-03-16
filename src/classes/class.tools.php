<?php
	
	class Tools {
		protected $succesMessage;
		protected $defaults = array(
			'host'	=>	'',
			'user'	=>	'PreProcessor',
			'pass'	=>	'ikhouvanjou',
			'db'  	=>	'scs_stat'
		);
		protected $enableAudio = true;
		protected $enableAlarmThreshold = true;
		protected $alarmThreshold = 5;
		protected $alarmThresholdWarning = 10;
		protected $alarmThresholdDanger = 20;
		
		function __construct($db_conn) {
			$this->db_conn 	= $db_conn;
			$this->locale 	= json_decode(file_get_contents(URL_ROOT.'Src/lang/'.APP_LANG.'.json'), true);
		}
		
		public function getThreshold($post_val){
			$conn_scs = $this->db_conn;

			$priority = preg_replace("/[^0-9]/","", $post_val['prio']);
			$year 			= date('Y');

			$now_week_no 	= date('W');
			$past_week_no 	= date('W', strtotime('-24 hours'));
			
			$rms_res = $conn_scs->query("SELECT AVG(`Event_First_Action`) AS average, 
									COUNT(IF(`Event_First_Action` <= '60' ,1,NULL)) AS green, 
									COUNT(IF((`Event_First_Action` <= '120' AND `Event_First_Action` > '60') ,1,NULL)) AS orange, 
									COUNT(IF(`Event_First_Action` > '120' ,1,NULL)) AS red FROM 
									((SELECT `Event_First_Action` 
									FROM `events".$year.'_'.$now_week_no."`.`event_received` 
									WHERE `Event_Priority` =  ".$priority." 
									AND `Event_First_Action` != -1 
									ORDER BY DATETIME DESC LIMIT 100) 
									UNION (SELECT `Event_First_Action` 
									FROM `events".$year.'_'.$past_week_no."`.`event_received` 
									WHERE `Event_Priority` =  ".$priority." 
									AND `Event_First_Action` != -1 
									ORDER BY DATETIME DESC LIMIT 100) 
									LIMIT 100) AS First_Action");
				
				$green 	= array('green');
				$orange = array('orange');
				$red 	= array('red');
				$avg 	= array();
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
					'avg'			=> round($avg) . ' sec'
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

		public function getSignalLoadEvents($primair_conn = array()){
			$table_now 		= 'events'.date('Y').'_'.date('W');		
			
			$this->defaults = array(
				'user'	=>	'scsClient',
				'pass'	=>	'ikhouvanjou',
				'db'  	=>	$table_now
			);
			$opt_prim = array_merge($this->defaults,$primair_conn);
			try {
				$conn_scs 			= (!empty($primair_conn)) ? new SafeMySQL($opt_prim) : null;
				$connected 	= true;
			} catch (Exception $e) {
				$connected 	= false;
			}			
			
			if($connected){
				$past_24 = date('YmdH'.'0000', strtotime('-24 hours'));
				$now_24 = date('YmdH'.'0000', strtotime('+1 hour'));
				
				$period = new DatePeriod(
					new DateTime($past_24),
					new DateInterval('PT1H'),
					new DateTime($now_24)
				);
				
				$q = '';
				$signal_str = '';
				$i= 1;
				foreach($period as $date){
					$minus_1_hour = date('YmdH', strtotime('-1 hour', strtotime($date->format('YmdHis'))));
					$q .= "SUM(IFNULL((`DateTime` > '".$minus_1_hour."0000' AND `DateTime` <= '".$date->format('YmdHis')."'),0)) AS h".$i.",";
					$signal_str .= '$row["h'.$i.'"]';
					$i++;
				}
			
				try {			
					$query = "SELECT
									".$q."
									Event_Type
									FROM `".$table_now."`.`event_received`  
									WHERE `DateTime` > '".date('YmdH'.'0000', strtotime('-25 hours'))."' AND `DateTime` < '".$now_24."'
									GROUP BY  Event_Type
									ORDER BY h1 DESC
									LIMIT 5;";
							
					$res = $conn_scs->query($query);
					
					$signal = array();
					$hours 	= array('x');
					$groups = array();
					
					for($i= 0;$i<25;$i++){
						$plus_1_hour = strtotime($past_24) + 60*60*$i;
						$hours[] = date('H:i', $plus_1_hour);
					}

					while ($row = $conn_scs->fetch($res)) {	
						$signal[] = array($row['Event_Type'], $row['h1'],$row['h2'],$row['h3'],$row['h4'],$row['h5'],$row['h6'],$row['h7'],$row['h8'],$row['h9'],$row['h10'],$row['h11'],$row['h12'],$row['h13'],$row['h14'],$row['h15'],$row['h16'],$row['h17'],$row['h18'],$row['h19'],$row['h20'],$row['h21'],$row['h22'],$row['h23'],$row['h24'],$row['h25']);
						$groups[] = $row['Event_Type'];
					};			
					array_pop($hours);
					
				}  catch (Exception $e) {
					return $response_array['status'] = 0;
				}
				$signal[] = $hours;
				
				$response_array = array(
					'status'	=> 1,
					'signal'	=> $signal,
					'groups'	=> $groups
				);
			} else {
				$response_array['status'] = 0;
			}
			
			jsonArr($response_array);			
				
		}		
		
		public function getSignalLoad($primair_conn = array()){
			//if($conn_scs != null){
			//	$conn_scs = $this->db_conn;	
			//}
			$opt_prim = array_merge($this->defaults,$primair_conn);
			try {
				$conn_scs 			= (!empty($primair_conn)) ? new SafeMySQL($opt_prim) : null;
				$connected 	= true;
			} catch (Exception $e) {
				$connected 	= false;
			}			
			
			if($connected){
				$year = date('Y');
				
				// Get weeknumber from today
				$now 			= date('YmdHis', strtotime('+1 hour'));
				$now_week_no 	= date('W');
				$table_now 		= 'signals_'.$year.'_'.$now_week_no;
				
				// Get weeknumber from past 24 hours
				$past 			= date('YmdHis', strtotime('-24 hours'));
				$past_week_no 	= date('W', strtotime('-24 hours'));
				$table_past 	= 'signals_'.$year.'_'.$past_week_no;
				
				// Check if both dates are in the same week and specify query
				try {			
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
					$t = array();
					while ($row = $conn_scs->fetch($res)) {	
						
						$signal[]	= $row['signal'];				
						$t[]		= $row['signal'];				
						$hours[]	= date('H:i', strtotime($row['signalDate']));													
					};			
					array_pop($signal);
					array_pop($hours);
				}  catch (Exception $e) {
					return $response_array['status'] = 0;
				}
				
				$c_hours = count($hours) -1;
				$hour = array();
				for($i=0;$i<$c_hours; $i++){
					$hour[] = $i;
				}
				
				//$hour = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25);
				$trendarray = $this->trendLineAnalyse($hour, $t );
				
				$trend = array('trend');
				foreach ( $hour as $item ) {
					$number = ( $trendarray['slope'] * $item ) + $trendarray['intercept'];
					$number = ( $number <= 0 )? 0 : $number;
					$trend[] = round($number);
				}

				
				$response_array = array(
					'status'	=> 1,
					'signal'	=> $signal,
					'trend'		=> $trend,
					'hours'		=> $hours,
					'avg_last'	=> $this->getSignalLoadWeeklyAvg(date('W', strtotime('last week'))),
					'avg_now'	=> $this->getSignalLoadWeeklyAvg($now_week_no)
				);
			} else {
				$response_array['status'] = 0;
			}
			// Return response_array
			return $response_array;				
				
		}
		
		public function getPendingEvents($event_type = ''){
			$conn_scs = $this->db_conn;
			
			$year = date('Y');
			
			// Get weeknumber from today
			$now 			= date('YmdHis', strtotime('tomorrow'));
			$now_week_no 	= date('W');
			$table_now 		= 'signals_'.$year.'_'.$now_week_no;

			if($event_type == 'TASK'){
				$where = "WHERE Event_Type IN ('TASK') OR Alarm_Priority IN (91)";
				$order = "ORDER BY `Alarm_Priority` ASC, `Alarm_Priority_Original` ASC, `DateTime` ASC";
			} else {
				$where = "WHERE Event_Type NOT IN ('TRTS', 'TASK') AND Alarm_Priority NOT IN ('91')";
				$order = "ORDER BY `Alarm_Priority` ASC, `DateTime` ASC";
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
					`Event_Type`,
					`Event_Description`,
					`Event_Operator`,
					`Event_Operator_First`,
					`Event_Reaction_Time`
					FROM `scs`.`scs_pending_events`
					".$where.$order;
			
			$res = $conn_scs->query($query);
				
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
					</thead>';
					
			$count_arr = $this->getAlarmDescriptionCount();
			//var_dump($count_arr);	
			while ($row = $conn_scs->fetch($res)) {	
				$reaction_time = $row['Event_Reaction_Time'] != '-1' ?  $row['Event_Reaction_Time'] : '0';
				$dt1 = new DateTime('@0');
				$dt2 = new DateTime("@$reaction_time");
				$if_day = ($dt1->diff($dt2)->format('%a')) ? '%a d, ' : '';
				$format_time = $row['Event_Reaction_Time'] != '-1' ? $dt1->diff($dt2)->format($if_day. '%H:%I:%S') : '';			
			
				//$reaction_time = $row['Event_Reaction_Time'] != '-1' ?  gmdate('H:i:s',$row['Event_Reaction_Time']) : '';
					
				$org_arr = $this->setPriority($row['Alarm_Priority_Original']);
				$org_prio = ($row['Alarm_Priority'] != $row['Alarm_Priority_Original']) ? '<span class="badge '.$org_arr['class'].'">'.$row['Alarm_Priority_Original'].'</span>' : '';
				
				$prio_arr = $this->setPriority($row['Alarm_Priority'],$row['Event_Operator']);
				
				if($this->enableAlarmThreshold == true && $row['Account_Group'] == @$count_arr[$row['Account_Group']]['group_name'] && @$count_arr[$row['Account_Group']][$row['Event_Type']]['e_type_count'] >= $this->alarmThreshold){
					$rows .= '';
				} else {
					$rows .= '<tr class="'.$prio_arr['class'].'" >
					<td>'.$row['Alarm_Priority'].' '.$org_prio.'</td>
					<td>'.date('d-m-y H:i:s', strtotime($row['DateTime'])).'</td>
					<td>'.$row['Account_Nmbr'].'</td>
					<td>'.$row['Account_Name'].'</td>
					<td>'.$row['Account_Group'].'</td>
					<td>'.$row['Event_Code'].' '.$row['Event_Zone'].'</td>
					<td>'.$row['Event_Description'].'</td>
					<td>'.$row['Event_Operator'].'</td>
					<td>'.$row['Event_Operator_First'].'</td>
					<td>'.$format_time.'</td>
					</tr>';						
				}
				
			};			
			
			$audio = $conn_scs->getRow("SELECT `Alarm_Priority`, `Event_Operator` FROM `scs`.`scs_pending_events` WHERE Alarm_Priority IN ('1','2','3','4','5') ORDER BY `Event_Operator` ASC, `Alarm_Priority` ASC, `DateTime` ASC");
			$audio_arr = $this->setPriority($audio['Alarm_Priority'],$audio['Event_Operator']);
			
			$response_array = array(
				'status'	=> 1,
				'audio'		=> $audio_arr['audio'],
				'rows'		=> $rows,
				'sound'		=> $this->enableAudio == true ? 'fa fa-volume-up' : 'fa fa-volume-off' 			
			);
				
			// Return JSON array
			jsonArr($response_array);				
		}

		public function getPendingEventsGouped(){
			if($this->enableAlarmThreshold == true){
				$count = $this->getAlarmDescriptionCount();	
				//var_dump($count);
				
				$blocks = '';
				foreach($count as $group_name => $val){
					$event_text = '';
					$name = $this->db_conn->getOne("SELECT SCS_Dealer_Name FROM `scs`.`scs_dealer_status` WHERE SCS_Dealer_Code = '".$group_name."'");

					foreach($val as $val_arr => $event_type_arr){
						if($val_arr != 'group_name'){
							
							if($event_type_arr['e_type_count'] > $this->alarmThresholdDanger){
								$event_class = 'red-bg';
								$event_icon = 'fa fa-minus-circle';
							} elseif($event_type_arr['e_type_count'] > $this->alarmThresholdWarning){
								$event_class = 'yellow-bg';
								$event_icon = 'fa fa-warning';
							} elseif($event_type_arr['e_type_count'] >= $this->alarmThreshold) {
								$event_class = 'blue-bg';
								$event_icon = 'fa fa-info-circle';
							} else {
								$event_class = 'blue-bg';
							}
							
							if($event_type_arr['e_type_count'] >= $this->alarmThreshold){
								$event_text =  $event_type_arr['e_type_name'];
								
								$blocks .= '<div class="col-lg-2">
									<div class="widget '.$event_class.' p-sm text-center">
										<div class="m-b-md">
											<i class="'.$event_icon.' fa-3x"></i>
											<h1 class="m-xs">'.$event_type_arr['e_type_count'].'</h1>
											<h3 class="font-bold no-margins">
												'.substr($name,0,23).'
											</h3>
											<small>'.$event_text.'</small>
										</div>
									</div>
								</div>';					
							}
	
						}
	
					}
				
				}

				if($blocks != ''){
					$response_array = array(
						'status'	=> 1,
						'blocks'	=> $blocks				
					);					
				} else {
					$response_array['status'] = 0;
				}				
			} else {
				$response_array['status'] = 0;
			}
			
				
			// Return JSON array
			jsonArr($response_array);	
			
		}
		
		public function getLocationSignalCount($limit_count){
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
					'010098',
					'010273',
					'010100',
					'010300'
					)
					GROUP BY PreProcessor_Account_Nmbr 
					ORDER BY signal DESC
					LIMIT ".$limit_count;
			
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
		
		public function getPortMonitor(){
			/*
			 * TODO: Make blocks available through database
			 *
			*/
			$blocks_asb ='';
			$blocks_kpn ='';
			$blocks_gw = '';
			
			
			//$blocks_asb .= $this->getPortMonitorBlock('BW ASB (EHV)',array('host'=>'10.53.183.101'), array('host'=>'10.53.183.105'));
			//$blocks_asb .= $this->getPortMonitorBlock('BW ASB (VRF)',array('host'=>'10.53.254.241','user'=>'root','pass'=>'asbbv'));
			//
			//$blocks_kpn .= $this->getPortMonitorBlock('BW KPN (EHV)',array('host'=>'10.53.183.21'), array('host'=>'10.53.183.22'));
			//
			//$blocks_gw .= $this->getPortMonitorBlock('Beheercentrum FEP',SCS_DB_CONN, array('host'=>'172.16.8.11','user'=>'scsClient'));
			
			$blocks_asb .= $this->getPortMonitorBlock('BW ASB (EHV)',array('host'=>'10.53.183.101'));
			//$blocks_asb .= $this->getPortMonitorBlock('BW ASB (VRF)',array('host'=>'10.53.254.241','user'=>'root','pass'=>'asbbv'));
			//$blocks_asb .= $this->getPortMonitorBlock('BW ASB (VR GV)',array('host'=>'10.53.254.209'));
			//$blocks_asb .= $this->getPortMonitorBlock('BW ASB (VRU)',array('host'=>'10.53.254.50'));
			
			$blocks_asb .= $this->getPortMonitorBlock('BW KPN (EHV)',array('host'=>'10.53.183.21'), array(), array('host'=>'10.53.183.9','user'=>'root','pass'=>'asbbv','db'=>'scs_fep'));
			$blocks_asb .= $this->getPortMonitorBlock('BW HRH',array('host'=>'10.53.64.92'));
			//$blocks_asb .= $this->getPortMonitorBlock('BW NHN',array('host'=>'192.168.100.197'));
			//$blocks_kpn .= $this->getPortMonitorBlock('BW KPN VF (25))',array('host'=>'10.53.254.225'));
			//$blocks_kpn .= $this->getPortMonitorBlock('BW KPN VGV (14)',array('host'=>'10.53.254.193'));
			//$blocks_kpn .= $this->getPortMonitorBlock('BW KPN VRAA (13)',array('host'=>'10.53.231.130'));			
			//$blocks_kpn .= $this->getPortMonitorBlock('BW KPN ZHZ (18)',array('host'=>'10.53.183.130'));
			
			$blocks_gw .= $this->getPortMonitorBlock('Beheercentrum FEP',SCS_DB_CONN);				
			$blocks_gw .= $this->getPortMonitorAoip('AOIP gateway',array('host'=>'192.168.100.60','user'=>'PreProcessorWEB','pass'=>'asb','db'=>'scs_fep'));
			
			$response_array = array(
				'status'			=> 1,
				'block_asb'			=> $blocks_asb,
				'block_kpn'			=> $blocks_kpn,
				'block_gw'			=> $blocks_gw
			);
				
			// Return JSON array
			jsonArr($response_array);				
		}

		public function getPortMonitorAoip($monitor_name, $primair_conn = array()){			
			$opt_prim = array_merge($this->defaults,$primair_conn);	
			
			try {
				$conn_scs 	= new SafeMySQL($opt_prim);
				$connected 	= true;
			} catch (Exception $e) {
				$connected 	= false;
			}				
			
			if($connected){
				$query = "SELECT `SCS_Gateway_IP`, `SCS_Gateway_Name`, `SCS_Gateway_Status` FROM `scs_gateway_table`";		
				$res = $conn_scs->query($query);
					
				$port_stat = array();
				while ($row = $conn_scs->fetch($res, 2)) {	
					$int = ($row[2] == '5') ? 1 : 2;
					$port_stat[] = $this->setPortState($row[2]);
				}
	
				$res_err = $conn_scs->query($query." WHERE `SCS_Gateway_Status` NOT IN ('5')");
						
				$port_err = array();
				while ($row_err = $conn_scs->fetch($res_err, 2)) {
					$port_err[] = '<small style="color:white;">'.$row_err[0].'</small> - '.$row_err[1];
				}
			} else {
				$port_err = array('Geen verbinding met SQL');
			}
			
			$class = (count($port_err) != 0) ? 'red-bg': 'text-white';
			
			$block = '<div class="col-md-4">';
			$block .= '<div class="widget '.$class.' p-sm text-center">';
			$block .= '<div class="m-b-md">';
			$block .= '<h1 class="m-xs">'.$monitor_name.'</h1>';
			
			if(count($port_err) != 0){
				$block .= '<h1 class="m-xs"><i class="fa fa-warning"></i></h1><h2 >'.implode('<br>',$port_err).'</h2>';
			} else {
				$block .= '<h5 class="font-bold">Poort status</h5><div class="p_status">'.implode(',', $port_stat).'</div>';	
				$signal = $this->getSignalLoad($primair_conn);
				if($signal['status'] == 1){
					$signal_s = array_slice($signal['signal'], 1);
					//$signal_s = array_shift($signal['signal']);
					$block .= '<h5 class="font-bold">Signaalbelasting (24h)</h5>';
					$block .= '<table width="100%"><tr><td rowspan="2"><div class="sparkline m-b-sm">'.implode(',',$signal_s).'</div></td><td><span class="label label-warning pull-right">High '.max($signal_s).'</span></td></tr><tr><td><span class="label label-primary pull-right">Low '.min($signal_s).'</span></td></tr></table>';
				}				
			}
						
			$block .= '</div>';
			$block .= '</div>';
			$block .= '</div>';	
			
			return $block;
		}
		
		public function getPortMonitorBlock($monitor_name, $primair_conn = array(), $backup_conn = array(), $signal_conn = array()){
			
			$opt_prim = array_merge($this->defaults,$primair_conn);
			$opt_back = array_merge($this->defaults,$backup_conn);
			try {
				$conn_scs 			= (!empty($primair_conn)) ? new SafeMySQL($opt_prim) : null;
				$conn_scs_backup 	= (!empty($backup_conn)) ? new SafeMySQL($opt_back) : null;
				$connected 	= true;
			} catch (Exception $e) {
				$connected 	= false;
			}
			
			$port_err 		= array();
			$block_primair 	= '';
			$block_backup 	= '';
			if($connected){	
				$query = "SELECT `SCS_Stat_Port`, `SCS_Stat_Port_Name`, `SCS_Stat_Port_Active`, `SCS_Stat_Port_State` FROM `scs_stat`.`scs_stat_port`";	
				if($primair_conn != null){				
					$res = $conn_scs->query($query." ORDER BY `SCS_Stat_Port` ASC");
						
					$port_stat = array();
					while ($row = $conn_scs->fetch($res)) {	
						$port_stat[] = $this->setPortState($row['SCS_Stat_Port_State']);
					}
		
					$res_err = $conn_scs->query($query." WHERE `SCS_Stat_Port_State` = 1  ORDER BY `SCS_Stat_Port` ASC");
							
					$port_err = array();
					while ($row_err = $conn_scs->fetch($res_err)) {
						$port_err[] = '<small style="color:white;">Poort: '.$row_err['SCS_Stat_Port'].'</small> - '.$row_err['SCS_Stat_Port_Name'];
					}
					$block_primair = '<h5 class="font-bold">Poort status primair</h5><div class="p_status">'.implode(',', $port_stat).'</div>';				
				}
				$port_stat_b = array();
				
				if($backup_conn != null){
					$res_b = $conn_scs_backup->query($query." ORDER BY `SCS_Stat_Port` ASC");
					while ($row_b = $conn_scs_backup->fetch($res_b)) {	
						$port_stat_b[] = $this->setPortState($row_b['SCS_Stat_Port_State']);
					}
					$block_backup = '<h5 class="font-bold no-margins">Poort status backup</h5><div class="p_status" >'.implode(',', $port_stat_b).'</div>';
				}
				$port_stat_b = array_pad($port_stat_b, 24, 0);
			} else {
				$port_err = array('Geen verbinding met SQL');
			}
			
			$class = (count($port_err) != 0) ? 'red-bg': 'text-white';
			
			$block = '<div class="col-md-4">';
			$block .= '<div class="widget '.$class.' p-sm text-center">';
			$block .= '<div class="m-b-md">';
			$block .= '<h1 class="m-xs">'.$monitor_name.'</h1>';
			
			if(count($port_err) != 0){
				$block .= '<h1><i class="fa fa-warning"></i></h1><h2 class="font-bold">'.implode('<br>',$port_err).'</h2>';
			} else {
				$block .= $block_primair;
				$block .= $block_backup;
				$sign_conn = (empty($signal_conn)) ? $primair_conn : $signal_conn;
				$signal = $this->getSignalLoad($sign_conn);
				if($signal['status'] == 1){
					$signal_s = array_slice($signal['signal'], 1);
					$block .= '<h5 class="font-bold">Signaalbelasting (24h)</h5>';
					$block .= '<table width="100%"><tr><td rowspan="2"><div class="sparkline m-b-sm">'.implode(',',$signal_s).'</div></td><td><span class="label label-warning pull-right">High '.max($signal_s).'</span></td></tr><tr><td><span class="label label-primary pull-right">Low '.min($signal_s).'</span></td></tr></table>';
				}				
			}
			
			$block .= '</div>';
			$block .= '</div>';
			$block .= '</div>';	
			
			return $block;	
		}
		
		protected function setPortState($state){
			switch($state) {
				case '-1': // Disabled
					$int = 0;
					break;
				case 1: // Error
					$int = 2;
					break;
				case 2: // Okay
					$int = 1;
					break;						
				case 3: // Inactive
					$int = 0;
					break;
				case 4: // No signal
					$int = 3;
					break;
				case 5: // AOIP gateway active
					$int = 1;
					break;					
				default:
					$int = 1;
					break;
			}
			return $int;
		}		
		
		protected function getAlarmDescriptionCount(){
			$conn_scs = $this->db_conn;
			// Bundle on event type
			$count = $conn_scs->query("SELECT Account_Group, GROUP_CONCAT(event_type SEPARATOR ';') AS e_type FROM `scs`.`scs_pending_events` WHERE `Alarm_Priority` NOT IN ('91') GROUP BY Account_Group");
				
			$count_arr = array();

			while ($row_count = $conn_scs->fetch($count)) {	
				if($row_count['Account_Group'] != ''){
					$str = $row_count['e_type'];
					
					$event_type_arr = explode(';', $str);
					$counts = array_count_values($event_type_arr);
					
					$bb = array();
					$bb['group_name'] = $row_count['Account_Group']; 
					
					foreach($event_type_arr as $a){
					
						$bb[$a] = array(
							'e_type_name' 	=> $conn_scs->getOne("SELECT SCS_Alarm_Type_Short_Description FROM `scs`.`scs_alarm_type` WHERE SCS_Alarm_Type_Code = '".$a."'"), 
							'e_type_count' 	=> $counts[$a]
						);
					}
					$count_arr[$row_count['Account_Group']] = $bb;					
				}

			}

			return $count_arr;
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

		protected function setPriority($prio_nr, $event_operator = null){
			if($prio_nr == '1'){
				$class = 'bg-danger';
				if($event_operator == null){
					$audio_source = URL_ROOT. 'src/libs/scs_sounds/P1.wav';	
				} else {
					$audio_source = '';
				}
			} elseif($prio_nr == '2'){
				$class = 'bg-danger';
				if($event_operator == null){
					$audio_source = URL_ROOT. 'src/libs/scs_sounds/P2.wav';	
				} else {
					$audio_source = '';
				}
			} elseif($prio_nr == '3'){
				$class = 'bg-danger';
				if($event_operator == null){
					$audio_source = URL_ROOT. 'src/libs/scs_sounds/P3.wav';	
				} else {
					$audio_source = '';
				}
			} elseif($prio_nr == '4'){
				$class = 'bg-warning';
				if($event_operator == null){
					$audio_source = URL_ROOT. 'src/libs/scs_sounds/P4.wav';	
				} else {
					$audio_source = '';
				}
			} elseif($prio_nr == '5'){
				$class = 'bg-yellow';
				if($event_operator == null){
					$audio_source = URL_ROOT. 'src/libs/scs_sounds/P5.wav';	
				} else {
					$audio_source = '';
				}				
			} elseif($prio_nr == '7'){
				$class = 'bg-primary';
				$audio_source = '';
			} elseif($prio_nr == '8'){
				$class = 'bg-dark-green';
				$audio_source = '';
			} elseif($prio_nr == '9'){
				$class = 'bg-white';	
				$audio_source = '';					
			} elseif($prio_nr == '91'){
				$class = 'bg-gray';	
				$audio_source = '';					
			} else {
				$class = '';
				$audio_source = '';
			}
			if($this->enableAudio == false){
				$audio_source = '';
			}
			return $arr = array(
				'class' => $class,
				'audio' => $audio_source
			);
		}

		protected function trendLineAnalyse( $x, $y ){

			$n     = count($x);     // number of items in the array
			$x_sum = array_sum($x); // sum of all X values
			$y_sum = array_sum($y); // sum of all Y values
			
			$xx_sum = 0;
			$xy_sum = 0;
			
			for($i = 0; $i < $n; $i++) {
				@$xy_sum += ( $x[$i]*$y[$i] );
				@$xx_sum += ( $x[$i]*$x[$i] );
			}
			
			// Slope
			$slope = ( ( $n * $xy_sum ) - ( $x_sum * $y_sum ) ) / ( ( $n * $xx_sum ) - ( $x_sum * $x_sum ) );
			
			// calculate intercept
			$intercept = ( $y_sum - ( $slope * $x_sum ) ) / $n;
			
			return array( 
				'slope'     => $slope,
				'intercept' => $intercept,
			);				
		}
		
	}