<?php
	$db_conn = new SafeMySQL(SCS_DB_CONN);
	
	$obj = new Location($db_conn);
		
	if(isset($_GET['get'])){
		// SCS call
		if($_GET['get'] == 'location'){
			if(isset($_GET['val'])){
				$obj->getLocationValue($_POST, $_GET['val']);
			}
			$obj->getLocation($_POST);
		}

		// RMS call
		if($_GET['get'] == 'rms'){
			if(isset($_GET['poll'])){
				$obj->getLocationRmsPoll($_POST);
			}
			if(isset($_GET['comp'])){
				$obj->getLocationRmsComp($_POST);
			}			
			if(isset($_GET['voeding'])){
				$obj->getLocationRmsVoeding($_POST);
			}				
			$obj->getLocationRmsPath($_POST);
		}
		
		// Datatables call
		if($_GET['get'] == 'comp'){
			$obj->getLocationSCSComp($_GET['id']);
		}
		// Datatables call
		if($_GET['get'] == 'values'){
			$obj->getLocationSCSValues($_GET['id']);
		}

		// Events
		if($_GET['get'] == 'events'){
			if(isset($_GET['pie'])){
				$obj->getLocationSCSEvents($_POST);
			}
			if(isset($_GET['line'])){
				$obj->getLocationEventsChart($_POST);
			}			
		}			

	}