<?php

class Device
{
    protected $succesMessage;
    
    function __construct($db_conn, $device_id)
    {
        $this->db_conn   = $db_conn;
        $this->auth_user = htmlentities($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');
        $this->device_id = $device_id;
    }
    	
	public function getDeviceLocation()
	{
		$conn = $this->db_conn;
				
		$device = getApiCall('http://'.WEB_API.'/api/devices/'.$this->device_id, 'GET');
		
		if($device['isAvailable'] == true){
			$conn_status 	= '<span class="label label-primary">'.LANG['connection']['conn'].'</span>';
			$path_status 	= 1;
		} else {
			$conn_status 	= '<span class="label label-danger">'.LANG['connection']['diss'].'</span>';
			$path_status 	= 0;
		}
		
		$result = $conn->query("SELECT * FROM site_location WHERE location_id IN (SELECT location_id FROM site_location_device WHERE device_id = ?i)", $this->device_id);
			
		while ($line = $conn->fetch($result)) {			
			$row = $line;
		};		
		
		$devices = getApiCall('http://'.WEB_API.'/api/devices/'.$this->device_id.'/signals', 'GET');
		
		$is_letter = strtoupper(substr($row['location_name'],0,1));	
		
		$response_array = array(
			'location_name' 	=> $row['location_name'],
			'location_address' 	=> $row['location_address'],
			'location_zip' 		=> $row['location_zipcode'],
			'location_city' 	=> $row['location_city'],
			'location_first' 	=> convertTimeZone($device['firstSignal']),
			'location_last'		=> convertTimeZone($device['lastSignal']),
			'location_mac'		=> $device['macAddress'],
			'location_udid'		=> $device['ipAddress'],
			'location_lijn'		=> $device['ipPort'],
			'location_serie'	=> $device['deviceTypeName'],
			'events_count_week'	=> @count($devices['items']),
			'conn_status' 		=> $conn_status,
			'lat' 				=> $row['location_latitude']+0.000001,
			'lng' 				=> $row['location_longitude']+0.000001,
			'first_char' 		=> $is_letter,
			'path_status' 		=> $path_status, 
			'info' 				=> '<div><b>'.$row['location_name'].'</b><br>'.$row['location_address'].'<br>'.$row['location_zipcode'].'<br>'.$row['location_city'].'</div>' 
		);

		return $response_array;	
	}
	
	public function getDeviceActions()
	{
		$device_actions = getApiCall('http://'.WEB_API.'/api/devices/'.$this->device_id.'/actions', 'GET');
		
		$response_array = array();
		
		if($device_actions){
			
			foreach($device_actions as $action){
				$response_array['actions'][] = '<div "row"><button type="button" class="btn btn-accent block full-width m-b" value="'.$action['name'].'" onClick="sendAction(this.value);">'.$action['name'].'</button>';
			}
			
			return $response_array;
		
		} else {
			return $response_array;
		}
	}
	
	public function exeDeviceAction($post_val)
	{
		$data = array(
			'deviceId' => 1,
			'actionName' => $post_val['execute']
		);			
		$execute_action = getApiCall('http://'.WEB_API.'/api/devices/'.$this->device_id.'/actions/'.$post_val['execute'], 'POST', $data);
		
		$response_array = array();
		
		if($execute_action['exceptionMessage']){
			$response_array['type'] 	= 'error';
			$response_array['title'] 	= 'ERROR';
			$response_array['msg'] 		= $execute_action['exceptionMessage'];
		} else {
			$response_array['type'] 	= 'success';
			$response_array['title'] 	= 'SUCCESS';
			$response_array['msg'] 		= $post_val['execute']. ' uitgevoerd';			
		}
		
		return $response_array;
	}
	
	public function getDeviceStatus()
	{
		$device_status = getApiCall('http://'.WEB_API.'/api/devices/'.$this->device_id.'/status', 'GET');
		
		$data['status'] = array();
		
		if($device_status){
			
			foreach($device_status['parameters'] as $device){
				// TODO: $device['value'] need to change to $device['oidvalue'] so 0 or 1 can be used to validate
				if($device['value'] == 'Ok'){
					$status = '<i class="fa fa-circle text-navy"></i>';
				} else {
					$status = $device['value'];
				}
				
				
				$data['status'][] = array(
					$device['name'],
					$status
				);			
			}
				
			return $data;	
		} else {
			return $data;
		}		
	}
	
	public function getTableDevice()
    {
		$devices = getApiCall('http://'.WEB_API.'/api/devices/'.$this->device_id.'/signals', 'GET');
		$data['data'] = array();
		
		if($devices){
				
			foreach($devices['items'] as $device){
	
				$data['data'][] = array(
					$device['code'],
					$device['zone'],				
					$device['statusName'],
					$device['statusValue'],
					$device['text'],
					convertTimeZone($device['dateTime'])
				);			
			}
				
			return $data;
		
		} else {
			return $data;
		}

    }	

}