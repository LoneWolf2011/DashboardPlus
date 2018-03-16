<?php
		
	$db_conn = new SafeMySQL();
	
	$obj = new Login($db_conn);
	
	if(isset($_GET['update'])){
		$obj->updateUserAccount($db, $_POST);
	}

	if(isset($_GET['login'])){
		$obj->processLogin($db, $_POST);
	}

	if(isset($_GET['logout'])){
		$obj->processLogOut($_GET['csrf']);
	}	
	
	if(isset($_GET['gentoken'])){
		$obj->processGenToken($db, $_POST);
	}

	if(isset($_GET['recover'])){
		$obj->processPassReset($db, $_POST);
	}
	
	if(isset($_GET['install'])){
		$obj->processInstall($_POST);
	}
	
