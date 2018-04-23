<?php

class Settings
{
    protected $succesMessage;
    
    function __construct($db_conn)
    {
        $this->db_conn   = $db_conn;
        $this->auth_user = htmlentities($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');
    }
    
    public function updateSettings($post_val)
    {
        $conn = $this->db_conn;
        
        
        $query_data = array(
			'app_name' 			=> $post_val['app_name'],
			'app_title' 		=> $post_val['app_title'],
			'app_email' 		=> $post_val['app_email'],
			'app_lat' 			=> floatval($post_val['app_lat']),
			'app_lng' 			=> floatval($post_val['app_lng']),
			'app_lang'			=> $post_val['app_lang'],
			'app_initialize'	=> (int)$post_val['app_initialize']
        );
        
        if ($conn->query("UPDATE app_settings SET ?u", $query_data)) {
            
            // Log to file
            $msg     = "App settings door " . $this->auth_user;
            $err_lvl = 0;
            
            $response_array['type']  = 'success';
            $response_array['title'] = 'Success';
            $response_array['body']  = 'App settings geupdatet';
            
        } else {
            $msg                     = "App settings NIET geupdatet door " . $this->auth_user;
            $err_lvl                 = 2;
            $response_array['type']  = 'error';
            $response_array['title'] = 'ERROR';
            $response_array['body']  = 'App settings NIET geupdatet';
            
        }
        
        logToFile(__FILE__, $err_lvl, $msg);
        
        
        // Return JSON array
        jsonArr($response_array);
    }

	public function getSettings()
	{
		$conn = $this->db_conn;
						
		$result = $conn->query("SELECT * FROM app_settings");
			
		while ($line = $conn->fetch($result)) {			
			$row = $line;
		};		
		
		$response_array = array(
			'app_name' 			=> $row['app_name'],
			'app_title' 		=> $row['app_title'],
			'app_email' 		=> $row['app_email'],
			'app_lat' 			=> $row['app_lat'],
			'app_lng' 			=> $row['app_lng'],
			'app_lang'			=> $row['app_lang'],
			'app_initialize'	=> $row['app_initialize']
		);

		return $response_array;	
	}	
}