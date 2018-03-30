<?php
	$db_conn = new SafeMySQL();
	
	$obj = new Home($db_conn);

	if(isset($_GET['get'])){
		if($_GET['get'] == 'sites'){
			jsonArr($obj->getSitesActivity());
		}

		if($_GET['get'] == 'gettable'){
			jsonArr($obj->getSitesTable());
		}		

		if($_GET['get'] == 'refreshtable'){
			jsonArr($obj->refreshSitesTable(preg_replace("/[^0-9]/","",$_GET['id'])));
		}
		
	}
	