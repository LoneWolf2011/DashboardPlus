<?php

class Home
{
    protected $succesMessage;
    
    function __construct($db_conn)
    {
        $this->db_conn = $db_conn;
        $this->locale  = json_decode(file_get_contents(URL_ROOT . '/Src/lang/' . APP_LANG . '.json'), true);
        
    }
    
    public function refreshSitesTable($last_id)
    {
        $conn = $this->db_conn;
        try {
			if (!empty($last_id)) {
				
				//$res = $conn->query("SELECT * FROM sensor_events WHERE `id` > ?i AND `Label` NOT IN ('bw') ORDER BY `id` ASC", (int)$last_id);
				$res = $conn->query("SELECT * FROM sensor_data WHERE `id` > ?i ORDER BY `id` ASC", (int)$last_id);
				
				$table_row = '';
				while ($row = $conn->fetch($res)) {
					
					$count = ($row['count'] > 0) ? '<i class="fa fa-user"></i> ' . $row['count'] : '';
					$table_row .= '<tr><td>' . $row['ip_address'] . '</td><td>' . $row['sensor_type'] . '</td><td>' . $row['datetime'] . '</td><td><i class="fa fa-sign-out fa-flip-horizontal text-danger"></i> ' . $row['fw'] . '</td><td>' . $count . '</td></tr>';
					$last_id = $row['id'];
					
				}
				$response_array = array(
					'status' => 1,
					'rows' => $table_row,
					'last_id' => $last_id,
					'row_count' => $conn->numRows($res)
				);
			} else {
				$response_array['status'] = 0;
			}
		}
		catch (Exception $e) {
			return $response_array['status'] = 0;
		}       
        return $response_array;
    }
    
    public function getSitesTable()
    {
        $conn = $this->db_conn;
        try {
			//$res = $conn->query("SELECT * FROM (SELECT * FROM sensor_events WHERE `Label` NOT IN ('bw') ORDER BY `id` DESC LIMIT 20) tmp ORDER BY tmp.id ASC");
			$res       = $conn->query("SELECT * FROM (SELECT * FROM sensor_data ORDER BY `id` DESC LIMIT 20) tmp ORDER BY tmp.id ASC");
			$table_row = '';
			while ($row = $conn->fetch($res)) {
				
				$count = ($row['count'] > 0) ? '<i class="fa fa-user"></i> ' . $row['count'] : '';
				$table_row .= '<tr><td>' . $row['ip_address'] . '</td><td>' . $row['sensor_type'] . '</td><td>' . $row['datetime'] . '</td><td><i class="fa fa-sign-out fa-flip-horizontal text-danger"></i> ' . $row['fw'] . '</td><td>' . $count . '</td></tr>';
				$last_id = $row['id'];
			}
			
			$response_array = array(
				'status' => 1,
				'rows' => $table_row,
				'last_id' => $last_id
			);
		}
		catch (Exception $e) {
			return $response_array['status'] = 0;
		}          
        return $response_array;
    }
    
    public function getSitesActivity()
    {
        $conn = $this->db_conn;
        
		try {
			$res = $conn->query("SELECT `site_id`, `site_name` FROM sensor_sites");
			
			
			$div = '';
			while ($row = $conn->fetch($res)) {
				// TODO: Check if the site contains active zones
	
					
				$site_obj = new Site($conn, $row['site_id']); 
				
				$div .= '<div class="row">';
				
				if($conn->getOne("SELECT `group` FROM sensor_sites_group WHERE `site_id` = ?i", $row['site_id'])){
					$div .= '<div class="col-xs-12"><h4 class="m-t-n-sm m-b-xs">Site name: <a  class="link;" href="' . URL_ROOT . '/view/site/?site=' . $row['site_id'] . '">' . $row['site_name'] . '</a></h4></div>';				
				}
			
				$zones     = $site_obj->getSiteZones();
				$count_que = 0;
				$count_out = 0;
	
				foreach ($zones as $zone => $val) {
					$get_count = $site_obj->getCountsTotal($val['site_group']);
					$total_wait = $val['zone_wait'];
					$count_que = $get_count['queue'];
					$count_out = $get_count['forward'];
					
					$count_que = ($count_que > 0) ? '<small>In queue:</small> '.$count_que  : 0;
					
					$div .= '<div class="col-xs-6">
									<div class="panel panel-filled">
										<div class="panel-body">
											<h3 class="m-b-none pull-right"><i class="fa fa-clock-o"></i> ' . gmdate('H:i:s',$total_wait) . '<small> wait time</small></h3>
											<h2 class="m-b-none">' . $count_que . '</h2>
											<div class="small">
												Zone: <a  style="color:#f6a821;" href="' . URL_ROOT . '/view/zone/?site=' . $row['site_id'] . '&id=' . $val['site_group'] . '">#'. $val['id'] . ' '. $val['site_group'] . '</a>
											</div>
											<div class="small slight m-t-sm">
												<i class="fa fa-clock-o"></i> Updated: <span class="c-white time">' . date('H:i:s') . '</span>
											</div>
										</div>
									</div>
								</div>';				
				}			
				
	
				$div .= '</div>';
				
			}
		}
		catch (Exception $e) {
			return $response_array['status'] = 0;
		} 
		
		$response_array = array(
			'status' => 1,
			'sites' => $div			
		);
        
        return $response_array;
        
    }
    
    protected function getLabelIcon($label)
    {
        if ($label == 'bw') {
            $label = '<i class="fa fa-sign-out "></i>';
        } elseif ($label == 'fw') {
            $label = '<i class="fa fa-sign-out fa-flip-horizontal text-danger"></i>';
        } else {
            $label = '<i class="fa fa-user"></i>';
        }
        
        return $label;
    }
    
    protected function getLabelCount($device_name, $group_name, $label = null)
    {
        $conn = $this->db_conn;
        try{
			$query = "SELECT `Value` FROM sensor_events WHERE `Name` = ?s AND `Group` = ?s ?p ORDER BY id DESC LIMIT 1";
			
			$qpart = '';
			if ($label != null) {
				$qpart = $conn->parse(" AND `Label` = ?s", $label);
			}
			
			$count_val = $conn->getOne($query, $device_name, $group_name, $qpart);
        }
		catch (Exception $e) {
			return $response_array['status'] = 0;
		}  
        return $count_val;
    }
}