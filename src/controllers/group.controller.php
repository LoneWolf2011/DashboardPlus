<?php
	$db_conn = new SafeMySQL();
	
	$obj = new Group($db_conn);
	$csrf 	= new Csrf();	

	if(isset($_GET['get'])){
		if($_GET['get'] == 'usersselect'){
			$obj->getUsersSelect();
		}	

		if($_GET['get'] == 'groupselect'){
			$obj->getGroupsSelect();
		}

	
		if($_GET['get'] == 'sitestable'){
			$obj->getTableGroup();
		}

		if($_GET['get'] == 'coordinates'){
			jsonArr($obj->getCoordinates($_POST));
		}		
	}

	if(isset($_GET['new'])){
		$csrf->checkCsrf($_POST);
		$obj->newGroup($_POST);
	}	

	if(isset($_GET['update'])){
		$csrf->checkCsrf($_POST);
		$obj->updateGroup($_POST);
	}
	
	if(isset($_GET['delete'])){
		$csrf->checkCsrf($_POST);
		$obj->deleteGroup($_POST);
	}

	if(isset($_GET['addusertogroup'])){
		$csrf->checkCsrf($_POST);
		$obj->addUserToGroup($_POST);
	}		