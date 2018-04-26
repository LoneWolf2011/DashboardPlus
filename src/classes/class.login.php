<?php
	
	class Login {
		protected $succesMessage;
		protected $env_file = ROOT_PATH.'/env.ini';
		protected $msg;
		protected $purifier = null;
		
		function __construct($db_conn) {
			$this->db_conn 		= $db_conn;
			$this->locale 		= json_decode(file_get_contents(URL_ROOT.'/Src/lang/'.APP_LANG.'.json'), true);
			$this->auth_user 	= @htmlentities($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');
			$this->purifier 	= new HTMLPurifier(HTMLPurifier_Config::createDefault());
		}	

		public function processLogin($conn,$post_val){
		
			$submitted_email 	= ''; 
			$cleaned_email 		= strtolower($this->purifier->purify($post_val['email']));    
			
			if(!empty($post_val['login']) && hash_equals($post_val['csrf'],$_SESSION['db_token'])) 
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
					':user_email' => $cleaned_email
				); 
				
				try 
				{ 
					// Execute the query against the database 
					$stmt 	= $conn->prepare($query); 
					$result = $stmt->execute($query_params); 
				} 
				catch(PDOException $ex) 
				{ 
		
					$msg = 'Regel: ' . $ex->getLine().' Bestand: ' . $ex->getFile().' Error: ' . $ex->getMessage();
					logToFile(__FILE__,1,$msg);
					die(header("Location: ../../?fail")); 
				} 
				
				$login_ok = false; 
		
				$row = $stmt->fetch(); 
				if($row) 
				{
					// Brute force preventie
					if (checkBrute($row['user_id'], $conn) === true) {
						// Log to file
						$msg = "Account: ".$row['user_email'] ." geblokkeerd";
						logToFile(__FILE__,1,$msg);				
						// Indien true, account geblokeerd.
						// TO DO: Stuur email met password reset of laat admins login attempts verwijderen.
						die(header("Location: ../../?lck&id=".$cleaned_email));
					} else { 		
						// Indien account status Blocked is log de inlog poging en stop het script.
						$check_status = $row['user_status'];
						// Check of user DEV role heeft.
						$check_dev = TRUE;//containsWord($row['Overige'], "DEV"); 
						
						if($check_status === "Blocked"){
							// Log to file
							$msg = "Login attempt. Blocked user: ".$cleaned_email;
							logToFile(__FILE__,2,$msg);
							
							die(header("Location: ../../?blc&id=".$cleaned_email));
						// Check of APP_ENV op OTAP staat en of user DEV role heeft.
						} elseif(APP_ENV == "OTAP" && $check_dev == FALSE) {	
							// Log to file
							$msg = "Login attempt. Non DEV user: ".$cleaned_email;
							logToFile(__FILE__,2,$msg);
							
							die(header("Location: ../../?dev&id=".$cleaned_email));				
						} else {
							if(password_verify($post_val['password'], $row['user_password'])){ 
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
					
					$_SESSION[SES_NAME] = $row; 
					
					// Log to file
					$msg = "Login success. User: ".$_SESSION[SES_NAME]['user_email'];
					logToFile(__FILE__,0,$msg);
					
					// Verwijder alle login attempts van de user.
					$id = $row['user_id'];
					$this->db_conn->query("DELETE FROM app_users_login_attempts WHERE user_id =?i",$id);			
					
					// Redirect the user to the private members-only page. 
					die($this->redirectLogin());
					//header("Location: ../login/redirect.php"));
				} else { 
					
					// Log to file
					$msg = "Login failed. User: ".$cleaned_email;
					logToFile(__FILE__,2,$msg);
		
					// Save login attempt in database
					$ip_adres	= $_SERVER['REMOTE_ADDR'];
					$ip_port	= $_SERVER['REMOTE_PORT'];
		
					$now = time();
					$date_time_now = date("Y-m-d H:i:s");
					$id = $row['user_id'];
					
					$query_arr = array(
						'user_id' => $id,
						'ip_adres' => $ip_adres,
						'ip_port' => $ip_port,
						'time' => $now,
						'date_time' => $date_time_now
					);
					$this->db_conn->query("INSERT INTO app_users_login_attempts SET ?u", $query_arr);
					
					die(header("Location: ../../?id=".$cleaned_email));  
		
				} 
			} else {
				// Log to file
				$msg = "CSRF token invalid during logout for user: ". $cleaned_email;
				logToFile(__FILE__,0,$msg);
				die(header("Location: ../../?fail"));			
			}
			
			// At the top of the page we check to see whether the user is logged in or not 
			if(!empty($_SESSION[SES_NAME])) 
			{ 
				//header("Location: ../login/redirect.php"); 
				$this->redirectLogin();
			}
		}

		public function processLogOut($csrf){
			if (isset($_GET['csrf']) && hash_equals($csrf,$_SESSION['db_token'])) 
			{
				// Verwijderd de cookie(s) aan de client side
				if(isset($_COOKIE['modal'])){
					unset($_COOKIE['modal']);
					setcookie('modal', '', time() -3600, "/"); // -3600 = 1 uur geleden.
				} 
			
				
				// If we want to keep some session information such as shopping cart contents,
				// we only remove the user's data from the session without unsetting remaining
				// session variables and without destroying the session.
				
				// Log to file
				$msg = "Logout success. User: ".$this->auth_user;
				logToFile(__FILE__,0,$msg);
						
				unset($_SESSION[SES_NAME]);
				unset($_SESSION['db_token']);
			
				// Otherwise, we unset all of the session variables.
				$_SESSION = array();
			
				// If it's desired to kill the session, also delete the session cookie.
				// Note: This will destroy the session, and not just the session data!
				if (ini_get("session.use_cookies")) {
					$params = session_get_cookie_params();
					setcookie(session_name(), '', time() - 42000,
						$params["path"], $params["domain"],
						$params["secure"], $params["httponly"]
					);
				}
			
				// Finally, destroy the session.
				session_destroy();
				
				//$session->destroy($id);
				// Whether we destroy the session or not, we redirect them to the login page
				die(header("Location: ".URL_ROOT));
			} else {
				// Log to file
				$msg = "CSRF token invalid during logout for user: ". $this->auth_user;
				logToFile(__FILE__,0,$msg);
				die(header("Location: ../../?fail"));		
			}			
		}
		
		public function processInstall($post_val){
			$conn = $this->db_conn;
			$lang = $this->locale;
			
			if(hash_equals($post_val['csrf'],$_SESSION['db_token']))
			{
				$env = parse_ini_file($this->env_file, true);
				
				$a = (empty($env['APP']['URL_ROOT'])) ? @$post_val['app_url_root'] : $env['APP']['URL_ROOT'];
				$b = (empty($env['APP']['URL_ROOT_IMG'])) ? @$post_val['app_url_root'].'img/' : $env['APP']['URL_ROOT_IMG'];
				$c = (empty($env['APP']['ROOT_PATH'])) ? @$post_val['app_document_root'] : $env['APP']['ROOT_PATH'];
				$d = (empty($env['APP']['GOOGLE_API'])) ? @$post_val['app_google_key'] : $env['APP']['GOOGLE_API'];
				
				$e = (empty($env['SCS_DB']['HOST'])) ? @$post_val['scs_host'] : $env['SCS_DB']['HOST'];
				$f = (empty($env['SCS_DB']['USER'])) ? @$post_val['scs_user'] : $env['SCS_DB']['USER'];
				$g = (empty($env['SCS_DB']['PASS'])) ? @$post_val['scs_pass'] : $env['SCS_DB']['PASS'];
				
				//$h = (empty($env['SMTP']['SMTP_HOST'])) ? @$post_val['scs_pass'] : $env['SCS_DB']['PASS'];
				//$i = (empty($env['SCS_DB']['PASS'])) ? @$post_val['scs_pass'] : $env['SCS_DB']['PASS'];
				//$d = (empty($env['RMS_DB']['HOST'])) ? @$post_val['rms_host'] : $env['RMS_DB']['HOST'];
				//$e = (empty($env['RMS_DB']['USER'])) ? @$post_val['rms_user'] : $env['RMS_DB']['USER'];
				//$f = (empty($env['RMS_DB']['PASS'])) ? @$post_val['rms_pass'] : $env['RMS_DB']['PASS'];
				
				$text = '; Generated ini file'.PHP_EOL;
				$text .= '[APP]'.PHP_EOL;
				$text .= 'URL_ROOT = '.$env['APP']['URL_ROOT'].PHP_EOL;
				$text .= 'URL_ROOT_IMG = '.$env['APP']['URL_ROOT_IMG'].PHP_EOL;
				$text .= 'ROOT_PATH = '.$env['APP']['ROOT_PATH'].PHP_EOL;
				$text .= 'SES_NAME = '.$env['APP']['SES_NAME'].PHP_EOL;
				$text .= 'ENV = '.$env['APP']['ENV'].PHP_EOL;
				$text .= 'VER = '.$env['APP']['VER'].PHP_EOL;
				$text .= 'GOOGLE_API = '.$d.PHP_EOL;
				$text .= 'DEBUG = true'.PHP_EOL;				
				$text .= '[EVENTS]'.PHP_EOL;
				$text .= 'ENABLE_AUDIO = true'.PHP_EOL;
				$text .= 'ENABLE_GROUPED_EVENTS = true'.PHP_EOL;
				$text .= 'GROUPED_EVENTS = 5'.PHP_EOL;
				$text .= 'GROUPED_EVENTS_WARNING = 10'.PHP_EOL;
				$text .= 'GROUPED_EVENTS_DANGER = 20'.PHP_EOL;
				$text .= '[SMTP]'.PHP_EOL;
				$text .= 'SMTP_HOST = '.$env['SMTP']['SMTP_HOST'].PHP_EOL;
				$text .= 'SMTP_PORT = '.$env['SMTP']['SMTP_PORT'].PHP_EOL;
				$text .= '[LOCAL_DB]'.PHP_EOL;
				$text .= 'HOST = '.$env['LOCAL_DB']['HOST'].PHP_EOL;
				$text .= 'USER = '.$env['LOCAL_DB']['USER'].PHP_EOL;
				$text .= 'PASS = '.$env['LOCAL_DB']['PASS'].PHP_EOL;
				$text .= 'NAME = '.$env['LOCAL_DB']['NAME'].PHP_EOL;
				$text .= 'LOGS = '.$env['LOCAL_DB']['LOGS'].PHP_EOL;
				$text .= '[SCS_DB]'.PHP_EOL;
				$text .= 'HOST = '.$e.PHP_EOL;
				$text .= 'USER = '.$f.PHP_EOL;
				$text .= 'PASS = '.$g.PHP_EOL;
				$text .= 'NAME = scs'.PHP_EOL;

				file_put_contents($this->env_file,$text);
				
				//$obj = $this->google;
				//$arr = $obj->getCoordinates($post_val['default_local']);
				
				$arr = explode(',',$post_val['default_local']);
	
				$setting_data = array( 
					'app_email' 		=>	$post_val['admin_email'],
					'app_lang' 			=>	strtolower($post_val['default_lang']),
					'app_lat' 			=>	0.1+$arr[0], // convert to float
					'app_lng' 			=>	0.1+$arr[1], // convert to float
					'app_initialize' 	=>	1			
				); 	
				
				$conn->query("UPDATE app_settings SET ?u",$setting_data); 	
				
				if($conn->affectedRows()) {
					// Create admin user
					$admin_arr = $this->createAdmin($post_val);
					
					$suc_msg = '<div class="alert alert-success" >';
					$suc_msg .= '<font color="green"><b data-i18n="[html]installscreen.msg.suc">'.$lang['installscreen']['msg']['suc'].'</b></font><br>';
					$suc_msg .= $lang['installscreen']['admin']['msg']['user'].': <h2><b>'.$admin_arr['name'].'</b></h2><br>';
					$suc_msg .= $lang['installscreen']['admin']['msg']['pass'].': <h2><b>'.$admin_arr['pass'].'</b></h2><br>';
					$suc_msg .= $lang['installscreen']['admin']['msg']['store'].'.<br>';
					$suc_msg .= '<a href="'.URL_ROOT.'" class="btn btn-primary">Login</a></div>';
							
					$this->succesMessage = $suc_msg;
					// Log to file
					$msg 		= 'Install successfull for admin user: '. $_POST['admin_email'];
					$err_lvl 	= 0;
	
				} else { 
					$this->succesMessage = '<div class="alert alert-danger" ><font color="red"><b>'.$lang['installscreen']['msg']['fail'].'</b></font><br> '.$lang['installscreen']['msg']['try'].'.</div>';
					
					$msg 		= 'Install unsuccesful admin user: '. $_POST['admin_email'];
					$err_lvl 	= 2;
				}	
				
				$response_array['body'] 	= $this->succesMessage;
				
				// Log to file functie
				logToFile(__FILE__,$err_lvl,$msg);	
				// Return JSON array
				jsonArr($response_array);				
			} else {
				// Log to file
				$msg = "CSRF token invalid during install for user: ". $_POST['admin_email'];
				logToFile(__FILE__,0,$msg);
				die(header("Location: ../../?fail"));			
			}

		}

		public function processGenToken($conn,$post_val){
			$lang = $this->locale;
			$cleaned_email 	= strtolower($this->purifier->purify($post_val['email'])); 
			
			if(!empty($_POST['request']) && hash_equals($_POST['csrf'],$_SESSION['db_token']))
			{
			
				$query = " 
					SELECT 
						user_id,
						user_email,
						user_name,
						user_last_name
					FROM app_users 
					WHERE 
						user_email = :user_email 
					LIMIT 1
				"; 
				
				$query_params = array( 
					':user_email' => $cleaned_email 
				); 
				
				try 
				{ 
					$stmt 	= $conn->prepare($query); 
					$result = $stmt->execute($query_params); 
				} 
				catch(PDOException $ex) 
				{ 
					$msg = 'Regel: ' . $ex->getLine().' Bestand: ' . $ex->getFile().' Error: ' . $ex->getMessage();
					logToFile(__FILE__,1,$msg);
					die(header("Location: ../../?fail")); 
				} 
		
				$user_auth = false; 
				$row = $stmt->fetch(); 
				
				if($row){ 
					$user_auth = true; 
				} else {
					die(header("Location: ../../?uknw=".$cleaned_email));			
				}
				
				if($user_auth) {  
					// Generate random token
					$token 		= openssl_random_pseudo_bytes(32);
					$token 		= bin2hex($token);
				
					$salt 		= dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
			
					$token_hash = hash('sha256', $token . $salt); 
			
					for($round 	= 0; $round < 65536; $round++){ 
						$token_hash = hash('sha256', $token_hash . $salt); 
					} 		
					
					$query = " 
						INSERT INTO app_users_tokens ( 
							user_email, 
							user_date_time, 
							user_date_request, 
							user_token_hash,
							user_token_salt					
						) VALUES ( 
							:user_email, 
							:date_time, 
							:date_request, 
							:token_hash, 
							:token_salt 
						) 
					"; 
					
					$query_params = array( 
						':user_email' 	=> $cleaned_email, 
						':date_time' 	=> time(),
						':date_request' => date("Y-m-d H:i:s"), 
						':token_hash' 	=> $token_hash,
						':token_salt' 	=> $salt
					); 
			
					try 
					{ 
						$stmt	= $conn->prepare($query); 
						$result = $stmt->execute($query_params);
						
						// Log to file
						$msg = "Token request voor user ". $cleaned_email." aangevraagd";
						logToFile(__FILE__,0,$msg);			
					} 
					catch(PDOException $ex) 
					{ 			
						$msg = 'Regel: ' . $ex->getLine().' Bestand: ' . $ex->getFile().' Error: ' . $ex->getMessage();
						logToFile(__FILE__,1,$msg);
						die(header("Location: ../../?tok=err"));
					} 
			
					// Enkel voor testing
					//die(header("Location: ../token.php?rec=".$token."&id=".$row['Initialen']));
					
					// Note: use the key names specified in the email template as array key
					$email_template = array(
						'user_name' 	=> $row['user_name']." ".$row['user_last_name'],
						'recover_link' 	=> '<a class="link" href="'.URL_ROOT.'/token.php?rec='.$token.'&id='.$row['user_email'].'">Recover token</a>'
					);
									
					$mail = new PHPmailer();
					$mail -> isSMTP();
					$mail -> Host = SMTP_HOST;
					$mail -> Port = SMTP_PORT;
					$mail -> AddAddress($row['user_email']);	
					$mail -> SetFrom(APP_EMAIL);
					$mail -> Subject = APP_TITLE.' '.$lang['tokenmsg']['email']['token_req'];
					$mail -> MsgHTML(setEmailTemplate($email_template, 'email.gen_token.php'));
					$mail -> WordWrap = 80;
					
					if($mail->Send()) {	
						die(header("Location: ../../?tok=suc"));
					} else {
						die(header("Location: ../../?tok=err"));
					}			
					
				} else {
					die(header("Location: ../../?uknw=".$cleaned_email));			
				}
			} else {
				// Log to file
				$msg = "CSRF token invalid during token generate for user: ". $cleaned_email;
				logToFile(__FILE__,0,$msg);
				die(header("Location: ../../?fail"));		
			}			
		}

		public function processPassReset($conn,$post_val){
			$lang = $this->locale;
			$cleaned_email 	= strtolower($this->purifier->purify($post_val['email']));
			
			if(!empty($_POST['recover']) && hash_equals($_POST['csrf'],$_SESSION['db_token'])) 
			{ 
				// Date_time is UNIX timestamp
				$query = " 
					SELECT 
						user_date_time,
						user_token_hash,
						user_token_salt
					FROM app_users_tokens 
					WHERE 
						user_email = :user_email 
					LIMIT 1"; 
				
				$query_params = array( 
					':user_email' => $cleaned_email 
				); 
				
				try 
				{ 
					$stmt = $conn->prepare($query); 
					$result = $stmt->execute($query_params); 
				} 
				catch(PDOException $ex) 
				{ 
					$msg = 'Regel: ' . $ex->getLine().' Bestand: ' . $ex->getFile().' Error: ' . $ex->getMessage();
					logToFile(__FILE__,1,$msg);
					die(header("Location: ../../?fail")); 
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
					if ($_SERVER["REQUEST_TIME"] - $row['user_date_time'] > $delta) {
						// Log to file
						$msg = "Token vervallen voor user ". $cleaned_email;
						logToFile(__FILE__,0,$msg);	
						// Indien token vervallen is verwijder uit database
						$this->db_conn->query("DELETE FROM app_users_tokens WHERE user_email = ?s ",$cleaned_email);
						die(header("Location: ../../?tok=exp"));	
					} else {
						// Anders hash de token
						$check_token_hash = hash('sha256', $_POST['token'] . $row['user_token_salt']); 
						for($round = 0; $round < 65536; $round++) { 
							$check_token_hash = hash('sha256', $check_token_hash . $row['user_token_salt']); 
						} 			
						// Komt de gehashde token overeen met de hash in de database dan is auth ok
						if(hash_equals($check_token_hash, $row['user_token_hash'])){
							$token_auth = TRUE; 
						}
					}
				} else {
					$token_auth = FALSE; 
					die(header("Location: ../../?tok=uknw"));			
				}
				
				if($token_auth) 
				{  
					// Generate random password
					$gen_password = genPassSeed(2);
					$hash = password_hash($gen_password, PASSWORD_DEFAULT);
					
					$query_user = " 
						SELECT 
							user_id,
							user_email,
							user_name,
							user_last_name,
							user_password
						FROM app_users 
						WHERE 
							user_email = :user_email 
						LIMIT 1"; 
				
				try 
				{ 
		
					$stmt 	= $conn->prepare($query_user); 
					$result = $stmt->execute($query_params); 
					$row 	= $stmt->fetch(); 
					
					$new_user = 1;
					
					$this->db_conn->query("DELETE FROM app_users_tokens WHERE user_email = ?s", $cleaned_email);
					$this->db_conn->query("UPDATE app_users SET user_password = ?s, user_new = ?i WHERE user_id = ?i",$hash, $new_user,$row['user_id']);
					
					// Log to file
					$msg = "Token valid. Nieuw wachtwoord voor user ". $cleaned_email." verstuurd";
					logToFile(__FILE__,0,$msg);			
				} 
				catch(PDOException $ex) 
				{ 
					$msg = 'Regel: ' . $ex->getLine().' Bestand: ' . $ex->getFile().' Error: ' . $ex->getMessage();
					logToFile(__FILE__,1,$msg);
					die(header("Location: ../../?fail")); 
				} 
					
					// Enkel voor testing
					//die(header("Location: ../token.php?rec=".$token."&id=".$row['Initialen']));

					$email_template = array(
						'user_name' 	=> $row['user_name']." ".$row['user_last_name'],
						'link' 			=> '<a class="link" href="'.URL_ROOT.'?ini='.$cleaned_email.'">DB+</a>',
						'gen_password' 	=> $gen_password,
					);
										
					$mail = new PHPmailer();
					$mail -> isSMTP();
					$mail -> Host = SMTP_HOST;
					$mail -> Port = SMTP_PORT;
					$mail -> AddAddress($row['user_email']);	
					$mail -> SetFrom(APP_EMAIL);
					$mail -> Subject = APP_TITLE.' '.$lang['tokenmsg']['email']['token_rec'];
					$mail -> MsgHTML(setEmailTemplate($email_template, 'email.password_reset.php'));
					$mail -> WordWrap = 80;
					
					if($mail->Send()) {	
						die(header("Location: ../../?res=suc&ini=".$cleaned_email));
					} else {                     
						die(header("Location: ../../?res=err"));
					}	
					
				} else {
					die(header("Location: ../../?tok=inv"));			
				}
			} else {
				// Log to file
				$msg = "CSRF token invalid during password reset for user: ". $cleaned_email;
				logToFile(__FILE__,0,$msg);
				die(header("Location: ../../?fail"));			
			}			
		}
		
		public function redirectLogin(){
			$conn 	= $this->db_conn;
			// At the top of the page we check to see whether the user is logged in or not 
			if(empty($_SESSION[SES_NAME])) 
			{ 
				// If they are not, we redirect them to the login page. 
				header("Location: ".URL_ROOT); 
				
				// Remember that this die statement is absolutely critical.  Without it, 
				// people can view your members-only content without logging in. 
				die(header("Location: ".URL_ROOT)); 
			} 
		
			//Update Lastaccess kolom in users database
			$id 		= $_SESSION[SES_NAME]['user_id'];		
 
			try {
				$conn->query("UPDATE app_users SET user_last_access = now() WHERE user_id = ?s",$id);
				$connected 	= true;
			} catch (Exception $ex) {
				$msg = 'Regel: ' . $ex->getLine().' Bestand: ' . $ex->getFile().' Error: ' . $ex->getMessage();
				logToFile(__FILE__,1,$msg);				
				$connected 	= false;
			}
		
			//Redirect naar juiste index pagina op basis van Userrole	
			$user_role 		= $_SESSION[SES_NAME]['user_role'];
			
			if(APP_INITIALIZE === 0 && $connected){
				header("Location: ".URL_ROOT."/view/install.php"); 
			} elseif($user_role == 1 && $connected){
				header("location: ".URL_ROOT."/view/admin/");
			} elseif($user_role == 2 && $connected) {
				header("location: ".URL_ROOT."/view/home/");
			} else {
				header("location: ".URL_ROOT);
			}			
		}
		
		protected function createAdmin($post_val){
			$conn 	= $this->db_conn;
			
			$pass = genPassSeed();
			$hash = password_hash($pass, PASSWORD_DEFAULT);
			
			$admin = array( 
				'user_name' 		=>	'Root',
				'user_last_name' 	=>	'Admin',
				'user_password' 	=>	$hash,		
				'user_email' 		=>	strtolower($post_val['admin_email']),			
				'user_last_access' 	=>	date('Y-m-d H:i:s'),		
				'user_role' 		=>	1,		
			); 	
			
			$conn->query("INSERT INTO app_users SET ?u",$admin); 
			
			if($conn->affectedRows() == 1) {
				return array('status' => 'success','name' => $admin['user_email'], 'pass' => $pass);
			} else {
				return array('status' => 'failed');
			}
		}
		
	}