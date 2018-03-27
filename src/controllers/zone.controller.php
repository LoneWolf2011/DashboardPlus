<?php
	$db_conn = new SafeMySQL();
	
	$obj = new Zone($db_conn, @$_GET['zone']);
		
	if(isset($_GET['get'])){
		if($_GET['get'] == 'details'){
			jsonArr($obj->getZoneDetails());
		}		
	}
