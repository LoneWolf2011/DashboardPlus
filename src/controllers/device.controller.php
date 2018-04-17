<?php
	$db_conn = new SafeMySQL();
	$device_id = preg_replace("/[^0-9]/","", $_GET['id']);
	
	$obj = new Device($db_conn, $device_id);
	$csrf 	= new Csrf();	

	if(isset($_GET['get'])){
		if($_GET['get'] == 'location'){
			jsonArr($obj->getDeviceLocation());
		}	
		
		if($_GET['get'] == 'table'){
			jsonArr($obj->getTableDevice());
		}
		
		if($_GET['get'] == 'status'){
			jsonArr($obj->getDeviceStatus());
		}

		if($_GET['get'] == 'actions'){
			jsonArr($obj->getDeviceActions());
		}		
	}
	if(isset($_GET['execute'])){
		jsonArr($obj->exeDeviceAction($_POST));
	}
		