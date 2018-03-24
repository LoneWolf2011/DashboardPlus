<?php
	$db_conn = new SafeMySQL();
	
	$obj = new Home($db_conn, @$_GET['site']);
		
	if(isset($_GET['get'])){
		if($_GET['get'] == 'signalload'){
			jsonArr($obj->getSignalLoad(array('db'=>'scs_motion')));
		}		
		
		if($_GET['get'] == 'peoplecount'){
			jsonArr($obj->getPeopleCount(array('db'=>'scs_motion')));
		}		
	}
	
	if(isset($_GET['autocomplete'])){
		$conn = new SafeMySQL(array('db'=>'scs_motion'));
		$term = trim(strip_tags($_GET['query']));
		
		$query = $conn->query("SELECT `site` FROM sensor_events GROUP BY `site`");
	
		$reply['suggestions'] 	= array();
	
		while ($row = $db_conn->fetch($query))
		{
			//Add this row to the reply
			array_push($reply['suggestions'], htmlentities(stripslashes($row['site'])));
		}
		
		echo json_encode($reply);
	}