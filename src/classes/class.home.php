<?php
	
	class Home {
		protected $succesMessage;
		
		function __construct($db_conn) {
			$this->db_conn 	= $db_conn;
			$this->locale 	= json_decode(file_get_contents(URL_ROOT.'/Src/lang/'.APP_LANG.'.json'), true);

		}	
		
	}