<?php
	$db_conn = new SafeMySQL();
	
	$obj = new Site($db_conn, @$_GET['site']);
		
	if(isset($_GET['get'])){
		if($_GET['get'] == 'signalload'){
			jsonArr($obj->getSignalLoad());
		}		

		if($_GET['get'] == 'zonestable'){
			jsonArr($obj->getZonesTable());
		}
		
		if($_GET['get'] == 'peoplecount'){
			jsonArr($obj->getPeopleCount());
		}		
	}
	
	if(isset($_GET['autocomplete'])){
		$conn = new SafeMySQL();
		$term = trim(strip_tags($_GET['query']));
		
		$query = $conn->query("SELECT `site_id`, `site_name` FROM sensor_sites ");
	
		$reply['suggestions'] 	= array();
	
		while ($row = $db_conn->fetch($query))
		{
			//Add this row to the reply
			$reply['suggestions'][] = array(
				'value'=>htmlentities(stripslashes($row['site_name'])), 
				'data'=>htmlentities(stripslashes($row['site_id']))
			);
		}
		
		echo json_encode($reply);
	}