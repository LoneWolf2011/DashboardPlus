<?php
	$db_conn = new SafeMySQL();
	
	$obj 	= new User($db_conn);
	$csrf 	= new Csrf();	
	
	if(isset($_GET['get'])){
		if($_GET['get'] == 'users'){
			$obj->getTable();
		}
	}
	
	if(isset($_GET['new'])){
		$csrf->checkCsrf($_POST);
		$obj->newUser($_POST);
	}

	if(isset($_GET['delete'])){
		$csrf->checkCsrf($_POST);
		$obj->deleteUser($_POST);
	}	
	
	if(isset($_GET['updateuser'])){
		$csrf->checkCsrf($_POST);
		$obj->updateUser($_POST);
	}
	
	if(isset($_GET['update'])){
		$csrf->checkCsrf($_POST);
		$obj->updateUserPass($db, $_POST);
	}