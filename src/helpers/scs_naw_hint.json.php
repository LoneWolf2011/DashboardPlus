<?php

	// Safe DB connection
	$scs_db_conn = new SafeMySQL(SCS_DB_CONN);	
	
	//retrieve the search term and strip and clean input
	$term = '%'.trim(strip_tags($_GET['query'])).'%';
	
	//query the database for entries containing the term
	$query = $scs_db_conn->query("SELECT SCS_Account_Nmbr FROM `scs_account_address` 
		WHERE 
			SCS_Account_Address_Type = 1 AND 
			SCS_Account_Nmbr LIKE ?s OR 
			SCS_Account_Address_Name LIKE ?s AND SCS_Account_Address_Type = 1 
		LIMIT 10",$term,$term);

	//array to return
	//$reply 				= array();
	//$reply['query'] 		= $term;
	$reply['suggestions'] 	= array();
	//$reply['value'] 		= array();

	while ($row = $scs_db_conn->fetch($query))  //loop through the retrieved values
	{
		//Add this row to the reply
		array_push($reply['suggestions'], htmlentities(stripslashes($row['SCS_Account_Nmbr'])));
		// $reply['suggestions'] = array($x => htmlentities(stripslashes($row['SCS_Account_Nmbr'])));
	}
	
	//format the array into json data
	echo json_encode($reply);
