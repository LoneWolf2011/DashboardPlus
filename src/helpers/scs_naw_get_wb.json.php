<?php

	$scs_db_conn = new SafeMySQL(SCS_DB_CONN);	
	
	$search 	= preg_replace("/[^0-9]/",'', $_POST['scsnr']);
	$soort	 	= preg_replace("/[^A-Z]/",'', $_POST['selected']);
	
	if($soort == "ACCI" || $soort == "ING"){
		$custom_id = "9";
		$custom_txt = "capid";
	} else {
		$custom_id = "7";
		$custom_txt = "serviceid";		
	}
	
	$query = "SELECT custom_data_value FROM scs_account_custom_data_values WHERE scs_account_nmbr LIKE '%".$search."%' AND custom_data_index = ".$custom_id;
	
	$cols  = $scs_db_conn->getRow($query);
	
		$json = array( 
			array($custom_txt 	=> preg_replace("/[^A-Za-z0-9]/",'', $cols['custom_data_value'])),
			array('status' 		=> 'success')
		);		

	//format the array into json data
	echo json_encode($json);	

