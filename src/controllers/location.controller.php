<?php
	$db_conn = new SafeMySQL();
	
	$obj = new Location($db_conn);
	$csrf 	= new Csrf();	

	if(isset($_GET['get'])){
		
		if($_GET['get'] == 'sitestable'){
			$obj->getTableLocation();
		}

		if($_GET['get'] == 'coordinates'){
			jsonArr($obj->getCoordinates($_POST));
		}		
	}

	if(isset($_GET['new'])){
		$csrf->checkCsrf($_POST);
		$obj->newLocation($_POST);
	}	

	if(isset($_GET['update'])){
		$csrf->checkCsrf($_POST);
		$obj->updateLocation($_POST);
	}
	
	if(isset($_GET['delete'])){
		$csrf->checkCsrf($_POST);
		$obj->deleteLocation($_POST);
	}
			