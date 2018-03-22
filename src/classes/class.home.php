<?php
	
	class Home {
		protected $succesMessage;
		protected $defaults = array(
			'host'	=>	DB_HOST,
			'user'	=>	DB_USER,
			'pass'	=>	DB_PASS,
			'db'  	=>	DB_NAME
		);
		
		function __construct($db_conn) {
			$this->db_conn 	= $db_conn;
			$this->locale 	= json_decode(file_get_contents(URL_ROOT.'/Src/lang/'.APP_LANG.'.json'), true);
		}	

		public function getSignalLoad($primair_conn = array()){
			//if($conn_scs != null){
			//	$conn_scs = $this->db_conn;	
			//}
			$opt_prim = array_merge($this->defaults,$primair_conn);
			try {
				$conn_scs 			= new SafeMySQL($opt_prim);
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
						$query = "SELECT `From` AS signalDate, COUNT(*) AS signal
									FROM sensor_events
									WHERE `From` 
									BETWEEN '2018-03-18 08:00:00' AND '2018-03-21 08:00:00' 
									GROUP BY MID(signalDate, 7, 8) 
									ORDER BY signalDate";
					
					
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
					'avg_last'	=> $this->getSignalLoadWeeklyAvg(date('W', strtotime('last week')),$opt_prim),
					'avg_now'	=> $this->getSignalLoadWeeklyAvg($now_week_no,$opt_prim)
				);
			} else {
				$response_array['status'] = 0;
			}
			// Return response_array
			return $response_array;				
				
		}
		
		protected function getSignalLoadWeeklyAvg($week_nr, $primair_conn = array()){
			$opt_prim = array_merge($this->defaults,$primair_conn);
			try {
				$conn_scs 			= new SafeMySQL($opt_prim);
				$connected 	= true;
			} catch (Exception $e) {
				$connected 	= false;
			}
			
			$year = date('Y');
			
			// Get weeknumber from today
			$table_now 		= 'signals_'.$year.'_'.$week_nr;	
			
			$query = "SELECT `From` AS signalDate, COUNT(*) AS signal
									FROM sensor_events
									WHERE `From` 
									BETWEEN '2018-03-18 08:00:00' AND '2018-03-21 08:00:00' 
									GROUP BY MID(signalDate, 7, 8) 
									ORDER BY signalDate";			
			$res = $conn_scs->query($query);
			$i = 0;
			while ($row = $conn_scs->fetch($res)) {			
				$i += $row['signal'];												
			};
			
			$row_cnt = $res->num_rows;
			
			$avg = $i / $row_cnt;
			
			return round($avg);
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