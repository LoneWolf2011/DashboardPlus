<?php

class Devices
{
    protected $succesMessage;
    
    function __construct($db_conn)
    {
        $this->db_conn   = $db_conn;
        $this->auth_user = htmlentities($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');
    }
    	
    public function updateDevices($post_val)
    {

        $conn = $this->db_conn;
        
        
        $query_data = array(
            'group_name' => $post_val['edit_site_name'],
        );
        
        if ($conn->query("UPDATE site_group SET ?u WHERE group_id = ?s", $query_data, $post_val['site_id'])) {
            
            // Log to file
            $msg     = "Groep geupdatet naar " . $post_val['edit_site_name'] . " door " . $this->auth_user;
            $err_lvl = 0;
            
            $response_array['type']  = 'success';
            $response_array['title'] = 'Success';
            $response_array['body']  = 'Groep <b>' . $post_val['edit_site_name'] . '</b> geupdatet';
            
        } else {
            $msg                     = "Groep niet geupdatet";
            $err_lvl                 = 2;
            $response_array['type']  = 'error';
            $response_array['title'] = 'ERROR';
            $response_array['body']  = 'Groep <b>' . $post_val['edit_site_name'] . '</b> niet geupdatet';
            
        }
        
        logToFile(__FILE__, $err_lvl, $msg);
        
        
        // Return JSON array
        jsonArr($response_array);
    }
    
    public function newDevices($post_val)
    {

        $conn = $this->db_conn;
        $msg = '';
		$query_data = array();
		
        $data = array(
            'ipAddress' 	=> $post_val['device_ip'],
            'ipPort' 		=> $post_val['device_ip_port'],
            'macAddress' 	=> str_replace(':', '',$post_val['device_mac'])
        );
        
		$add_new_device = getApiCall('http://'.WEB_API.'/api/devices', 'POST', $data);
		
		// Check if there are no errors, else show exceptionMessage to user
		if (!array_key_exists('exceptionMessage', $add_new_device)) {
			// Get new id from api result
			$new_id = $add_new_device['id'];
			
			// If a location is selected add connection to location device table
			if($post_val['select_site'] != 0){
				$query_data['location_id'] = $post_val['select_site'];
				$query_data['device_id'] = $new_id;
				
				if($conn->query("INSERT site_location_device SET ?u", $query_data)){
					$continue = true;
				} else {
					$continue = false;
				}
			}
			$continue = true;
		} else {
			$msg = $add_new_device['exceptionMessage'];
		}
		
		// If all went ok return success msg, else return exceptionMessage
        if ($continue) {
            
            // Log to file
            $msg     = "Nieuw device " . $post_val['device_mac'] . " toegevoegd door " . $this->auth_user;
            $err_lvl = 0;
            
            $response_array['type']  = 'success';
            $response_array['title'] = 'Success';
            $response_array['body']  = "Nieuw device toegevoegd";
            
        } else {

            $msg                     = "Nieuw device " . $post_val['device_mac'] . " niet toegevoegd";
            $err_lvl                 = 2;
            $response_array['type']  = 'error';
            $response_array['title'] = 'ERROR';
            $response_array['body']  = $msg;
            
        }
        
        logToFile(__FILE__, $err_lvl, $msg);
        
        // Return JSON array
        jsonArr($response_array);
    }
    
    public function deleteDevices($post_val)
    {
		
        $data = array(
            'deviceId' 	=> (int)$post_val['site_id']
        );
        
		$delete_device = getApiCall('http://'.WEB_API.'/api/devices/'.$data['deviceId'], 'DELETE', $data);		
        
		if ($delete_device == null) {
			$conn = $this->db_conn;
			
			$body = '<b>Device ' . $data['deviceId'] . '</b> verwijderd';
			
			if ($conn->query("DELETE FROM site_location_device WHERE device_id = ?i", $data['deviceId'])) {
				$body .= '<br> Device losgekoppeld van locatie';				
			} 
			
			// Log to file
			$msg     = "Device ".$data['deviceId'] . " verwijderd door " . $this->auth_user;
			$err_lvl = 0;
			
			$response_array['type']  = 'success';
			$response_array['title'] = 'Success';
			$response_array['body']  = $body;
			
        } else {
			$msg                     = 'Device ' . $data['deviceId'] . ' NIET verwijderd';
			$err_lvl                 = 2;
			$response_array['type']  = 'error';
			$response_array['title'] = 'ERROR';
			$response_array['body']  = 'Device <b>' . $data['deviceId'] . '</b> NIET verwijderd';
		}
		
		logToFile(__FILE__, $err_lvl, $msg);
        jsonArr($response_array);
    }
    
	public function getTableDevices()
    {
		$devices = getApiCall('http://'.WEB_API.'/api/devices', 'GET');
		
		$data['data'] = array();
		
		if($devices){
			foreach($devices['items'] as $device){
				if($device['isAvailable'] == true){
					$active = '<i class="fa fa-circle text-navy"></i>';
				} else {
					$active = '<i class="fa fa-circle text-danger"></i>';
				}
				
				$link = '<a href="'.URL_ROOT.'/device/?'.$device['id'].'" class="link"># '.$device['id'].'</a>';
				
				$get_location_name = $this->db_conn->getOne("SELECT `location_name` FROM site_location WHERE `location_id` IN (SELECT `location_id` FROM site_location_device WHERE `device_id` = ?i)", $device['id']);
				$location = ($get_location_name != false) ? '<span class="badge badge-success">' . $get_location_name . '</span>' : '';
				
				$mac = rtrim(chunk_split($device['macAddress'], 2, ':'),':');

				$data['data'][] = array(
					$link,
					$device['ipAddress'],
					$mac,
					$device['deviceTypeName'],
					$location,
					$active,
					convertTimeZone($device['lastSignal']),
					"<a class='label label-danger' id='delete' value='" . $device['id'] . "' rel='" . $device['macAddress'] . "' >Delete</a>"
				);			
			}
					
			return $data;
		} else {
			return $data;
		}
    }
 
    public function addDevicesToLocation($post_val)
    {

        $conn = $this->db_conn;
        
        $zones = implode(',', $post_val['add_zones']);
        
        $query_data = array(
            'location_id' => $post_val['select_site']
        );
        foreach ($post_val['add_zones'] as $zone) {
            if ($conn->getOne("SELECT `device_id` FROM site_location_device WHERE `device_id` = ?s", $zone)) {
				
                $conn->query("UPDATE site_location_device SET ?u WHERE `device_id` = ?s", $query_data, $zone);
                
                $msg     = "User: " . $zone . " group gewijzigd naar: " . $post_val['select_site'] . "  door: " . $this->auth_user;
                $err_lvl = 0;
                
                $response_array['type']  = 'success';
                $response_array['title'] = 'Success';
                $response_array['body']  = 'Locatie voor device <b>' . $zones . '</b> gewijzigd';
            } else {
				
                $query_data['device_id'] = $zone;
				
                $conn->query("INSERT site_location_device SET ?u", $query_data);
                
                $msg     = "User: " . $zone . " toegevoegd aan locatie " . $post_val['select_site'] . " door: " . $this->auth_user;
                $err_lvl = 0;
                
                $response_array['type']  = 'success';
                $response_array['title'] = 'Success';
                $response_array['body']  = 'Device(s) toegevoegd aan <b>locatie' . $post_val['select_site'] . '</b>';
            }
            logToFile(__FILE__, $err_lvl, $msg);
        }
        
        
        
        
        jsonArr($response_array);
    }	
	
    public function getLocationSelect()
    {

        $conn = $this->db_conn;
        
        $result_site = $conn->query("SELECT `location_id`, `location_name` FROM site_location");
        
        $option = array();
        while ($site_row = $conn->fetch($result_site)) {
            $option[$site_row['location_id']] = $site_row['location_name'];
        }
        ;
        
        if ($result_site) {
            $response_array = array(
                'status' => 1,
                'get_sites' => $option
            );
        } else {
            $response_array['status'] = 0;
        }
        
        jsonArr($response_array);
    }
    
    public function getDevicesSelect()
    {
		
        $conn = $this->db_conn;
        
        $devices = getApiCall('http://'.WEB_API.'/api/devices', 'GET');;
        
        $option = array();
		if ($devices) {
			foreach($devices['items'] as $device){
				$site = $conn->getOne("SELECT `location_id` FROM site_location_device WHERE `device_id` = ?i", $device['id']);
				if ($site) {
					//$site_name = 'in group: ' . $conn->getOne("SELECT `group_name` FROM site_group WHERE `group_id` = ?i", $site);
					$site_name = '';
					$in_site = 1;
				} else {
					$site_name = 'NEW';
					$in_site = 0;
				}
				$option[$device['id']] = array( 
					'in_site' => $in_site,
					'text' => 'Device ID: ' . $device['id'] . ', ' . rtrim(chunk_split($device['macAddress'], 2, ':'),':') . ' ' . $site_name
				);
			}
        
        
            $response_array = array(
                'status' => 1,
                'get_zones' => $option
            );
        } else {
            $response_array['status'] = 0;
        }
        
        jsonArr($response_array);
    }
    
}