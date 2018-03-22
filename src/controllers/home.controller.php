<?php
	$db_conn = new SafeMySQL();
	
	$obj = new Home($db_conn);
		
	if(isset($_GET['get'])){
		if($_GET['get'] == 'signalload'){
			jsonArr($obj->getSignalLoad(array('db'=>'scs_motion')));
		}		

	}