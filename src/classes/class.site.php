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
    
    public function getZonesTable()
    {
        $conn = $this->db_conn;
        $rows = array();
        foreach ($this->getZones() as $key => $val) {
            //$count = $conn->getOne("SELECT `Value` FROM sensor_events WHERE `Group` = ?s AND `Label` = 'count' ORDER BY `id` DESC LIMIT 1",$val['zone']);
            $count = $conn->getOne("SELECT SUM(`count`) FROM sensor_info_values WHERE `Group` = ?s", $val['zone']);
            if ($count > 0) {
                $label = '<span class="badge badge-success">' . $count . '</span>';
            } else {
                $label = '';
            }
            
            $rows[] = array(
                $val['link'],
                $val['zone'],
                implode('<br>', $val['devices']),
                $val['wait'],
                $label,
                '<span class="btn btn-primary btn-xs zone_graph" rel="' . $val['zone'] . '" value="' . $val['zone_count'] . '">graph</span>'
            );
        }
        
        $response_array = array(
            'status' => 1,
            'rows' => $rows
        );
        
        return $response_array;
    }
    
    public function getZones()
    {
        $conn = $this->db_conn;
        
        try {
            $query = "SELECT 
						GROUP_CONCAT(DISTINCT  `ip_address` SEPARATOR ';') AS devices_ip,
						GROUP_CONCAT(DISTINCT  `name` SEPARATOR ';') AS devices,
						`group`, 
						SUM(`fw`) AS forward, SUM(`bw`) AS backward,SUM(`count`) AS queue FROM sensor_info_values 
						?p";
            
            if (!empty($this->site_nr)) {
                $qpart = $conn->parse(" WHERE `Group` IN (SELECT `Group` FROM sensor_sites_group WHERE site_id = ?i)", $this->site_nr);
            }
            
            $res   = $conn->query($query, $qpart);
            $i     = 1;
            $zones = array();
            
            $count_total = 0;
            $count_out   = 0;
            
            while ($row = $conn->fetch($res)) {
				$z_count = $this->getZoneValCount($row['group']);
                
                $get_count = $this->getCountsTotal($row['group']);
                
                $count_total += $get_count['queue'];
                $count_out += $get_count['forward'];
                
                $avg_wait_time_min = ($count_out == 0) ? 0 : $count_total / $count_out; // Per minute
                $avg_wait_time_sec = $avg_wait_time_min * 60; // Per seconds
                
                $zones[] = array(
                    'id' 			=> $i,
                    'site' 			=> $conn->getOne("SELECT `site_id` FROM sensor_sites_group WHERE `Group` = ?s", $row['group']),
                    'zone' 			=> $row['group'],
                    'zone_count' 	=> $z_count['count'],
                    'wait' 			=> gmdate("H:i:s", round($avg_wait_time_sec)),
                    'devices' 		=> explode(';', $row['devices']),
                    'link' 			=> '<a  style="color:#f6a821;" href="' . URL_ROOT . '/view/zone/?site=' . $this->site_nr . '&id=' . $row['group'] . '">#' . $i . '</a>'
                );
                $i++;
            }
            
        }
        catch (Exception $e) {
            return $response_array['status'] = 0;
        }
        
        return $zones;
    }
    
    public function getPeopleCount()
    {
        $conn = $this->db_conn;
        
        $zones       = $this->getZones();
        $count_total = 0;
        $count_out   = 0;
        foreach ($zones as $zone => $val) {
            $get_count = $this->getCountsTotal($val['zone']);
            $count_total += $get_count['queue'];
            $count_out += $get_count['forward'];
        }
        
        $qpart = $conn->parse(" AND `Group` IN (SELECT `Group` FROM sensor_sites_group WHERE site_id = ?i)", $this->site_nr);
        
        $total_count = $conn->getOne("SELECT SUM(`Value`) AS maxid FROM sensor_events WHERE `Label` = 'count' AND `From` BETWEEN ?s AND ?s ?p", $this->date_past_day, $this->date_now_day, $qpart);
        $total_out   = $conn->getOne("SELECT SUM(`Value`) AS maxid FROM sensor_events WHERE `Label` = 'fw' AND `From` BETWEEN ?s AND ?s ?p", date('Y-m-d H:i:s', strtotime('-1 hour')), date('Y-m-d H:i:s', strtotime('+1 hour')), $qpart);
        
        $avg_wait_time_min = ($count_out == 0) ? 0 : $count_total / $count_out; // Per minute
        $avg_wait_time_sec = $avg_wait_time_min * 60; // Per seconds
        
        $max_bg   = ($total_count > C_MIN_DANGER) ? 'red-bg' : (($total_count > C_MIN_WARNING) ? 'yellow-bg' : 'dark-bg');
        $out_bg   = ($total_out > C_MIN_DANGER) ? 'red-bg' : (($total_out > C_MIN_WARNING) ? 'yellow-bg' : 'dark-bg');
        $total_bg = ($count_total > C_MIN_DANGER) ? 'red-bg' : (($count_total > C_MIN_WARNING) ? 'yellow-bg' : 'dark-bg');
        $avg_bg   = ($avg_wait_time_min > C_MIN_DANGER) ? 'red-bg' : (($avg_wait_time_min > C_MIN_WARNING) ? 'yellow-bg' : 'dark-bg');
        
        $t = $count_total + $count_out;
        if ($conn) {
            
            $count_div = '<div class="widget style1 ' . $max_bg . '">
                    <div class="row vertical-align">
                        <div class="col-xs-7">
                            <h2><i class="fa fa-users"></i> <small style="color:inherit;">max in queue</small></h2>
                        </div>
                        <div class="col-xs-5 text-right">
                            <h2 class="font-bold" >' . $t . '</h2>
                        </div>
                    </div>
                </div>
				<div class="widget style1 ' . $out_bg . '">
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
                            <h2 class="font-bold" id="c_avg">' . round($avg_wait_time_min) . ' <small style="color:inherit;">min</small></h2>
                        </div>
                    </div>
                </div>';
            
            //$total_bg = (array_sum($signal) > C_PER_DANGER) ? 'red-bg' : ((array_sum($signal) > C_PER_WARNING) ? 'yellow-bg' : 'dark-bg');
            
            $total_div = '<div class="widget ' . $total_bg . ' p-lg text-center">
					<div class="m-b-md" style="color:white;">
						<i class="fa fa-user fa-4x"></i>
						<h1 class="m-xs" >' . $count_total . '</h1>
						<h3 class="font-bold no-margins">
							Current queue
						</h3>
						<small>total</small>
					</div>
				</div>';
            
            $location_row = $conn->getRow("SELECT * FROM sensor_sites WHERE site_id = ?i", $this->site_nr);
            $location_div = '<tr><th>Location</th><td>' . $location_row['site_location'] . '</tr></td>
				<tr><th>Address</th><td>' . $location_row['site_address'] . '</tr></td>
				<tr><th>Zipcode</th><td>' . $location_row['site_zipcode'] . '</tr></td>
				<tr><th>City</th><td>' . $location_row['site_city'] . '</tr></td>';
            
            $response_array = array(
                'status' => 1,
                'c_all' => $count_div,
                'c_total' => $total_div,
                'location' => $location_div
            );
        } else {
            $response_array['status'] = 0;
        }
        // Return response_array
        return $response_array;
        
    }
    
    public function getSignalLoad($primair_conn = array())
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
            
            // Check if both dates are in the same week and specify query
            try {
                $query = "SELECT `datetime` AS signalDate, `bw`,`fw` ,`count` FROM sensor_data  WHERE `datetime` BETWEEN ?s AND ?s ?p GROUP BY MID(signalDate, 7, 8) ORDER BY signalDate";
                
                if (!empty($this->site_nr)) {
                    $qpart = $conn_scs->parse(" AND `Group` IN (SELECT `Group` FROM sensor_sites_group WHERE site_id = ?i)", $this->site_nr);
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
            }
            catch (Exception $e) {
                return $response_array['status'] = 0;
            }
            
            
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
    
    protected function getSignalLoadPerLabel($label, $primair_conn = array())
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
            
            // Check if both dates are in the same week and specify query
            try {
                $query = "SELECT `From` AS signalDate, SUM(`Value`) AS signal FROM sensor_events  WHERE `From` BETWEEN ?s AND ?s";
                
                if (!empty($this->site_nr)) {
                    $query .= " AND `Group` IN (SELECT `Group` FROM sensor_sites_group WHERE site_id = ?i)";
                }
                
                $query .= " AND `Label` = '" . $label . "' GROUP BY MID(signalDate, 7, 8) ORDER BY signalDate";
                
                $res = $conn_scs->query($query, $this->date_past_day, $this->date_now_day, $this->site_nr);
                
                $signal = array(
                    $label
                );
                while ($row = $conn_scs->fetch($res)) {
                    
                    $signal[] = $row['signal'];
                    
                }
                ;
                
            }
            catch (Exception $e) {
                return $response_array['status'] = 0;
            }
            
            $response_array = array(
                'status' => 1,
                'signal' => $signal
            );
        } else {
            $response_array['status'] = 0;
        }
        // Return response_array
        return $response_array['signal'];
        
    }
    
    protected function getZoneValCount($group)
    {
        $conn = $this->db_conn;
        try {
            //$query = "SELECT GROUP_CONCAT(`value` SEPARATOR ';') AS `zone_count` FROM sensor_events WHERE `Group` = ?s AND `From` BETWEEN ?s AND ?s";
            $query = "SELECT `group`,`datetime` AS signalDate, `bw`,`fw` ,`count` FROM sensor_data  WHERE `group` = ?s AND `datetime` BETWEEN ?s AND ?s ?p GROUP BY MID(signalDate, 7, 8) ORDER BY signalDate";
                
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
    
    protected function getCountsTotal($group_name)
    {
        $conn = $this->db_conn;
        
        $query = "SELECT 
						GROUP_CONCAT(DISTINCT  `ip_address` SEPARATOR ';') AS devices_ip,
						GROUP_CONCAT(DISTINCT  `name` SEPARATOR ';') AS devices,
						`group`, SUM(`fw`) AS forward, SUM(`bw`) AS backward,SUM(`count`) AS queue FROM sensor_info_values WHERE `Group` = ?s";
        
        $device    = array();
        $count_val = $conn->query($query, $group_name);
        
        $q = 0;
        $f = 0;
        $b = 0;
        while ($row = $conn->fetch($count_val)) {
            $q += $row['queue'];
            $f += $row['forward'];
            $b += $row['backward'];
        }
        return array(
            'queue' => $q,
            'forward' => $f,
            'backward' => $b
        );
    }
    
    protected function getZoneWaitTime()
    {
        $conn = $this->db_conn;
        
        $zones       = $this->getZones();
        $count_total = 0;
        $count_out   = 0;
        $get_count   = $this->getCountsTotal($val['zone']);
        foreach ($zones as $zone => $val) {
            $count_total += $get_count['queue'];
            $count_out += $get_count['forward'];
        }
        
        $avg_wait_time_min = ($count_out == 0) ? 0 : $count_total / $count_out; // Per minute
        
        $wait_time_sec = $avg_wait_time_min * 60; // seconds
        
        return gmdate("H:i:s", round($wait_time_sec));
        
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