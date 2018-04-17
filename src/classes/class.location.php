<?php

class Location
{
    protected $succesMessage;
    
    function __construct($db_conn)
    {
        $this->db_conn   = $db_conn;
        $this->locale    = json_decode(file_get_contents(URL_ROOT . '/Src/lang/' . APP_LANG . '.json'), true);
        $this->auth_user = htmlentities($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');
    }

	public function getCoordinates($post_val){
		$obj = new googleHelper(GOOGLE_API);

		$str = str_replace(' ','%20',$post_val['addr']).','.str_replace(' ','%20',$post_val['zipc']).','.str_replace(' ','%20',$post_val['city']);
		$arr = $obj->getCoordinates($str);
		$lat = @$arr['lat'];
		$lng = @$arr['lng'];

		return array(
			'lat' => $lat,
			'lon' => $lng,
			'q' => @$arr['q'],
		);
	}
    
    public function updateLocation($post_val)
    {
        $lang = $this->locale;
        $conn = $this->db_conn;
        
        $location_id = $post_val['site_id'];
		
        $query_data = array(
            'location_name' => $post_val['edit_site_name'],
            'location_address' => $post_val['edit_site_address'],
            'location_zipcode' => $post_val['edit_site_zipcode'],
            'location_city' => $post_val['edit_site_city']
        );
		
		if(!empty($post_val['edit_site_latitude']) && !empty($post_val['edit_site_longitude'])){
			$query_data['location_longitude'] = $post_val['edit_site_longitude'];
			$query_data['location_longitude'] = $post_val['edit_site_latitude'];
		}
		
		if(!empty($post_val['edit_site_group'])){
			$q = array(
				'group_id' =>  $post_val['edit_site_group']
			);
            if ($conn->getOne("SELECT `location_id` FROM site_group_location WHERE `location_id` = ?s", $location_id)) {
				
                $conn->query("UPDATE site_group_location SET ?u WHERE `location_id` = ?s", $q, $location_id);

            } else {
                $q['location_id'] = $location_id;
                $conn->query("INSERT site_group_location SET ?u", $query_data);

            }
		}
        
        if ($conn->query("UPDATE site_location SET ?u WHERE location_id = ?s", $query_data, $location_id)) {
            
            // Log to file
            $msg     = "Locatie geupdatet naar " . $post_val['edit_site_name'] . " door " . $this->auth_user;
            $err_lvl = 0;
            
            $response_array['type']  = 'success';
            $response_array['title'] = 'Success';
            $response_array['body']  = 'Locatie <b>' . $post_val['edit_site_name'] . '</b> geupdatet';
            
        } else {
            $msg                     = "Locatie niet geupdatet";
            $err_lvl                 = 2;
            $response_array['type']  = 'error';
            $response_array['title'] = 'ERROR';
            $response_array['body']  = 'Locatie <b>' . $post_val['edit_site_name'] . '</b> niet geupdatet';
            
        }
        
        logToFile(__FILE__, $err_lvl, $msg);
        
        
        // Return JSON array
        jsonArr($response_array);
    }
    
    public function newLocation($post_val)
    {
        $lang = $this->locale;
        $conn = $this->db_conn;
        
        $query_data = array(
            'location_name' => ucfirst($post_val['new_site_name']),
            'location_address' => ucfirst($post_val['new_site_address']),
            'location_zipcode' => ucfirst($post_val['new_site_zipcode']),
            'location_city' => ucfirst($post_val['new_site_city']),
            'location_latitude' => floatval($post_val['new_site_latitude']),
            'location_longitude' => floatval($post_val['new_site_longitude']),
        );
 		$exe = $conn->query("INSERT INTO site_location SET ?u", $query_data);
		$last_id = $conn->insertId();
		
		if($post_val['new_site_group'] != ''){
			$q = array(
				'group_id' 		=> $post_val['new_site_group'],
				'location_id' 	=> $last_id
			);			
		}

        if ($exe) {
			
			$exe_group = $conn->query("INSERT INTO site_group_location SET ?u", $q);
			
            // Log to file
            $msg     = "Nieuwe locatie " . $post_val['new_site_name'] . " aangemaakt door " . $this->auth_user;
            $err_lvl = 0;
            
            $response_array['type']  = 'success';
            $response_array['title'] = 'Success';
            $response_array['body']  = "Nieuwe Locatie <b>" . $post_val['new_site_name'] . "</b> aangemaakt";
            
        } else {
            $msg                     = "Nieuwe Locatie " . $post_val['new_site_name'] . " niet aangemaakt ";
            $err_lvl                 = 2;
            $response_array['type']  = 'error';
            $response_array['title'] = 'ERROR';
            $response_array['body']  = 'Locatie niet aangemaakt';
            
        }
        
        logToFile(__FILE__, $err_lvl, $msg);
        
        
        // Return JSON array
        jsonArr($response_array);
    }
    
    public function deleteLocation($post_val)
    {
        $lang = $this->locale;
        
        $conn = $this->db_conn;

            $site_name = $conn->getOne("SELECT location_name FROM site_location WHERE location_id = ?i", $post_val['site_id']);
            
            if ($conn->query("DELETE FROM site_location WHERE location_id = ?i", $post_val['site_id'])) {
				$conn->query("DELETE FROM site_group_location WHERE location_id = ?i", $post_val['site_id']);
				$number = $conn->getOne("SELECT MAX( `location_id` ) FROM site_location");
				$conn->query("ALTER TABLE site_location AUTO_INCREMENT = ?i", $number +1);
                // Log to file
                $msg     = $site_name . " verwijderd door " . $this->auth_user;
                $err_lvl = 0;
                
                $response_array['type']  = 'success';
                $response_array['title'] = 'Success';
                $response_array['body']  = '<b>' . $site_name . '</b> verwijderd';
                
            } else {
                $msg                     = "Locatie niet verwijderd";
                $err_lvl                 = 2;
                $response_array['type']  = 'error';
                $response_array['title'] = 'ERROR';
                $response_array['body']  = 'Locatie niet verwijderd';
            }
            logToFile(__FILE__, $err_lvl, $msg);
                
        jsonArr($response_array);
    }
    
	public function getTableLocation()
    {
        
        $lang = $this->locale;
        $db   = @new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS, array(\PDO::ATTR_PERSISTENT => true));
        
        $columns = array(
            array(
                'db' => "location_id",
                'dt' => 'DT_RowClass'
            ),
            array(
                'db' => "location_id",
                'dt' => 0
            ),
            array(
                'db' => "location_name",
                'dt' => 1
            ),
            array(
                'db' => "location_address",
                'dt' => 2
            ),
            array(
                'db' => "location_zipcode",
                'dt' => 3
            ),
            array(
                'db' => "location_city",
                'dt' => 4
            ),
            array(
                'db' => "location_id",
                'dt' => 5,
                'formatter' => function($d, $row)
                {
                    $conn  = $this->db_conn;
                    $count = $conn->getOne("SELECT `group_name` FROM site_group WHERE `group_id` = (SELECT `group_id` FROM site_group_location WHERE `location_id` = ?i)", $d);
                    return ($count != "") ? '<span class="badge badge-success">' . $count . '</span>' : '';
                }
            ),
            array(
                'db' => "location_name",
                'dt' => 6,
                'formatter' => function($d, $row)
                {
                    $edit = "<a class='label label-success' id='edit' value='" . $row[0] . "' rel='" . $row[2] . "'>Edit</a>";
                    $dele = "<a class='label label-danger' id='delete' value='" . $row[0] . "' rel='" . $row[2] . "' >Delete</a>";
                    return $edit . ' ' . $dele;
                }
            )
        );
        
        echo json_encode(SSP::complex($_GET, $db, 'site_location', 'location_id', $columns, $whereResult = null, $whereAll = null));
    }

	public function getSelectLocation(){
        $lang = $this->locale;
        $conn = $this->db_conn;
        
        $result_site = $conn->query("SELECT `location_id`, `location_name` FROM site_location");
        
        $select = array();
        while ($site_row = $conn->fetch($result_site)) {
            $select[$site_row['location_id']] = $site_row['location_name'];
        };
                
        return $select;
   	
	}
     
}