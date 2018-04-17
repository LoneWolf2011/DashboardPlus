<?php

class Devices
{
    protected $succesMessage;
    
    function __construct($db_conn)
    {
        $this->db_conn   = $db_conn;
        $this->locale    = json_decode(file_get_contents(URL_ROOT . '/Src/lang/' . APP_LANG . '.json'), true);
        $this->auth_user = htmlentities($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');
    }
    	
    public function updateDevices($post_val)
    {
        $lang = $this->locale;
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
        $lang = $this->locale;
        $conn = $this->db_conn;
        
        $query_data = array(
            'group_name' => ucfirst($post_val['new_site_name'])
        );
        
        if ($conn->query("INSERT INTO site_group SET ?u", $query_data)) {
            
            // Log to file
            $msg     = "Nieuwe groep " . $post_val['new_site_name'] . " aangemaakt door " . $this->auth_user;
            $err_lvl = 0;
            
            $response_array['type']  = 'success';
            $response_array['title'] = 'Success';
            $response_array['body']  = "Nieuwe groep <b>" . $post_val['new_site_name'] . "</b> aangemaakt";
            
        } else {
            $msg                     = "Nieuwe groep " . $post_val['new_site_name'] . " niet aangemaakt ";
            $err_lvl                 = 2;
            $response_array['type']  = 'error';
            $response_array['title'] = 'ERROR';
            $response_array['body']  = 'groep niet aangemaakt';
            
        }
        
        logToFile(__FILE__, $err_lvl, $msg);
        
        
        // Return JSON array
        jsonArr($response_array);
    }
    
    public function deleteDevices($post_val)
    {
        $lang = $this->locale;
        
        $conn = $this->db_conn;
		$get_location_count = $conn->getOne("SELECT COUNT(*) FROM site_group_location WHERE `group_id` = ?i", $post_val['site_id']);
        if ($get_location_count) {
            $response_array['type']  = 'warning';
            $response_array['title'] = 'Let op';
            $response_array['body']  = 'Groep kan niet verwijderd worden<br> omdat er <b>'.$get_location_count.'</b> locatie(s) aan gekoppeld zitten';
        } else {
            $site_name = $conn->getOne("SELECT group_name FROM site_group WHERE group_id = ?i", $post_val['site_id']);
            
            if ($conn->query("DELETE FROM site_group WHERE group_id = ?i", $post_val['site_id'])) {
				
				$number = $conn->getOne("SELECT MAX( `group_id` ) FROM site_group");
				$conn->query("ALTER TABLE site_group AUTO_INCREMENT = ?i", $number +1);
                // Log to file
                $msg     = $site_name . " verwijderd door " . $this->auth_user;
                $err_lvl = 0;
                
                $response_array['type']  = 'success';
                $response_array['title'] = 'Success';
                $response_array['body']  = '<b>' . $site_name . '</b> verwijderd';
                
            } else {
                $msg                     = "Locatie niet verwijderd";
                $err_lvl                 = 2;
                $response_array['type']  = 'error';
                $response_array['title'] = 'ERROR';
                $response_array['body']  = 'Locatie niet verwijderd';
            }
            logToFile(__FILE__, $err_lvl, $msg);
        }
        
        jsonArr($response_array);
    }
    
	public function getTableDevices()
    {
		$devices = getApiCall('http://'.WEB_API.'/api/devices', 'GET');
		
		$data['data'] = array();
		
		foreach($devices['items'] as $device){
			if($device['isAvailable'] == true){
				$active = '<i class="fa fa-circle text-navy"></i>';
			} else {
                $active = '<i class="fa fa-circle text-danger"></i>';
            }
			
			$link = '<a href="'.URL_ROOT.'/view/device/?'.$device['id'].'" class="link"># '.$device['id'].'</a>';
			
			$get_location_name = $this->db_conn->getOne("SELECT `location_name` FROM site_location WHERE `location_id` IN (SELECT `location_id` FROM site_location_device WHERE `device_id` = ?i)", $device['id']);
			$location = ($get_location_name != false) ? $get_location_name : '';
			
			$data['data'][] = array(
				$link,
				$device['ipAddress'],
				$device['macAddress'],
				$device['deviceTypeName'],
                $location,
				$active,
				convertTimeZone($device['lastSignal'])
			);			
		}
				
		return $data;

    }
 
    public function addDevicesToLocation($post_val)
    {
        $lang = $this->locale;
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
        $lang = $this->locale;
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
        $lang = $this->locale;
        $conn = $this->db_conn;
        
        $devices = getApiCall('http://'.WEB_API.'/api/devices', 'GET');;
        
        $option = array();
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
				'text' => 'Device ID: ' . $device['id'] . ', ' . $device['macAddress'] . ' ' . $site_name
			);
        }
        
        if ($devices) {
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