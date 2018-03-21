<?php
	$db_conn = new SafeMySQL();
	
	$obj 	= new Ticket($db_conn);
	$csrf 	= new Csrf();
	
	if(isset($_GET['new'])){
		$csrf->checkCsrf($_POST);
		$obj->ticketCreateNew($_POST);
	}

	if(isset($_GET['save'])){
		$csrf->checkCsrf($_POST);
		$obj->ticketSaveUpdate($_POST);
	}	
	
	if(isset($_GET['soort'])){
		$obj->getTable($_GET['soort']);
	}	