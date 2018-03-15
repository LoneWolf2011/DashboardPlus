<?php
	
	class Login {
		protected $succesMessage;
		protected $env_file = ROOT_PATH.'/Mdb/env.ini';
		protected $google;
		
		function __construct($db_conn) {
			$this->db_conn 	= $db_conn;
			$this->google	= new googleHelper(GOOGLE_API);
			$this->locale 	= json_decode(file_get_contents(URL_ROOT.'Src/lang/'.APP_LANG.'.json'), true);
		}	

		public function processInstall($post_val){
			$conn 	= $this->db_conn;
			
			if(hash_equals($post_val['csrf'],$_SESSION['db_token']))
			{
				$env = parse_ini_file($this->env_file, true);
				$a = (empty($env['SCS_DB']['HOST'])) ? @$post_val['scs_host'] : $env['SCS_DB']['HOST'];
				$b = (empty($env['SCS_DB']['USER'])) ? @$post_val['scs_user'] : $env['SCS_DB']['USER'];
				$c = (empty($env['SCS_DB']['PASS'])) ? @$post_val['scs_pass'] : $env['SCS_DB']['PASS'];
				$d = (empty($env['RMS_DB']['HOST'])) ? @$post_val['rms_host'] : $env['RMS_DB']['HOST'];
				$e = (empty($env['RMS_DB']['USER'])) ? @$post_val['rms_user'] : $env['RMS_DB']['USER'];
				$f = (empty($env['RMS_DB']['PASS'])) ? @$post_val['rms_pass'] : $env['RMS_DB']['PASS'];
				
				$text = '# Generated ini file'.PHP_EOL;
				$text .= '[SCS_DB]'.PHP_EOL;
				$text .= 'HOST = '.$a.PHP_EOL;
				$text .= 'USER = '.$b.PHP_EOL;
				$text .= 'PASS = '.$c.PHP_EOL;
				$text .= 'NAME = scs'.PHP_EOL;
				$text .= '[RMS_DB]'.PHP_EOL;
				$text .= 'HOST = '.$d.PHP_EOL;
				$text .= 'USER = '.$e.PHP_EOL;
				$text .= 'PASS = '.$f.PHP_EOL;
				$text .= 'NAME = rms'.PHP_EOL;
	
				file_put_contents($this->env_file,$text);
				
				//$obj = $this->google;
				//$arr = $obj->getCoordinates($post_val['default_local']);
				
				$arr = explode(',',$post_val['default_local']);
	
				$setting_data = array( 
					'app_lang' 			=>	strtolower($post_val['default_lang']),
					'app_lat' 			=>	0.1+$arr[0], // convert to float
					'app_lng' 			=>	0.1+$arr[1], // convert to float
					'app_debug' 		=>	'false',
					'app_initialize' 	=>	1			
				); 	
				
				$conn->query("UPDATE app_settings SET ?u",$setting_data); 	
				
				if($conn->affectedRows() == 1) {
					// Create admin user
					$admin_arr = $this->createAdmin($post_val);
					
					$this->succesMessage = '<div class="alert alert-success" >
					<font color="green"><b>Install succesful</b></font><br>
					Admin login: <h2><b>'.$admin_arr['name'].'</b></h2><br>
					One time password: <h2><b>'.$admin_arr['pass'].'</b></h2><br>Please store your password somewhere save.<br>
					<a href="'.URL_ROOT.'" class="btn btn-primary">Login</a></div>';
									
					// Log to file
					$msg = 'Install successfull for admin user: '. $_POST['admin_email'];
					$err_lvl 	= 0;
	
				} else { 
					$this->succesMessage = '<div class="alert alert-danger" ><font color="red"><b>Install unsuccesful</b></font><br> please wait.</div>';
					
					$msg = 'Install unsuccesful admin user: '. $_POST['admin_email'];
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
				header("Location: ".URL_ROOT."install.php");
				die("Redirecting to: ".URL_ROOT);			
			}

		}

		protected function createAdmin($post_val){
			$conn 	= $this->db_conn;
			
			$pass = genPassSeed();
			$hash = password_hash($pass, PASSWORD_DEFAULT);
			
			$admin = array( 
				'user_name' 		=>	'Admin',
				'user_last_name' 	=>	'root',
				'user_password' 	=>	$hash,		
				'user_email' 		=>	$post_val['admin_email'],			
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