<?php
/* 
==========================================================================================================

	Name: Mdb functions
	Functie: 
		- Bevat alle core functions die door meerdere pagina's gebruikt worden.	
	Version: 1.0.3
	Author:	Roelof Jan van Golen - <r.vangolen@asb.nl>

	Function index:
==========================================================================================================
		getCategory
		getOutOfService
		getPathStatus
		containsWord
		checkBrute
		appVersionCode
		logToFile
========================================================================================================== */

	function getCategory($scsnr){
		$oms = substr($scsnr,0,6);
		
		if($oms == "010013"){
			$regio = "VRAA";
			$dienst = "Brand";
		} elseif($oms == "010018"){
			$regio = "ZHZ";    
			$dienst = "Brand";				
		} elseif($oms == "020205"){
			$regio = "NHN";
			$dienst = "Brand";				
		} elseif($oms == "010500") {
			$regio = "VRH";    
			$dienst = "Brand";					
		} elseif($oms == "010109"){
			$regio = "VRU_ASB";     
			$dienst = "Brand";					
		} elseif($oms == "010009"){
			$regio = "VRU_KPN";   
			$dienst = "Brand";					
		} elseif($oms == "010114"){
			$regio = "VRGV_ASB";   
			$dienst = "Brand";					
		} elseif($oms == "010014"){
			$regio = "VRGV_KPN"; 
			$dienst = "Brand";					
		} elseif($oms == "010125"){
			$regio = "VRF_ASB"; 
			$dienst = "Brand";					
		} elseif($oms == "010025"){
			$regio = "VRF_KPN";
			$dienst = "Brand";	
		} elseif($oms == "010022") {
			$regio = "EHV";    
			$dienst = "Brand";	
		} elseif($oms == "600100") {
			$regio = "";    
			$dienst = "Brand";						
		} elseif($oms == "800100"){
			$regio = "";
			$dienst = "DIGI";	
		} elseif($oms == "020000"){
			$regio = "";
			$dienst = "RAC";					
		} elseif($oms == "010400"){
			$regio = "";
			$dienst = "ING";		
		} elseif($oms == "010278"){
			$regio = "";
			$dienst = "IPC_ADT";
		} elseif($oms == "010276"){
			$regio = "";
			$dienst = "IPC_SMC";
		} elseif($oms == "010274"){
			$regio = "";
			$dienst = "IPC";				
		} elseif($oms == "010099"){
			$regio = "";
			$dienst = "PAC";						
		} elseif($oms == "010098"){
			$regio = "";					
			$dienst = "VERIFIRE";	
		} elseif($oms == "010273"){
			$regio = "";					
			$dienst = "S&E";
		} elseif($oms == "010100"){
			$regio = "";					
			$dienst = "MIST";
		} elseif($oms == "010300"){
			$regio = "";					
			$dienst = "BNOT";				
		} else {
			$regio = "";
			$dienst = "";					
		}	
		return $dienst;
	}

	function getOutOfService($db_conn, $id){
		$id = preg_replace("/[^0-9]/","", $id);
		
		$query = "SELECT 
			SCS_Account_Nmbr,
			SCS_OUS_Start_DateTime,
			SCS_OUS_End_DateTime 
		FROM scs_outofservice 
		WHERE SCS_Account_Nmbr = '".$id."'";
		
		$arr = array();
		if($row = $db_conn->getRow($query)){
			
			$arr['status'] 	= 1;
			$arr['scs_id'] 	= $row['SCS_Account_Nmbr'];
			$arr['start'] 	= $row['SCS_OUS_Start_DateTime'];
			$arr['end'] 	= $row['SCS_OUS_End_DateTime'];
			
		} else {
			$arr['status'] = 0;
		}
		
		return $arr;
	}

	function getPathStatus($path_status){
		$path_status = (empty($path_status)) ? '????????' : $path_status;
		$path_arr = str_split(substr($path_status,0,4));
		
		$primair = array( @$path_arr[0],  @$path_arr[2]);
		$secundair = array( @$path_arr[1],  @$path_arr[3]);
		
		if(@$path_arr[0] =='?' && @$path_arr[2] == '?' && @$path_arr[1] == '?' && @$path_arr[3] == '?'){
			// No path status 
			$path_conn = 3;
		} elseif(!in_array( '1', $primair ) && in_array( '1', $secundair )){
			// Backup conn
			$path_conn = 2;
		} elseif(!in_array( '1', $primair ) && !in_array( '1', $secundair )){
			// Disconnected
			$path_conn = 0;
		} else {
			// Connected
			$path_conn = 1;
		}
		return $path_conn;
	}

	function jsonArr($response_array){
		header('Content-type: application/json; charset=UTF-8');
		echo json_encode($response_array);	
		exit();			
	}
		
	// Waves blockchain password seed function. Entropy > 3.0
	function genPassSeed($length = 2){
        $path = ROOT_PATH ."/Mdb/Src/config/seed_words.txt";
		$file = file_get_contents($path);
		$word_list = preg_split('/[\s]+/', $file, -1, PREG_SPLIT_NO_EMPTY);	
		// var_dump($words);
		
		$word_count = count($word_list);
		$words = '';
		
		//$r = unpack('c*', bin2hex(openssl_random_pseudo_bytes(4))); // array bytes
		//$x = rand(0, PHP_INT_MAX).str_pad(rand(0,999999999),9,0, STR_PAD_LEFT); //long int
		for($i=0; $i<$length; $i++){
			$r = unpack('c*', bin2hex(openssl_random_pseudo_bytes(4)));
			$x = (int)($r[4] & 0xff) + (($r[3] & 0xff) << 8) + (($r[2] & 0xff) << 16) + (($r[1] & 0xff) << 24);
			$w1 = (int)($x % $word_count);
			$w2 = (int)(((($x / $word_count) >> 0) + $w1) % $word_count);
			$w3 = (int)(((($x / $word_count) >> 0) + $w2) % $word_count);
			//$w3 = (int)(((((($x / $word_count) >> 0) / $word_count) >> 0) + $w2) % $word_count);
			
			$words .= $word_list[$w1] . " ";
			$words .= $word_list[$w2] . " ";
			$words .= $word_list[$w3] . " ";
		}
		$seed = rtrim($words);

		return $seed;
	}	
		
	// Needle in the haystack functie; Checkt of een woord voorkomt in een zin 
	// Return is TRUE als $needle voorkomt in $haystack anders FALSE
	function containsWord($haystack, $needle) {
		return strpos($haystack, $needle) !== false;
	}

	// Functie voor het checken van het aantal login attempts van users.
	// REF: /Beheer/Login/login_process.php
	function checkBrute($user_id, $mysqli) {
		// Get timestamp of current time 
		$now 	= time();
		// All login attempts are counted from the past 2 hours. 
		$valid_attempts = $now - (2 * 60 * 60);
		
		$ini 	= strtoupper($user_id);
		$stmt 	= $mysqli->prepare("SELECT * from app_users_login_attempts where user_id = '$ini' AND time > '$valid_attempts'");
		$stmt->execute();
		
		$count 	= $stmt->rowCount(); 
		// If there have been more than 5 failed logins 
		if ($count > 5) {	
			return true;
		} else {
			return false;
		}
	}

	// Functie voor het ophalen van de hoogste ACTIEVE versioncode voor de LIVE omgeving.
	// indien OTAP omgeving geef HOOGSTE versioncode weer.
	function appVersionCode($env = 'OTAP'){
		$db_conn = new SafeMySQL();	
		
		$env = strtoupper($env);
		
		if($env == 'OTAP') {
			$sql = "SELECT * FROM `app_versions` WHERE id=(SELECT MAX(id) FROM app_versions)";
		} else {
			$sql = "SELECT * FROM `app_versions` WHERE id=(SELECT MAX(id) FROM app_versions  WHERE version_live = 1)";
		}

		if($result = $db_conn->query($sql)){
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
				$versiecode = $row['major'].".".$row['minor'].".".$row['patch'];
				return $versiecode;
				}
			} 			
		}
	}

	// Functie voor het loggen van gebeurtenissen in log files 
	function logToFile($file,$level,$msg) { 
		// Connectie met DB
		$db_conn = new SafeMySQL(array('db' => 'mdb_beheer_log'));	
		$pdo = new PDO("mysql:host=".DB_HOST.";dbname=mdb_beheer_log;charset=utf8", DB_USER, DB_PASS); 	

		$user = (isset($_SESSION['db_user']['user_email'])) ? htmlentities($_SESSION['db_user']['user_email'], ENT_QUOTES, 'UTF-8') : '---';
		$env  = APP_ENV;		
		$year = date("Y");
		$date = date("Y-m-d");
		$path = ROOT_PATH;
        $path .= "/Mdb/Src/Logs/".$year."/";
		// Bestaat de folder niet maak deze dan aan
		if(!file_exists($path)){
			mkdir($path);
		}
		
        $filename = $path.$date.'.log';
        // Open file
		$fileContent = @file_get_contents($filename);
			
		$datum = date("D Y-m-d H:i:s");
			// Log level
			if($level === 1){
				$level = "CRITICAL";
			} elseif($level === 2){
				$level = "WARNING";
			} else {
				$level = "NOTICE";
			}
			
        $str = "[{$datum}] [{$level}] [{$user}] [{$env}] [{$file}] {$msg}".PHP_EOL; 
        // Schrijf string naar file
        file_put_contents($filename, $str . $fileContent);
		
		$query_arr = array(
			'datum' 			=> date("Y-m-d H:i:s"),
			'filename' 			=> $file,
			'criteria_level' 	=> $level,
			'msg'				=> $msg
		);		
		//var_dump($db_conn->query("SHOW TABLES"));
		
		if(!$db_conn->query("SHOW TABLES LIKE 'beheer_log_".$year."'")){
			$db_conn->query("CREATE TABLE `beheer_log_".$year."` (
							`id` INT(11) NOT NULL AUTO_INCREMENT,
							`datum` DATETIME DEFAULT NULL,
							`filename` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
							`criteria_level` VARCHAR(255) COLLATE utf8_unicode_ci DEFAULT NULL,
							`msg` VARCHAR(5000) COLLATE utf8_unicode_ci DEFAULT NULL,
							PRIMARY KEY (`id`)
						) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

			$db_conn->query("INSERT INTO `beheer_log_".$year."` SET ?u", $query_arr);
	
		// Insert in DB
		} else {
			$db_conn->query("INSERT INTO `beheer_log_".$year."` SET ?u", $query_arr);		
		}					
				
	}