<?php
	$db_conn = new SafeMySQL();
	
	$obj = new Devices($db_conn);
	$csrf 	= new Csrf();	

	if(isset($_GET['get'])){

		if($_GET['get'] == 'devicesselect'){
			$obj->getDevicesSelect();
		}	

		if($_GET['get'] == 'locationselect'){
			$obj->getLocationSelect();
		}
		
		if($_GET['get'] == 'sitestable'){
			jsonArr($obj->getTableDevices());
		}

		if($_GET['get'] == 'coordinates'){
			jsonArr($obj->getCoordinates($_POST));
		}		
	}

	if(isset($_GET['new'])){
		$csrf->checkCsrf($_POST);
		$obj->newDevices($_POST);
	}	

	if(isset($_GET['update'])){
		$csrf->checkCsrf($_POST);
		$obj->updateDevices($_POST);
	}
	
	if(isset($_GET['delete'])){
		$csrf->checkCsrf($_POST);
		$obj->deleteDevices($_POST);
	}

	if(isset($_GET['adddevicetolocation'])){
		$csrf->checkCsrf($_POST);
		$obj->addDevicesToLocation($_POST);
	}			