<?php

	$db_conn = new SafeMySQL();
	
	$obj = new Home($db_conn);
		
	if(isset($_GET['get'])){
		if($_GET['get'] == 'markers'){
			if(isset($_GET['all'])){
				// Get all = true
				$obj->getMarkers(true);
			};
			$obj->getMarkers(false,$_GET['time']);
		}
		
		if($_GET['get'] == 'list'){

			if(isset($_GET['state'])){
				jsonArr($obj->getList($_GET['state']));				
			}

			if(isset($_GET['rms'])){
				$obj->getListRms();				
			}			
		}

		if($_GET['get'] == 'event_count'){
			$obj->getEventCount();
		}		

	}	

	
	if(isset($_GET['autocomplete'])){
		//$conn = new SafeMySQL();
		//$term = trim(strip_tags($_GET['query']));
		
		//$query = $conn->query("SELECT `site_id`, `site_name` FROM sensor_sites ");
		
		$devices = getApiCall('http://'.WEB_API.'/api/devices', 'GET');
		
		$reply['suggestions'] 	= array();
		
		foreach($devices['items'] as $device){	
			//Add this row to the reply
			$reply['suggestions'][] = array(
				'value'=>htmlentities(stripslashes($device['macAddress'])), 
				'data'=>htmlentities(stripslashes($device['id']))
			);		
		}
		
		echo json_encode($reply);
	}	