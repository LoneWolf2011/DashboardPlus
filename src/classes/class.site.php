<?php

class Site
{
    protected $succesMessage;
    protected $site_nr;
    protected $date_now_day;
    protected $date_past_day;
    protected $defaults = array('host' => DB_HOST, 'user' => DB_USER, 'pass' => DB_PASS, 'db' => DB_NAME);
    
    function __construct($db_conn, $site_nr = '')
    {
        $this->db_conn = $db_conn;
        $this->locale  = json_decode(file_get_contents(ROOT_PATH . '/Src/lang/' . APP_LANG . '.json'), true);
        $this->site_nr = preg_replace("/[^0-9]/", "", $site_nr);
        
        $this->date_now_day  = date('Y-m-d H:i:s', strtotime('+24 hours')); // End of current day
        $this->date_past_day = date('Y-m-d 08:00:00'); // Beginning of current day
    }
    
    public function getSiteZones()
    {
        $conn = $this->db_conn;
        
        try {
			$groups = array();
			$get_group = $conn->query("SELECT `group` FROM sensor_sites_group WHERE `site_id` = ?i",$this->site_nr);
			while ($group_row = $conn->fetch($get_group)) {
				$groups[] = $group_row['group'];
			}
            $query = "SELECT 
						GROUP_CONCAT(DISTINCT  inf.`ip_address` SEPARATOR ';') AS devices_ip,
						GROUP_CONCAT(DISTINCT  inf.`name` SEPARATOR ';') AS devices,
						inf.`group`
						FROM sensor_info_values AS inf_val, sensor_info AS inf
						WHERE inf.`ip_address` IN (SELECT `ip_address` FROM sensor_info WHERE `group` IN (?a))
						GROUP BY inf.`group` ";
            
            $res   = $conn->query($query, $groups);
			
			$zone_obj = new Zone($conn, $this->site_nr); 
			
            $i     = 1;
            $count_total = 0;
            $count_out   = 0;
		    $zones = array(); 
			
            while ($row = $conn->fetch($res)) {
				
				$z_count = $zone_obj->getZoneValCount($row['group']);

					
				$zone_waittime = array();
				
				$device_ips = explode(';', $row['devices_ip']);
				
				foreach($device_ips as $device_ip){
					$zone_waittime[] = $zone_obj->getZoneWaitTime($device_ip, $row['group']);
					$get_count = $zone_obj->getZoneCountsTotal($device_ip, $row['group']);
                
					$count_total += $get_count['queue'];
					$count_out += $get_count['forward'];					
				}
				$total_sec = 0;
				foreach($zone_waittime as $waittime){
					$total_sec += $waittime['seconds'];					
				}				
				
                $zones[] = array(
                    'id' 			=> $i,
                    'site' 			=> $conn->getOne("SELECT `site_id` FROM sensor_sites_group WHERE `Group` = ?s", $row['group']),
                    'site_group' 	=> $row['group'],
                    'zone_count' 	=> $z_count['count'],
                    'zone_wait' 	=> $total_sec,
                    'zone_devices' 	=> explode(';', $row['devices']),
                    'devices_ip' 	=> $device_ips,
                    'zone_link' 	=> '<a  style="color:#f6a821;" href="' . URL_ROOT . '/view/zone/?site=' . $this->site_nr . '&id=' . $row['group'] . '">#' . $i . '</a>'
                );
                $i++;
            }
            
        }
        catch (Exception $e) {
            return $response_array['status'] = 0;
        }
        
        return $zones;
    }
 
    public function getSiteZonesTable()
    {
        $conn = $this->db_conn;
		
		try {
			$rows = array();
			foreach ($this->getSiteZones() as $key => $val) {
				//$count = $conn->getOne("SELECT `Value` FROM sensor_events WHERE `Group` = ?s AND `Label` = 'count' ORDER BY `id` DESC LIMIT 1",$val['group']);
				$count = $conn->getOne("SELECT SUM(`count`) FROM sensor_info_values WHERE `Group` = ?s", $val['site_group']);
				if ($count > 0) {
					$label = '<span class="badge badge-success">' . $count . '</span>';
				} else {
					$label = '';
				}
				
				$rows[] = array(
					$val['zone_link'],
					$val['site_group'],
					implode('<br>', $val['zone_devices']),
					gmdate('H:i:s',$val['zone_wait']),
					$label,
					'<span class="btn btn-primary btn-xs zone_graph" rel="' . $val['site_group'] . '" value="' . $val['zone_count'] . '">graph</span>'
				);
			}
		}
        catch (Exception $e) {
            return $response_array['status'] = 0;
        }
		
        $response_array = array(
            'status' 	=> 1,
            'rows' 		=> $rows
        );
        
        return $response_array;
    }
    
    public function getSitePeopleCount()
    {
        $conn = $this->db_conn;

        try {		
			$zones     = $this->getSiteZones();
			$count_que = 0;
			$count_out = 0;
			$total_wait = 0;
			
			
			foreach ($zones as $zone => $val) {
				$get_count = $this->getCountsTotal($val['site_group']);
				$total_wait += $val['zone_wait'];
				$count_que += $get_count['queue'];
				$count_out += $get_count['forward'];
			}
			$wait_avg = $total_wait / $count_out;
			$count_total = $count_que + $count_out;
				
			$total_bg = ($count_que > C_MIN_DANGER) ? 'red-bg' : (($count_que > C_MIN_WARNING) ? 'yellow-bg' : 'dark-bg');
			$avg_bg = ($wait_avg > C_AVG_DANGER) ? 'red-bg' : (($wait_avg > C_AVG_WARNING) ? 'yellow-bg' : 'dark-bg');
        
            $count_div = '<div class="widget style1 dark-bg">
                    <div class="row vertical-align">
                        <div class="col-xs-7">
                            <h2><i class="fa fa-users"></i> <small style="color:inherit;">Total people</small></h2>
                        </div>
                        <div class="col-xs-5 text-right">
                            <h2 class="font-bold" >' . $count_total . '</h2>
                        </div>
                    </div>
                </div>
				<div class="widget style1 dark-bg">
                    <div class="row vertical-align">
                        <div class="col-xs-7">
                          <h2><i class="fa fa-sign-out fa-flip-horizontal"></i> <small style="color:inherit;">People out</small></h2>
                        </div>
                        <div class="col-xs-5 text-right">
                            <h2 class="font-bold" id="c_min">' . $count_out . '</h2>
                        </div>
                    </div>
                </div>
				<div class="widget style1 ' . $avg_bg . '">
                    <div class="row vertical-align">
                        <div class="col-xs-7">
                            <h2><i class="fa fa-clock-o"></i> <small style="color:inherit;">Avg wait</small></h2>
                        </div>
                        <div class="col-xs-5 text-right">
                            <h2 class="font-bold" id="c_avg"><small style="color:inherit;">' . gmdate('H:i:s',$wait_avg ). ' </small></h2>
                        </div>
                    </div>
                </div>';
            
            $total_div = '<div class="widget ' . $total_bg . ' p-lg text-center">
					<div class="m-b-md" style="color:white;">
						<i class="fa fa-user fa-4x"></i>
						<h1 class="m-xs" >' . $count_que . '</h1>
						<h3 class="font-bold no-margins">
							Current queue
						</h3>
						<small><i class="fa fa-clock-o"></i> '.gmdate('H:i:s',$total_wait).'</small>
					</div>
				</div>';
            
            $location_row = $conn->getRow("SELECT * FROM sensor_sites WHERE site_id = ?i", $this->site_nr);
            $location_div = '<tr><th>Location</th><td>' . $location_row['site_location'] . '</tr></td>
				<tr><th>Address</th><td>' . $location_row['site_address'] . '</tr></td>
				<tr><th>Zipcode</th><td>' . $location_row['site_zipcode'] . '</tr></td>
				<tr><th>City</th><td>' . $location_row['site_city'] . '</tr></td>';
            
            $response_array = array(
                'status' 	=> 1,
                'c_all' 	=> $count_div,
                'c_total' 	=> $total_div,
                'location' 	=> $location_div
            );
		}
        catch (Exception $e) {
            return $response_array['status'] = 0;
        }
        // Return response_array
        return $response_array;
        
    }
    
    public function getSiteSignalLoad($primair_conn = array())
    {
        
        $opt_prim = array_merge($this->defaults, $primair_conn);
        try {
            $conn_scs  = new SafeMySQL($opt_prim);
            $connected = true;
        }
        catch (Exception $e) {
            $connected = false;
        }
        
        if ($connected) {
            try {
				$group = $conn_scs->getOne("SELECT `Group` FROM sensor_sites_group WHERE site_id = ?i", $this->site_nr);
				
                $query = "SELECT `datetime` AS signalDate, `bw`,`fw` ,`count` FROM sensor_data  WHERE `datetime` BETWEEN ?s AND ?s ?p GROUP BY MID(signalDate, 7, 8) ORDER BY signalDate";
				
                if (!empty($this->site_nr)) {
                    $qpart = $conn_scs->parse(" AND `ip_address` IN (SELECT `ip_address` FROM sensor_info WHERE `Group` = ?s)", $group);
                }
                
                $res = $conn_scs->query($query, $this->date_past_day, $this->date_now_day, $qpart);
                
                $signal = array(
                    'signal'
                );
                $fw     = array(
                    'fw'
                );
                $bw     = array(
                    'bw'
                );
                $queue  = array(
                    'queue'
                );
                $hours  = array(
                    'x'
                );
                $t      = array();
                
                while ($row = $conn_scs->fetch($res)) {
                    $total = $row['count'] + $row['bw'] + $row['fw'];
                    
                    $fw[]    = $row['fw'];
                    $bw[]    = $row['bw'];
                    $queue[] = $row['count'];
                    
                    $signal[] = $total;
                    $t[]      = $total;
                    $hours[]  = date('H:i', strtotime($row['signalDate']));
                }
                ;
                //array_pop($signal);
                //array_pop($hours);
            
				$c_hours = count($hours) - 1;
				
				$hour = array();
				for ($i = 0; $i <= $c_hours; $i++) {
					$hour[] = $i;
				}
				
				//$hour = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25);
				$trendarray = $this->trendLineAnalyse($hour, $t);
				
				$trend = array(
					'trend'
				);
				foreach ($hour as $item) {
					$number  = ($trendarray['slope'] * $item) + $trendarray['intercept'];
					$number  = ($number <= 0) ? 0 : $number;
					$trend[] = round($number);
				}
            
            }
            catch (Exception $e) {
                return $response_array['status'] = 0;
            }
            
            $response_array = array(
                'status' => 1,
                'signal' => $signal,
                'trend' => $trend,
                'fw' => $fw,
                'bw' => $bw,
                'queue' => $queue,
                'hours' => $hours
            );
        } else {
            $response_array['status'] = 0;
        }
        // Return response_array
        return $response_array;
        
    }
    
    public function getCountsTotal($group_name)
    {
        $conn = $this->db_conn;
        try {
			$query = "SELECT `group`, SUM(`fw`) AS forward, SUM(`bw`) AS backward,SUM(`count`) AS queue FROM sensor_info_values WHERE `ip_address` IN (SELECT `ip_address` FROM sensor_info WHERE `group` = ?s)";
			
			$q = 0;
			$f = 0;
			$b = 0;  
			
			if($count_val = $conn->query($query, $group_name)){
				while ($row = $conn->fetch($count_val)) {
					$q += $row['queue'];
					$f += $row['forward'];
					$b += $row['backward'];
				}			
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
    
    protected function trendLineAnalyse($x, $y)
    {
        
        $n     = count($x); // number of items in the array
        $x_sum = array_sum($x); // sum of all X values
        $y_sum = array_sum($y); // sum of all Y values
        
        $xx_sum = 0;
        $xy_sum = 0;
        
        for ($i = 0; $i < $n; $i++) {
            @$xy_sum += ($x[$i] * $y[$i]);
            @$xx_sum += ($x[$i] * $x[$i]);
        }
        
        // Slope
        @$slope = (($n * $xy_sum) - ($x_sum * $y_sum)) / (($n * $xx_sum) - ($x_sum * $x_sum));
        
        // calculate intercept
        @$intercept = ($y_sum - ($slope * $x_sum)) / $n;
        
        return array(
            'slope' => $slope,
            'intercept' => $intercept
        );
    }
    
}