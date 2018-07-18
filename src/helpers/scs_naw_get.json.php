<?php

	// Safe DB connection
	$scs_db_conn = new SafeMySQL(SCS_DB_CONN);	
	
	//retrieve the search name and strip and clean input
	$name = trim(strip_tags($_POST['p']));

	$query = $scs_db_conn->query("SELECT 
					SCS_Account_Nmbr, 
					SCS_Account_Address_Name, 
					SCS_Account_Address_Street, 
					SCS_Account_Address_Number, 
					SCS_Account_Address_Zip, 
					SCS_Account_Address_City  
				FROM `scs_account_address`  WHERE SCS_Account_Address_Type = 1 AND SCS_Account_Nmbr LIKE '%".$name."%' OR SCS_Account_Address_Name LIKE '%".$name."%'");

	while ($row = $scs_db_conn->fetch($query)) {

			//Kent Dienst en regio toe op basis van eerste 6 cijfers
			if(substr($row['SCS_Account_Nmbr'], 0, 6) == "010013"){
				$regio = "VRAA";
				$dienst = "Brand";
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010018"){
				$regio = "ZHZ";    
				$dienst = "Brand";				
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "020205"){
				$regio = "NHN";
				$dienst = "Brand";				
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010500") {
				$regio = "VRH";    
				$dienst = "Brand";					
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010109"){
				$regio = "VRU_ASB";     
				$dienst = "Brand";					
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010009"){
				$regio = "VRU_KPN";   
				$dienst = "Brand";					
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010114"){
				$regio = "VRGV_ASB";   
				$dienst = "Brand";					
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010014"){
				$regio = "VRGV_KPN"; 
				$dienst = "Brand";					
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010125"){
				$regio = "VRF_ASB"; 
				$dienst = "Brand";					
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010025"){
				$regio = "VRF_KPN";
				$dienst = "Brand";	
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010022") {
				$regio = "EHV";    
				$dienst = "Brand";	
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "600100") {
				$regio = "";    
				$dienst = "Brand";						
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "800100"){
				$regio = "";
				$dienst = "DIGI";	
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "020000"){
				$regio = "";
				$dienst = "RAC";					
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010400"){
				$regio = "";
				$dienst = "ING";		
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010278"){
				$regio = "";
				$dienst = "IPC_ADT";
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010276"){
				$regio = "";
				$dienst = "IPC_SMC";
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010274"){
				$regio = "";
				$dienst = "IPC";				
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010099"){
				$regio = "";
				$dienst = "PAC";						
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010098"){
				$regio = "";					
				$dienst = "VERIFIRE";	
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010273"){
				$regio = "";					
				$dienst = "S&E";
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010100"){
				$regio = "";					
				$dienst = "MIST";
			} elseif(substr($row['SCS_Account_Nmbr'], 0, 6) == "010300"){
				$regio = "";					
				$dienst = "BNOT";				
			} else {
				$regio = "";
				$dienst = "";					
			}	
		// Maak een json array van de gegevens	
		$json = array( 
			array('omsnr' => $row['SCS_Account_Nmbr']), 
			array('dienst' => $dienst), 
			array('locatie' => $row['SCS_Account_Address_Name']),
			array('adres' => empty($row['SCS_Account_Address_Street']) ? '-' :$row['SCS_Account_Address_Street']." ".$row['SCS_Account_Address_Number']),
			array('postcode' => empty($row['SCS_Account_Address_Zip'])? '-' :$row['SCS_Account_Address_Zip']),
			array('regio' => $regio),
			array('plaats' => empty($row['SCS_Account_Address_City'])? '-' :$row['SCS_Account_Address_City']),
		);
		
	}
	
	//format the array into json data
	echo json_encode($json);