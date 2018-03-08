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

			$now 		= date('Ymd', strtotime('tomorrow')).'000000';			
			$past 		= date('YmdHis', strtotime('-24 hours'));

			$past_week_no = date('W', strtotime('past week'));
			$now_week_no = date('W');
			$year = date('Y');
			
			$table_past = 'signals_'.$year.'_'.$past_week_no;
			$table_now = 'signals_'.$year.'_'.$now_week_no;
			
			$rms_res = $conn_scs->query("(SELECT PreProcessor_Signal_DateTime AS signalDate, COUNT(*) AS signal
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
										ORDER BY PreProcessor_Signal_DateTime)");
				
				$signal = array('signal');
				$hours 	= array('x');
				
				while ($row = $conn_scs->fetch($rms_res)) {			
					$signal[]	= $row['signal'];				
					$hours[]	= date('d-m H:i', strtotime($row['signalDate']));													
				};			
				
				$response_array = array(
					'status'	=> 1,
					'signal'	=> $signal,
					'hours'		=> $hours
				);
				
			// Return JSON array
			jsonArr($response_array);				
				
		}
		

		
	}