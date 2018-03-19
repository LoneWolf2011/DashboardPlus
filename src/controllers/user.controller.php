<?php
	$db_conn = new SafeMySQL();
	
	$obj = new User($db_conn);
		
	if(isset($_GET['get'])){
		if($_GET['get'] == 'users'){
			$obj->getTable();
		}
	}
	
	if(isset($_GET['new'])){
		$obj->newUser($_POST);
	}

	if(isset($_GET['delete'])){
		$obj->deleteUser($_POST);
	}	
	
	if(isset($_GET['updateuser'])){
		$obj->updateUser($_POST);
	}
	
	if(isset($_GET['update'])){
		$obj->updateUserPass($db, $_POST);
	}