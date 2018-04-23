<?php
	$db_conn = new SafeMySQL();
	
	$obj 	= new Settings($db_conn);
	$csrf 	= new Csrf();	

	if(isset($_GET['get'])){
		if($_GET['get'] == 'settings'){
			jsonArr($obj->getSettings());
		}		
	}

	if(isset($_GET['update'])){
		$csrf->checkCsrf($_POST);
		$obj->updateSettings($_POST);
	}
	