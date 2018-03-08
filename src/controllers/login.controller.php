<?php
		
	$db_conn = new SafeMySQL();
	
	$obj = new Login($db_conn);
		
	if(isset($_GET['install'])){

		$obj->processInstall($_POST);

	}
	
