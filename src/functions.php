<?php
/* 
==========================================================================================================

	Name: Mdb functions
	Functie: 
		- Bevat alle core functions die door meerdere pagina's gebruikt worden.	
	Version: 1.0.5
	Author:	Roelof Jan van Golen - <r.vangolen@asb.nl>

	Function index:
==========================================================================================================
		updateLastAccess
		formatSecToTime
		setEmailTemplate
		jsonArr
		genPassSeed
		containsWord
		checkBrute
		appVersionCode
		logToFile
========================================================================================================== */

	function updateLastAccess($db_conn, $user_id){
		try {
			$stmt 	= $db_conn->prepare("UPDATE app_users SET user_last_access = now() WHERE user_id = ?");
			$stmt->execute([$user_id]);	
		}
		catch (Exception $ex) {
			$msg = 'Regel: ' . $ex->getLine() . ' Bestand: ' . $ex->getFile() . ' Error: ' . $ex->getMessage();
			logToFile(__FILE__, 1, $msg);
		}			
	}

	function formatSecToTime($seconds){
		$dt1 = new DateTime('@0');
		$dt2 = new DateTime("@$seconds");
		$if_day = ($dt1->diff($dt2)->format('%a')) ? '%a days, ' : '';
		$format_time = $dt1->diff($dt2)->format($if_day. '%Hh %Im %Ss');

		return $format_time;		
	}
	
	function setEmailTemplate($arr_val, $template_name){
		$template = file_get_contents(URL_ROOT.'/view/email_temp/'.$template_name);
		
		foreach($arr_val as $key => $value){
			$template = str_replace('{{'.$key.'}}', $value, $template);
		}
		return $template;
	}

	function jsonArr($response_array){
		header('Content-type: application/json; charset=UTF-8');
		echo json_encode($response_array);	
		exit();			
	}
		
	// Waves blockchain password seed function. Entropy > 3.0
	function genPassSeed($length = 2){
        $path = ROOT_PATH ."/Src/config/seed_words.txt";
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
		if ($count >= 5) {	
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
		$db_conn = new SafeMySQL(array('db'=>DB_LOGS));	
		$pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_LOGS.";charset=utf8", DB_USER, DB_PASS); 	

		$user = (isset($_SESSION[SES_NAME]['user_email'])) ? htmlentities($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8') : '---';
		$env  = APP_ENV;		
		$year = date("Y");
		$date = date("Y-m-d");
		$path = ROOT_PATH;
        $path .= "/Src/Logs/".$year."/";
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