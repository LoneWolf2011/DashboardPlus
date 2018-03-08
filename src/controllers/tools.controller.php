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
	}
	
