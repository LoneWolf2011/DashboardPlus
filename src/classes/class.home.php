<?php
	
class Home 
{
	protected $succesMessage;
	
	function __construct($db_conn) 
	{
		$this->db_conn 	= $db_conn;
		$this->google	= new googleHelper(GOOGLE_API);
		$this->user_id	= htmlentities($_SESSION[SES_NAME]['user_id'], ENT_QUOTES, 'UTF-8');
	}	

	public function checkGroups()
	{
		// Check if current user is attached to any group
		if($this->db_conn->getOne("SELECT COUNT(group_id) FROM site_group_users WHERE user_id = ?i",$this->user_id) > 0){
			return array(
				'status' => true
			);	
		// If not, show msg
		} else {
			$user_is_admin = $this->db_conn->getOne("SELECT user_role FROM app_users WHERE user_id = ?i",$this->user_id);

			$msg = '<div class="middle-box text-center animated fadeInDown">
							<h1><i class="fa fa-remove"></i></h1>
							<h3 class="font-bold" data-i18n="[html]error_page.403.label">No groups</h3>
							<div class="error-desc">
							';
							
			$msg .=	'<p data-i18n="[html]error_page.403.msg">You have no locations attached to your account</p>';
			
			if($user_is_admin == 1){
				$msg .=	'<a href="" class="btn btn-primary" data-i18n="[html]error_page.return_btn">Create first location</a>';					
			}			

			$msg .=	'</div>
						</div>';
			
			return array(
				'status' => false,
				'msg' => $msg,
			);
		}
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
	
	public function getMarkers($getall = false, $updatetime='')
	{
		
		$conn = $this->db_conn;
		
		$datetime = date('YmdHis', strtotime('now'));
		$ids = $this->getUserIds();
		// Get all locations associated with the group the user is attached too
		$sql= "SELECT * FROM site_location WHERE location_id IN (?a)";
						
		$result = $conn->query($sql,$ids['get_location_ids']);

		if($result){
			while ($row = $conn->fetch($result)) {
				// Check if there are devices associated with the locations
				$get_device_id = explode(';',$this->db_conn->getOne("SELECT GROUP_CONCAT(DISTINCT `device_id` SEPARATOR ';') FROM site_location_device WHERE `location_id` = ?i", $row['location_id']));
				// If so show devices marker, else remove marker from map
				if(array_sum($get_device_id) > 0){
					// If there are multiple devices attached to same location
					if(count($get_device_id) > 1){						
						$link = '';
						$status = 0;
						
						$device_arr = array();
						foreach($get_device_id as $device_id){
							$devices = getApiCall('http://'.WEB_API.'/api/devices/'.$device_id, 'GET');
							$crit_arr = $this->getDeviceCriticalityArr($device_id);
							// Skip device if there is a deviceID in the local DB but there are no API results
							if(array_key_exists('exceptionMessage', $devices)){
								continue;
							}
							
							$device_arr[] = $device_id;
							
							// Increment status if there are errors
							// Status defines the marker color on the map
							if($devices['isAvailable'] != true){
								$err_class = 'text-danger';
								$status += 1;
							} elseif(in_array_any(array(2,3), $crit_arr)) {
								$err_class = 'text-warning';
								$status += 1;								
							} else {
								$err_class = 'text-navy';
								$status += 0;
							}
							
							if($status == count($device_arr)) {
								$path_status = 0;
							} elseif($status > 0){
								$path_status = 2;
							} else {
								$path_status = 1;
							}
							$link .= '<a class="text-info '.$err_class.'" onclick="popupWindow(\''.URL_ROOT.'/device/?'.$device_id.'\', \'location\', 1980, 1080 ); return false;">Device #'.$device_id.'</a><br>';
							$device_type = $devices['deviceTypeName'];
						}
						
					} else {
						$devices = getApiCall('http://'.WEB_API.'/api/devices/'.implode('',$get_device_id), 'GET');
						$crit_arr = $this->getDeviceCriticalityArr($devices['id']);
						
						// Skip device if there is a deviceID in the local DB but there are no API results
						if(array_key_exists('exceptionMessage', $devices)){
							continue;
						}
						if($devices['isAvailable'] != true){
							$err_class = 'text-danger';
							$path_status = 0;
						// If device status array contains a value other than 1 display problem marker
						} elseif(in_array_any(array(2,3), $crit_arr)) {
							$err_class = 'text-warning';
							$path_status = 2;					
						} else {
							$err_class = 'text-navy';
							$path_status = 1;
						}					
						$link = '<a class="text-info '.$err_class.'" onclick="popupWindow(\''.URL_ROOT.'/device/?'.$devices['id'].'\', \'location\', 1980, 1080 ); return false;">Device #'.$devices['id'].'</a>';
						$device_type = $devices['deviceTypeName'];
					}
					
					$is_letter = strtoupper(substr($row['location_name'],0,1));	
					// Markers array
					$locs[$row['location_name']] = array( 
						'info' 			=> '<div><b>'.$row['location_name'].'</b><br>'.$row['location_address'].'<br>'.$link.'<br></div>', 
						'path_status' 	=> $path_status, 
						'first_char' 	=> $is_letter, 
						'lat' 			=> $row['location_latitude'], 
						'lng' 			=> $row['location_longitude'],
						'category' 		=> $device_type,
						'id' 			=> $row['location_id']
					);
				} else {
					$locs[$row['location_name']] = array( 
						'remove' 			=> true 
					);						
				}
			}
			$locs['updatetime'] = date('YmdHis');
		} 
		jsonArr($locs);			
	}

	public function getUserIds()
	{
		// Get the group IDs the current user is assigned too
		$get_group_ids = explode(';',$this->db_conn->getOne("SELECT GROUP_CONCAT(DISTINCT `group_id` SEPARATOR ';') FROM site_group_users WHERE user_id IN (?i)", $this->user_id));
		// Get location IDs based on assigned group IDs
		$get_location_ids = explode(';',$this->db_conn->getOne("SELECT GROUP_CONCAT(DISTINCT `location_id` SEPARATOR ';') FROM site_group_location WHERE group_id IN (?a)", $get_group_ids));
		// Get device IDs based on assigned location IDs 
		$get_device_ids = explode(';',$this->db_conn->getOne("SELECT GROUP_CONCAT(DISTINCT `device_id` SEPARATOR ';') FROM site_location_device WHERE location_id IN (?a)", $get_location_ids));
	
		return $ids = array(
			'get_group_ids' 	=> $get_group_ids,
			'get_location_ids' 	=> $get_location_ids,
			'get_device_ids' 	=> $get_device_ids
		);
	}
	
	public function getList($state)
	{
					
		/* States:
		 * - active
		 * - inactive
		 * - problem
		 */
		if($state == 'active'){
			$check_status = true;
		} else {
			$check_status = false;
		}
		$devices = getApiCall('http://'.WEB_API.'/api/devices', 'GET');
		
		$data['data'] = array();
		
		if($devices){
			foreach($devices['items'] as $device){
				
				$crit_arr = $this->getDeviceCriticalityArr($device['id']);
				
				if($device['isAvailable'] != true){
					$active = '<i class="fa fa-circle text-danger"></i>';
				// If device status array contains a value other than 1 display problem marker
				} elseif(in_array_any(array(2,3), $crit_arr)) {
					$active = '<i class="fa fa-circle text-warning"></i>';				
				} else {
					$active = '<i class="fa fa-circle text-navy"></i>';
				}
						
				$link = '<a href="'.URL_ROOT.'/device/?'.$device['id'].'" class="link"># '.$device['id'].'</a>';
				
				$get_location_name = $this->db_conn->getOne("SELECT `location_name` FROM site_location WHERE `location_id` IN (SELECT `location_id` FROM site_location_device WHERE `device_id` = ?i)", $device['id']);
				$get_location_id = $this->db_conn->getOne("SELECT `location_id` FROM site_location WHERE `location_id` IN (SELECT `location_id` FROM site_location_device WHERE `device_id` = ?i)", $device['id']);
				$location = ($get_location_name != false) ? $get_location_name : '';				
				$mac = rtrim(chunk_split($device['macAddress'], 2, ':'),':');
				
				$ids = $this->getUserIds();
				if(in_array($device['id'],$ids['get_device_ids'])){
					if($device['isAvailable'] == $check_status){
						$data['data'][] = array(
							$link,
							$device['deviceTypeName'],
							'<a onclick="selectMarker('.$get_location_id.');"><i class="fa fa-map-marker"></i></a> '. $location,
							$mac,
							$active
						);
					}					
				}
			}	
			return $data;
		} else {
			return $data;
		}
	}

	public function getEventCount()
	{
		$ids = $this->getUserIds();

		$devices = getApiCall('http://'.WEB_API.'/api/devices', 'GET');
		
		$available = array();
		$not_available = array();
		
		if($devices){
			foreach($devices['items'] as $device){
				if(in_array($device['id'], $ids['get_device_ids'])){
					if($device['isAvailable'] == false){	
						$not_available[] = $device['id'];
					} else {
						$available[] = $device['id'];
					}
				}
			}
			
			$response_array = array(
				'day' => [
					'count' 	=> count($available)
				],				
				'week' => [
					'count' 	=> count($not_available)
				]	
			);
		} else {
			$response_array = array();
		}
		jsonArr($response_array);
	}
	
}