<?php
	
	class Csrf {
		
		function __construct() {
			$this->locale 		= json_decode(file_get_contents(URL_ROOT.'/Src/lang/'.APP_LANG.'.json'), true);
			$this->auth_user 	= htmlentities($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');
		}		
		
		public function checkCsrf($post_val){
			$lang = $this->locale;
			if(!hash_equals($post_val['csrf'],$_SESSION['db_token'])) 
			{		
				$msg = "CSRF token invalid for user: ". $this->auth_user;
				logToFile(__FILE__,0,$msg);
				$response_array['type'] 	= 'warning';				
				$response_array['title'] 	= $lang['error_msg']['csrf']['label'];			
				$response_array['body'] 	= $lang['error_msg']['csrf']['msg'];
				jsonArr($response_array);
			}
		}
		
	}