<?php
	$db_conn = new SafeMySQL();
	
	$obj = new Ticket($db_conn);
		
	if(isset($_GET['new'])){
		$obj->ticketCreateNew($_POST);
	}

	if(isset($_GET['save'])){
		$obj->ticketSaveUpdate($_POST);
	}	
	
	if(isset($_GET['soort'])){
		$obj->getTable($_GET['soort']);
	}	