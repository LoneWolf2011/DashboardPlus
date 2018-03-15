<?php
		
	$db_conn = new SafeMySQL(SCS_DB_CONN);
	
	$obj = new Tools($db_conn);
		
	if(isset($_GET['get'])){
		if($_GET['get'] == 'threshold'){
			$obj->getThreshold($_POST);
		}
		
		if($_GET['get'] == 'responsetime'){
			$obj->getResponseTime();
		}

		if($_GET['get'] == 'signalload'){
			$obj->getSignalLoad();
		}
		
		if($_GET['get'] == 'locationsignalcount'){
			$obj->getLocationSignalCount();
		}

		if($_GET['get'] == 'events'){
			if(isset($_GET['pending'])){
				$obj->getPendingEvents();
			}
			if(isset($_GET['grouped'])){
				$obj->getPendingEventsGouped();
			}			
			if(isset($_GET['tasks'])){
				$obj->getPendingEvents('TASK');
			}			
			
		}

		if($_GET['get'] == 'port'){
			if(isset($_GET['feps'])){
				$obj->getPortMonitor();
			}		
			if(isset($_GET['aoip'])){
				$obj->getPortMonitorAoip('AOIP gateway');
			}
		}		
	}
	
