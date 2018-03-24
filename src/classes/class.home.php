<?php
	
	class Home {
		protected $succesMessage;
		protected $site_nr;
		protected $date_now_day;
		protected $date_past_day;
		protected $defaults = array(
			'host'	=>	DB_HOST,
			'user'	=>	DB_USER,
			'pass'	=>	DB_PASS,
			'db'  	=>	DB_NAME
		);
		
		function __construct($db_conn,$site_nr='') {
			$this->db_conn 	= $db_conn;
			$this->locale 	= json_decode(file_get_contents(URL_ROOT.'/Src/lang/'.APP_LANG.'.json'), true);
			$this->site_nr 	= preg_replace("/[^0-9]/","",$site_nr);
			
			$this->date_now_day = '2018-03-23 08:00:00';//date('Y-m-d H:i:s');
			$this->date_past_day = '2018-03-20 08:00:00'; //date('Y-m-d H:i:s', strtotime('-24 hours'));
		}	
		
		public function getZones() {
			$conn = $this->db_conn;

			try {			
				$query = "SELECT `Group` AS zone, `site`, GROUP_CONCAT(DISTINCT  `IP_address` SEPARATOR ';') AS devices_ip, GROUP_CONCAT(DISTINCT  `Name` SEPARATOR ';') AS `devices`, GROUP_CONCAT(`value` SEPARATOR ';') AS `zone_count` FROM sensor_events";
				
				if(!empty($this->site_nr)){
					$query .= " WHERE `Site` = ?s";
				}	
				$query .= " GROUP BY `Group` ORDER BY `Group` ASC";
		
				$res = $conn->query($query,'site'.$this->site_nr);
				
				$zones = array();
				$i = 1;
				while ($row = $conn->fetch($res)) {	
					$z_count = $this->getZoneValCount($row['zone']);
					$zones[] = array(
						'id' 			=> $i,					
						'site' 			=> $row['site'],					
						'zone' 			=> $row['zone'],
						'zone_count' 	=> $z_count['count'],
						'wait' 			=> $this->getZoneWaitTime($row['zone']),						
						'devices' 		=> explode(';',$row['devices']),						
						'link' 			=> '<a href="'. URL_ROOT.'/view/zone/?site='.$this->site_nr.'&id='.$row['zone'].'">#'.$i.'</a>'
					);					
					$i++;
				};			

			}  catch (Exception $e) {
				return $response_array['status'] = 0;
			}	
						
			return $zones;
		}

		protected function getZoneValCount($zone){
			$conn = $this->db_conn;
			try {			
				//$query = "SELECT GROUP_CONCAT(`value` SEPARATOR ';') AS `zone_count` FROM sensor_events WHERE `Group` = ?s AND `From` BETWEEN ?s AND ?s";
				$query = "SELECT `From` AS signalDate, SUM(`Value`) AS `zone_count`, `Group` FROM sensor_events WHERE `Group` = ?s AND `From` BETWEEN ?s AND ?s";
				
				if(!empty($this->site_nr)){
					$query .= " AND `Site` = ?s";
				}	
				$query .= " GROUP BY MID(signalDate, 7, 8) ORDER BY signalDate";
		
				$res = $conn->query($query,$zone,$this->date_past_day,$this->date_now_day,'site'.$this->site_nr);
				
				$zones_count = array();
				$zones_name = array();
				while ($row = $conn->fetch($res)) {	
					
					$zones_count[] = $row['zone_count'];				
					$zones_name[] = $row['Group'];				
				
				};			

			}  catch (Exception $e) {
				return $response_array['status'] = 0;
			}

			return array(
				'count' => implode(';',$zones_count),
				'name'	=> $zones_name
			);
		}
		
		protected function getZoneWaitTime($zone){
			$conn = $this->db_conn;
			
			$count_this_hour = $conn->getOne("SELECT SUM(`Value`) AS total_count FROM sensor_events  WHERE `Group` = ?s AND `From` LIKE '".date('Y-m-d H')."%' GROUP BY `id` DESC", $zone);
			$count_past_hour = $conn->getOne("SELECT SUM(`Value`) AS total_count FROM sensor_events  WHERE `Group` = ?s AND `From` LIKE '".date('Y-m-d H', strtotime('-1 hour'))."%' GROUP BY `id` DESC", $zone);
						
			$count = ($count_past_hour - $count_this_hour == 0) ? 1 : $count_past_hour - $count_this_hour;
			
			$time_sec = round(3600/abs($count),1);
			$time_min = round($time_sec/60,1);
			
			$wait_time = $time_sec * $count_this_hour;
			
			return gmdate("H:i:s", $wait_time);
			//return $wait_time;
			
		}

		public function getPeopleCount($primair_conn = array()){
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
				
				// Check if both dates are in the same week and specify query
				try {			
					$query = "SELECT `From` AS signalDate, SUM(`Value`) AS signal, `site` FROM sensor_events  WHERE `From` BETWEEN ?s AND ?s";
									
					if(!empty($this->site_nr)){
						$query .= " AND `Site` = ?s";
					}					
					
					$query .= " GROUP BY MID(signalDate, 7, 8) ORDER BY signalDate";

					
					$res = $conn_scs->query($query,$this->date_past_day,$this->date_now_day,'site'.$this->site_nr);
					
					$signal = array();

					while ($row = $conn_scs->fetch($res)) {	
						
						$signal[]	= $row['signal'];															
					};			

				}  catch (Exception $e) {
					return $response_array['status'] = $e->getMessage();
				}
				
				$max_bg = (max($signal) > C_MAX_DANGER) ? 'red-bg' : ((max($signal) > C_MAX_WARNING) ? 'yellow-bg' : 'dark-bg');
				$min_bg = (min($signal) > C_MIN_DANGER) ? 'red-bg' : ((min($signal) > C_MIN_WARNING) ? 'yellow-bg' : 'dark-bg');
				$avg_bg = (round(array_sum($signal)/count($signal)) > C_AVG_DANGER) ? 'red-bg' : ((round(array_sum($signal)/count($signal)) > C_AVG_WARNING) ? 'yellow-bg' : 'dark-bg');
				
				$count = '<div class="widget style1 '.$max_bg.'">
                    <div class="row vertical-align">
                        <div class="col-xs-8">
                            <h2><i class="fa fa-user"></i> <small style="color:inherit;">max (24h)</small></h2>
                        </div>
                        <div class="col-xs-4 text-right">
                            <h2 class="font-bold" >'.max($signal).'</h2>
                        </div>
                    </div>
                </div>
				<div class="widget style1 '.$min_bg.'">
                    <div class="row vertical-align">
                        <div class="col-xs-8">
                           <h2><i class="fa fa-user"></i> <small style="color:inherit;">min (24h)</small></h2>
                        </div>
                        <div class="col-xs-4 text-right">
                            <h2 class="font-bold" id="c_min">'.min($signal).'</h2>
                        </div>
                    </div>
                </div>
				<div class="widget style1 '.$avg_bg.'">
                    <div class="row vertical-align">
                        <div class="col-xs-8">
                            <h2><i class="fa fa-user"></i> <small style="color:inherit;">avg (24h)</small></h2>
                        </div>
                        <div class="col-xs-4 text-right">
                            <h2 class="font-bold" id="c_avg">'.round(array_sum($signal)/count($signal)).'</h2>
                        </div>
                    </div>
                </div>';
				
				$total_bg = (array_sum($signal) > C_PER_DANGER) ? 'red-bg' : ((array_sum($signal) > C_PER_WARNING) ? 'yellow-bg' : 'navy-bg');
				
				$total = '<div class="widget '.$total_bg.' p-lg text-center">
					<div class="m-b-md">
						<i class="fa fa-user fa-4x"></i>
						<h1 class="m-xs" >'.array_sum($signal).'</h1>
						<h3 class="font-bold no-margins">
							Current count
						</h3>
						<small>total</small>
					</div>
				</div>';
				
				$location = 
				'<tr><th>Location</th><td>ASB</tr></td>
				<tr><th>Address</th><td>Boschdijk</tr></td>
				<tr><th>Zipcode</th><td>5627</tr></td>
				<tr><th>City</th><td>Eindhoven</tr></td>';
				
				$response_array = array(
					'status'	=> 1,
					'c_all'		=> $count,
					'c_total'	=> $total,
					'location'	=> $location
					//'c_max'		=> $trend,
					//'c_min'		=> $hours,
					//'c_avg'		=> $hours
				);
			} else {
				$response_array['status'] = 0;
			}
			// Return response_array
			return $response_array;				
				
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
				
				// Check if both dates are in the same week and specify query
				try {	
					$query = "SELECT `From` AS signalDate, SUM(`Value`) AS signal, `site` FROM sensor_events  WHERE `From` BETWEEN ?s AND ?s";
									
					if(!empty($this->site_nr)){
						$query .= " AND `Site` = ?s";
					}					
									
					$query .= " GROUP BY MID(signalDate, 7, 8) ORDER BY signalDate";
					
					$res = $conn_scs->query($query,$this->date_past_day,$this->date_now_day,'site'.$this->site_nr);
					
					$signal = array('signal');
					$hours 	= array('x');
					$t = array();
					while ($row = $conn_scs->fetch($res)) {	
						
						$signal[]	= $row['signal'];				
						$t[]		= $row['signal'];				
						$hours[]	= date('H:i', strtotime($row['signalDate']));													
					};			
					//array_pop($signal);
					//array_pop($hours);
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
					//'avg_last'	=> $this->getSignalLoadWeeklyAvg(date('W', strtotime('last week')),$opt_prim),
					//'avg_now'	=> $this->getSignalLoadWeeklyAvg($now_week_no,$opt_prim)
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
			
			$query = "SELECT `From` AS signalDate, COUNT(*) AS signal FROM sensor_events WHERE `From` BETWEEN ?s AND ?s GROUP BY MID(signalDate, 7, 8) ORDER BY signalDate";
									
			$res = $conn_scs->query($query,$this->date_past_day,$this->date_now_day);
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