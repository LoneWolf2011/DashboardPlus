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

	private function getDeviceCriticalityArr($device_id)
	{
		//var_dump($device_id);
		$device = getApiCall('http://'.WEB_API.'/api/devices/'.$device_id, 'GET');
		$data = array();
		if($device['isAvailable'] == true)
		{
			$device_status = getApiCall('http://'.WEB_API.'/api/devices/'.$device_id.'/status', 'GET');
			
			
							
			foreach($device_status['parameters'] as $device){
				$data[] = 	$device['criticality'];
			}
				
			return $data;	
	
		}
		else 
		{
			return $data;
		}
		
	}
	
	public function getDeviceLocation()
	{
		$conn = $this->db_conn;
				
		$device = getApiCall('http://'.WEB_API.'/api/devices/'.$this->device_id, 'GET');
		$crit_arr = $this->getDeviceCriticalityArr($this->device_id);
		
		if($device['isAvailable'] != true){
			$conn_status 	= '<span class="label label-danger">'.LANG['connection']['diss'].'</span>';
			$path_status 	= 0;	
		} elseif(in_array_any(array(2,3), $crit_arr)){
			$conn_status 	= '<span class="label label-warning">'.LANG['connection']['back'].'</span>';
			$path_status 	= 2;				
		} else {
			$conn_status 	= '<span class="label label-primary">'.LANG['connection']['conn'].'</span>';
			$path_status 	= 1;
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
			$response_array['title'] 	= 'SUCCES';
			$response_array['msg'] 		= $post_val['execute']. ' uitgevoerd';			
		}
		
		return $response_array;
	}
	
	public function getDeviceStatus()
	{
		$device_status = getApiCall('http://'.WEB_API.'/api/devices/'.$this->device_id.'/status', 'GET');
		
		$data['status'] = array();
		
		if($device_status){
			
			// Device status names to be excluded from list
			$name_arr = array(
				'FluidLow',
				'FluidEmpty',
				'TemperatureLow',
				'PowerLow',
				'BatteryFault',
				'ACFault'
			);
			
			foreach($device_status['parameters'] as $device){
				// Criticality defines status
				// 1 = active
				// 2 = warning
				// 3 = error
				if($device['criticality'] == 3 && !is_numeric($device['value'])){
					$status = '<i class="fa fa-circle text-danger"></i>' . ' ' . $device['value'];
				} elseif($device['criticality'] == 2 && !is_numeric($device['value'])){
					$status = '<i class="fa fa-circle text-warning"></i>' . ' ' . $device['value'];
				} elseif($device['criticality'] == 1 && !is_numeric($device['value'])){
					$status = '<i class="fa fa-circle text-navy"></i>' . ' ' . $device['value'];
				} else {
					$status = $device['value'];
				}
				
				if(in_array($device['name'], $name_arr))
				{
					// If criticality is not ok show excluded status name
					if($device['criticality'] != 1 )
					{
						$status = $status;
					}
					else 
					{
						continue;						
					}

				}
				
				if($device['name'] == 'Temperature')
				{
					$status = $status.' &#8451;';
				}
				
				if($device['name'] == 'Fluidlevel')
				{
					$percent = round($device['value'] / 10);
					if($percent <= 25)
					{
						$color = 'bg-danger';
					} 
					elseif($percent > 25 && $percent <= 50)
					{
						$color = 'bg-warning';
					}
					else
					{
						$color = '';
					}
					// NOTE: Max fluid lvl can be 500 or 1000 depending on config
					$status = ' <div class="progress password-progress" style="margin-bottom: 0; background-color:#333;">
									<div class="progress-bar '.$color.'" role="progressbar" style="width: '. $percent .'%;" aria-valuenow="'.$device['value'].'" aria-valuemin="0" aria-valuemax="1000">'. $status .'ml</div>
								</div>';
				}
				
				$data['status'][] = array(
					$device['name'],
					$status,
					$device['criticality']
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
					convertTimeZone($device['dateTime'])
				);			
			}
				
			return $data;
		
		} else {
			return $data;
		}

    }	

}