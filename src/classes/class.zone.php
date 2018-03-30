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
    
    public function getZoneDetails($zone_name)
    {
        $conn  = $this->db_conn;
        $group = $conn->getRow("SELECT `Name`, `Group`, GROUP_CONCAT(DISTINCT `Name` SEPARATOR ';') AS devices FROM sensor_events WHERE `Group` = ?s GROUP BY `Group`", $zone_name);
        
        $devices_row       = array();
        $devices_wait_time = array();
        if ($group) {
            $devices_arr = explode(';', $group['devices']);
            
            $i = 0;
            foreach ($devices_arr as $device) {
                $get_count = $this->getCountsTotal($device, $group['Group']);
                
                $count_current = $get_count['queue'];
                $count_out     = $get_count['forward'];
                //$count_out		= $this->getLabelCount($device, $group['Group'], 'fw');
                //$count_out 		= $this->getLabelCount($device, $group['Group'], 'bw');
                
                $count_total       = $count_current + $count_out;
                $avg_wait_time_min = ($count_out == 0) ? 0 : $count_current / $count_out; // Per minute
                $avg_wait_time_sec = $avg_wait_time_min * 60; // Per seconds
                
                $zone_row = $conn->getRow("SELECT * FROM sensor_status WHERE `Name` = ?s ORDER BY `Datetime` DESC LIMIT 1", $device);
                
                $count_current = ($count_current != 0) ? $count_current : '';
                $total_bg      = ($count_current > C_MIN_DANGER) ? 'red-bg' : (($count_current > C_MIN_WARNING) ? 'yellow-bg' : 'dark-bg');
                
                @$wait_time = ($count_out == 0 && $count_current == 0) ? '' : '<i class="fa fa-clock-o"></i> ' . $count_current / $count_out . ' min';
                
                if ($zone_row['Status_request'] != 'OK') {
                    $active = '<i class="fa fa-circle text-danger"></i>';
                } else {
                    $active = '<i class="fa fa-circle text-navy"></i>';
                    ;
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
												<th>' . $active . ' ' . $device . '</th>
											</tr>
										</thead>
										<tbody >' . $location . '</tbody>
									</table>
								</div>
							</div>			
						</div>		
						<div class="col-lg-3">
							<div class="widget ' . $total_bg . ' p-lg text-center">
								<div class="m-b-md" style="color:white;">
									<i class="fa fa-user fa-4x"></i>
									<h1 class="m-xs" >' . $count_current . '</h1>
									<h3 class="font-bold no-margins">
										Current queue
									</h3>
									<small>' . $wait_time . '</small>
								</div>
							</div>
						</div>
						<div class="col-lg-3">
							<div class="widget style1 dark-bg">
								<div class="row vertical-align">
									<div class="col-xs-8">
										<h2><i class="fa fa-users"></i> <small style="color:inherit;">Max in queue</small></h2>
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
							<div class="widget style1 dark-bg">
								<div class="row vertical-align">
									<div class="col-xs-8">
										<h2><i class="fa fa-clock-o"></i> <small style="color:inherit;">Avg wait</small></h2>
									</div>
									<div class="col-xs-4 text-right">
										<h2 class="font-bold" >' . round($avg_wait_time_min) . '</h2>
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
                
                $devices_wait_time[] = $avg_wait_time_min;
                $i++;
            }
        } else {
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
    
    
    
    protected function getCountsTotal($device_name, $group_name)
    {
        $conn = $this->db_conn;
        
        $query = "SELECT ip_address, `name`, `group`, SUM(`fw`) AS forward, SUM(`bw`) AS backward,SUM(`count`) AS queue FROM sensor_info_values  WHERE `Name` = ?s AND `Group` = ?s";
        
        $device    = array();
        $count_val = $conn->query($query, $device_name, $group_name);
        
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
}
