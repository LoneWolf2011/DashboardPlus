<?php

	require ROOT_PATH.$root_file['phpmailer'];
	
	$clean_ini 		= preg_replace("/[^a-zA-Z]/", "", $_POST['email'] );
	$cleaned_ini 	= strtoupper($clean_ini); 
	
    if(!empty($_POST['recover']) && hash_equals($_POST['csrf'],$_SESSION['db_token'])) 
    { 
		// Date_time is UNIX timestamp
        $query = " 
            SELECT 
				date_time,
                token_hash,
				token_salt
            FROM users_tokens 
            WHERE 
                user_ini = :ini 
			LIMIT 1
        "; 
         
        $query_params = array( 
            ':ini' => $cleaned_ini 
        ); 
         
        try 
        { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
			$msg = 'Regel: ' . $ex->getLine().' Bestand: ' . $ex->getFile().' Error: ' . $ex->getMessage();
			logToFile(__FILE__,1,$msg);
            die("Error logged to file!"); 
        } 
 
        $token_auth = FALSE; 
        $row = $stmt->fetch(); 
		// Get user row		
        if($row) 
        { 
			// 1 dag beschikbaar = 60 seconds * 60 minutes * 24 hours
			// 10 min = 60 seconds * 10
			$delta = 600;
			// Als server tijd min timestamp uit database groter is dan $delta 
			// dan is de token vervallen.
			if ($_SERVER["REQUEST_TIME"] - $row['date_time'] > $delta) {
				// Log to file
				$msg = "Token vervallen voor user ". $cleaned_ini;
				logToFile(__FILE__,0,$msg);	
				// Indien token vervallen is verwijder uit database
				$db->query("DELETE FROM users_tokens WHERE user_ini = '$cleaned_ini'");
				die(header("Location: ../index.php?tok=exp"));	
			} else {
				// Anders hash de token
				$check_token_hash = hash('sha256', $_POST['token'] . $row['token_salt']); 
				for($round = 0; $round < 65536; $round++) 
				{ 
					$check_token_hash = hash('sha256', $check_token_hash . $row['token_salt']); 
				} 			
				// Komt de gehashde token overeen met de hash in de database dan is auth ok
				if($check_token_hash === $row['token_hash']){
					$token_auth = TRUE; 
				}
			}
		} else {
			$token_auth = FALSE; 
			die(header("Location: ../index.php?tok=uknw"));			
		}
		
        if($token_auth) 
        {  
			// Generate random password
			$gen_password = genPassSeed(2);
			
            $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
            $new_password = hash('sha256', $gen_password . $salt);
			
            for($round = 0; $round < 65536; $round++) 
            { 
                $new_password = hash('sha256', $new_password . $salt); 
            } 	
			
			$query_user = " 
				SELECT 
					id,
					password,
					Voornaam,
					Achternaam,
					email,
					salt
				FROM users 
				WHERE 
					Initialen = :ini 
				LIMIT 1
			"; 
		
        try 
        { 

            $stmt 	= $db->prepare($query_user); 
            $result = $stmt->execute($query_params); 
			$row 	= $stmt->fetch(); 
			
			$new_user = 1;
			
			$db->query("DELETE FROM users_tokens WHERE user_ini = '$cleaned_ini'");
			$db->query("UPDATE users SET password = '$new_password', salt = '$salt', new_user = '$new_user' WHERE id = '". $row['id']."'");
			
			// Log to file
			$msg = "Token valid. Nieuw wachtwoord voor user ". $cleaned_ini." verstuurd";
			logToFile(__FILE__,0,$msg);			
        } 
        catch(PDOException $ex) 
        { 
			$msg = 'Regel: ' . $ex->getLine().' Bestand: ' . $ex->getFile().' Error: ' . $ex->getMessage();
			logToFile(__FILE__,1,$msg);
            die("Error logged to file!"); 
        } 
			
			// Enkel voor testing
			//die(header("Location: ../token.php?rec=".$token."&id=".$row['Initialen']));
			
			$message = '<body>';
			$message .= '<table border="0" style=" border-color: #fff; width: 500px;" cellpadding="5">';		
			$message .= "<th colspan='2' style='border-bottom: 1px solid #9CD4F6;'><img src='".URL_ROOT_IMG."ASB.png' alt='ASB security' style='float: left;'/></th>"; 				
			$message .= "<tr><td colspan='2'>Beste " .$row['Voornaam']." ".$row['Achternaam'].",</td></tr>";
			$message .= "<tr><td colspan='2'> </td></tr>";
			$message .= "<tr><td colspan='2'>De authenticatie token is geverifeerd. <br></td></tr>"; 
			$message .= "<tr><td colspan='2'>Login met jouw gebruikersnaam en het onderstaande wachtwoord op <a class='link' href='".URL_ROOT."index.php?ini=".$cleaned_ini."'>Paperless Office</a></td></tr>";
			$message .= "<th style='border-bottom: 1px solid #9CD4F6; background: #eee;' colspan='3' align='left'>User</th>";		
			$message .= "<tr><td><strong>Nieuw wachtwoord</strong> </td><td>" .$gen_password. "</td></tr>";				
			$message .= "</table>";
			$message .= "</body>";

			$mail = new PHPmailer();
			
			$mail -> AddAddress($row['email']);	
			$mail -> SetFrom(APP_EMAIL);
			$mail -> Subject = "Paperless Office nieuw wachtwoord (2/2)";
			$mail -> MsgHTML($message);
			$mail -> WordWrap = 80;
			
			if($mail->Send()) {	
				die(header("Location: ../index.php?res=suc&ini=".$cleaned_ini));
			} else {
				die(header("Location: ../index.php?res=err"));
			}	
			
		} else {
			die(header("Location: ../index.php?tok=inv"));			
		}
    } else {
		// Log to file
		$msg = "CSRF token invalid during password reset for user: ". $_POST['email'];
		logToFile(__FILE__,0,$msg);
		header("Location: ".URL_ROOT);
		die("Redirecting to: ".URL_ROOT);			
	}