<?php
	$db_conn = new SafeMySQL();
	
	$obj = new Settings($db_conn);
	$csrf 	= new Csrf();	

	if(isset($_GET['get'])){
		if($_GET['get'] == 'sitesselect'){
			$obj->getSitesSelect();
		}	

		if($_GET['get'] == 'zonesselect'){
			$obj->getZonesSelect();
		}	
		
		if($_GET['get'] == 'sitestable'){
			$obj->getTable();
		}		
	}

	if(isset($_GET['new'])){
		$csrf->checkCsrf($_POST);
		$obj->newSite($_POST);
	}	

	if(isset($_GET['update'])){
		$csrf->checkCsrf($_POST);
		$obj->updateSite($_POST);
	}
	
	if(isset($_GET['delete'])){
		$csrf->checkCsrf($_POST);
		$obj->deleteSite($_POST);
	}
	
	if(isset($_GET['updatezone'])){
		$csrf->checkCsrf($_POST);
		$obj->updateSiteZones($_POST);
	}		