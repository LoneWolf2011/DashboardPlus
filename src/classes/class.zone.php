<?php

class Zone
{
    protected $succesMessage;
    protected $site_id;
    protected $date_now_day;
    protected $date_past_day;
    
    function __construct($db_conn, $site_id = '')
    {
        $this->db_conn = $db_conn;
        $this->locale  = json_decode(file_get_contents(URL_ROOT . '/Src/lang/' . APP_LANG . '.json'), true);
        $this->site_id = preg_replace("/[^0-9]/", "", $site_id);
        
        $this->date_now_day  = date('Y-m-d H:i:s');
        $this->date_past_day = date('Y-m-d H:i:s', strtotime('-24 hours'));
    }
	
	public function getZoneWaitTime($device_ip_address,$group='')
	{
		$conn  = $this->db_conn;
		try {
			$res = $conn->getAll("SELECT `datetime`,`fw`, `count`, `ip_address` FROM sensor_data WHERE `ip_address` = ?s GROUP BY MID(`datetime`, 7,8) ORDER BY `id` DESC  LIMIT 2;",$device_ip_address);		
			
			$out_per_hour 	= (abs((@$res[0]['fw'] -  @$res[1]['fw'])) / 3600) / 60; 
			$queue_per_hour = abs((@$res[0]['count'] -  @$res[1]['count'])) / 3600; 
			
			$get_count = $this->getZoneCountsTotal(@$res[0]['ip_address']);
			
			$waittime_in_sec = ($get_count['queue'] == 0 || $out_per_hour == 0)? 0 :$get_count['queue'] / $out_per_hour;
			
			return array(
				'out_diff' 		=> $out_per_hour,
				'queue_diff' 	=> $queue_per_hour,
				'seconds' 		=> $waittime_in_sec,
				'minutes'		=> $waittime_in_sec / 60,
				'hours' 		=> $waittime_in_sec / 3600
			);
		}
		catch (Exception $e) {
			return $response_array['status'] = 0;
		}		
	}   

    public function getZoneValCount($group)
    {
        $conn = $this->db_conn;
        try {
            //$query = "SELECT GROUP_CONCAT(`value` SEPARATOR ';') AS `zone_count` FROM sensor_events WHERE `Group` = ?s AND `From` BETWEEN ?s AND ?s";
            $query = "SELECT `group`,`datetime` AS signalDate, `bw`,`fw` ,`count` FROM sensor_data  WHERE `group` = ?s AND `datetime` BETWEEN ?s AND ?s ?p GROUP BY MID(signalDate, 7, 8) ORDER BY signalDate";
               $qpart ='';
            if (!empty($this->site_nr)) {
                $qpart = $conn->parse(" AND `group` IN (SELECT `Group` FROM sensor_sites_group WHERE site_id = ?i)", $this->site_nr);
            }
            
            $res = $conn->query($query, $group, $this->date_past_day, $this->date_now_day, $qpart);
            
            $zones_count = array();
            $zones_name  = array();
            while ($row = $conn->fetch($res)) {
                $total = $row['bw'] + $row['fw'] + $row['count'];
                $zones_count[] = $total;
                $zones_name[]  = $row['group'];
                
            }
            
        }
        catch (Exception $e) {
            return $response_array['status'] = 0;
        }
        
        return array(
            'count' => implode(';', $zones_count),
            'name' => $zones_name
        );
    }
   
    public function getZoneDetails($zone_name)
    {
        $conn  = $this->db_conn;
		
		try {
			$group = $conn->getRow("SELECT `ip_address`,`Name`, `Group`, GROUP_CONCAT(DISTINCT `ip_address` SEPARATOR ';') AS devices FROM sensor_info WHERE `Group` = ?s GROUP BY `Group`", $zone_name);
			
			$devices_row       = array();
			$devices_wait_time = array();
			if ($group) {
				$devices_arr = explode(';', $group['devices']);
				
				$i = 0;
				foreach ($devices_arr as $ip_address) {
					$get_count = $this->getZoneCountsTotal($ip_address);
					$get_wait_time = $this->getZoneWaitTime($ip_address);
					
					$count_queue	= $get_count['queue'];
					$count_out		= ($get_count['forward'] > 0) ? $get_count['forward'] : 1;
					$count_total    = $count_queue + $count_out;
	
					$wait_time 		= '<i class="fa fa-clock-o"></i> '.gmdate('H:i:s',$get_wait_time['seconds']);
					$wait_avg 		= round($get_wait_time['seconds'] / $count_out);
					$wait_now 		= round($get_wait_time['seconds'] / 60);				
					//$wait_queue		= round($count_queue * $get_wait_time['queue_diff']);				
					
					$total_bg      	= ($count_queue > C_MIN_DANGER) ? 'red-bg' : (($count_queue > C_MIN_WARNING) ? 'yellow-bg' : 'dark-bg');				
					$avg_bg 		= ($wait_avg > C_AVG_DANGER) ? 'red-bg' : (($wait_avg > C_AVG_WARNING) ? 'yellow-bg' : 'dark-bg');				
	
					$zone_row 		= $conn->getRow("SELECT * FROM sensor_status WHERE `ip_address` = ?s ORDER BY `Datetime` DESC LIMIT 1", $ip_address);
					
					if ($zone_row['Status_request'] != 'OK') {
						$active = '<i class="fa fa-circle text-danger"></i>';
					} else {
						$active = '<i class="fa fa-circle text-navy"></i>';
					}
					$updatime = round($zone_row['Uptime'] / 1000);
					
					
					$location = '<tr><th>Serial</th><td>' . $zone_row['Serial_number'] . '</tr></td>
						<tr><th>IP address</th><td>' . $zone_row['IP_address'] . '</tr></td>
						<tr><th>Uptime</th><td>' . formatSecToTime($updatime) . '</tr></td>';
					
					$devices_row[] = '<div class="row">
							<div class="col-lg-3">
								<div class="ibox float-e-margins">
									<div class="ibox-content">
										<table class="table table-hover">
											<thead>
												<tr>
													<th>Device name</th>
													<th>' . $active . ' ' . $conn->getOne("SELECT `Name` FROM sensor_info WHERE `ip_address` = ?s",$ip_address) . '</th>
												</tr>
											</thead>
											<tbody >' . $location . '</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="widget p-lg text-center ' . $total_bg . '">
									<div class="m-b-md" style="color:white;">
										<i class="fa fa-user fa-4x"></i>
										<h1 class="m-xs" >' . $count_queue . '</h1>
										<h3 class="font-bold no-margins">
											Current queue
										</h3>
										<small>' .$wait_time . '</small>
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="widget style1 dark-bg">
									<div class="row vertical-align">
										<div class="col-xs-8">
											<h2><i class="fa fa-users"></i> <small style="color:inherit;">Total people</small></h2>
										</div>
										<div class="col-xs-4 text-right">
											<h2 class="font-bold" >' . $count_total . '</h2>
										</div>
									</div>
								</div>
								<div class="widget style1 dark-bg">
									<div class="row vertical-align">
										<div class="col-xs-8">
											<h2><i class="fa fa-sign-out fa-flip-horizontal"></i> <small style="color:inherit;">People out</small></h2>
										</div>
										<div class="col-xs-4 text-right">
											<h2 class="font-bold" >' . $count_out . '</h2>
										</div>
									</div>
								</div>
								<div class="widget style1 '.$avg_bg.'">
									<div class="row vertical-align">
										<div class="col-xs-8">
											<h2><i class="fa fa-clock-o"></i> <small style="color:inherit;">Avg wait</small></h2>
										</div>
										<div class="col-xs-4 text-right">
											<h2 class="font-bold" >' .gmdate('H:i:s',(int)$wait_avg) . '</h2>
										</div>
									</div>
								</div>				
							</div>
							<div class="col-lg-3">
								<div class="ibox float-e-margins">
									<div class="ibox-content">
										<small class="pull-right"><i class="fa fa-clock-o"></i> Current wait time</small>
										<div id="avg_wait_chart' . $i . '"></div>
									</div>
								</div>			
							</div>						
						</div>';
					
					$devices_wait_time[] = $wait_now;
					$i++;
				}
			} else {
				return $response_array['status'] = 0;
			}
		}
		catch (Exception $e) {
			return $response_array['status'] = 0;
		}			
			
        $response_array = array(
            'status' => 1,
            'devices' => $devices_row,
            'devices_wait_time' => $devices_wait_time
			
        );
        
        // Return response_array
        return $response_array;
    }
    	
    public function getZoneCountsTotal($ip_address, $group_name ='')
    {
        $conn = $this->db_conn;
        try {
			//$query = "SELECT ip_address, `name`, `group`, SUM(`fw`) AS forward, SUM(`bw`) AS backward,SUM(`count`) AS queue FROM sensor_info_values  WHERE `ip_address` = ?s AND `Group` = ?s";
			$query = "SELECT 
			ip_address, 
			SUM(`fw`) AS forward, 
			SUM(`bw`) AS backward,
			SUM(`count`) AS queue 
			FROM sensor_info_values  
			WHERE `ip_address` = ?s";
			
			$device    = array();
			$count_val = $conn->query($query, $ip_address);
			
			$q = 0;
			$f = 0;
			$b = 0;
			while ($row = $conn->fetch($count_val)) {
				$q += $row['queue'];
				$f += $row['forward'];
				$b += $row['backward'];
			}
		}
		catch (Exception $e) {
			return $response_array['status'] = 0;
		}  			
			
        return array(
            'queue' => $q,
            'forward' => $f,
            'backward' => $b
        );
    }
}
