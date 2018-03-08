<?php 
  
    $submitted_email 	= ''; 
	$email 					= strtolower($_POST['email']);    
	
    if(!empty($_POST['login']) && hash_equals($_POST['csrf'],$_SESSION['token'])) 
    { 
        // This query retreives the user's information from the database using 
        // their email. 
        $query = " 
            SELECT 
                *
            FROM app_users 
            WHERE 
                user_email = :user_email 
			LIMIT 1
        "; 
         
        // The parameter values 
        $query_params = array( 
            ':user_email' => $email
        ); 
         
        try 
        { 
            // Execute the query against the database 
            $stmt 	= $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 

			$msg = 'Regel: ' . $ex->getLine().' Bestand: ' . $ex->getFile().' Error: ' . $ex->getMessage();
			logToFile(__FILE__,1,$msg);
			
            die("Error logged to file!"); 
        } 
         
        $login_ok = false; 

        $row = $stmt->fetch(); 
        if($row) 
        {
			// Brute force preventie
			if (checkBrute($row['user_id'], $db) === true) {
				// Log to file
				$msg = "Account: ".$row['user_email'] ." geblokkeerd";
				logToFile(__FILE__,1,$msg);				
				// Indien true, account geblokeerd.
				// TO DO: Stuur email met password reset of laat admins login attempts verwijderen.
				die(header("Location: ../../?lck&id=".$email));
			} else { 		
				// Indien account status Blocked is log de inlog poging en stop het script.
				$check_status = $row['user_status'];
				// Check of user DEV role heeft.
				$check_dev = TRUE;//containsWord($row['Overige'], "DEV"); 
				
				if($check_status === "Blocked"){
					// Log to file
					$msg = "Login attempt. Blocked user: ".$email;
					logToFile(__FILE__,2,$msg);
					
					die(header("Location: ../../?blc&id=".$email));
				// Check of APP_ENV op OTAP staat en of user DEV role heeft.
				} elseif(APP_ENV == "OTAP" && $check_dev == FALSE) {	
					// Log to file
					$msg = "Login attempt. Non DEV user: ".$email;
					logToFile(__FILE__,2,$msg);
					
					die(header("Location: ../../?dev&id=".$email));				
				} else {
					if(password_verify($_POST['password'], $row['user_password'])){ 
						// Indien de passwords overeen komen zet $login_ok naar TRUE 
						$login_ok = true; 
					} 					
					
				}
			}
        } 
         
        if($login_ok) 
        { 
            unset($row['user_password']); 
            unset($row['user_status']); 
             
            $_SESSION['user'] = $row; 
			
			// Log to file
			$msg = "Login success. User: ".$_SESSION['user']['user_email'];
			logToFile(__FILE__,0,$msg);
			
			// Verwijder alle login attempts van de user.
			$id = $row['user_id'];
			$db->query("DELETE FROM app_users_login_attempts WHERE user_id = $id");			
			
            // Redirect the user to the private members-only page. 
            die(header("Location: redirect.php"));
        } else { 
			
			// Log to file
			$msg = "Login failed. User: ".$email;
			logToFile(__FILE__,2,$msg);

			// Save login attempt in database
			$ip_adres	= $_SERVER['REMOTE_ADDR'];
			$ip_port	= $_SERVER['REMOTE_PORT'];

			$now = time();
			$date_time_now = date("Y-m-d H:i:s");
			$id = $row['user_id'];
			$db->query("INSERT INTO app_users_login_attempts(user_id, ip_adres, ip_port, time, date_time)
			VALUES ('$id', '$ip_adres', '$ip_port', '$now', '$date_time_now')");
			
			die(header("Location: ../../?id=".$email));  

        } 
    } else {
		// Log to file
		$msg = "CSRF token invalid during logout for user: ". $_POST['email'];
		logToFile(__FILE__,0,$msg);
		header("Location: ".URL_ROOT);
		die("Redirecting to: ".URL_ROOT);			
	}
     
    // At the top of the page we check to see whether the user is logged in or not 
    if(!empty($_SESSION['user'])) 
    { 
        header("Location: redirect.php"); 
    } 
     
    // Everything below this point in the file is secured by the login system 

