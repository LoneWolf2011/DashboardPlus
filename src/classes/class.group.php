<?php

class Group
{
    protected $succesMessage;
    
    function __construct($db_conn)
    {
        $this->db_conn   = $db_conn;
        $this->auth_user = htmlentities($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');
    }
    	
    public function updateGroup($post_val)
    {

        $conn = $this->db_conn;
        
        
        $query_data = array(
            'group_name' => $post_val['edit_site_name'],
        );
        
        if ($conn->query("UPDATE site_group SET ?u WHERE group_id = ?s", $query_data, $post_val['site_id'])) {
            
            // Log to file
            $msg     = "Groep geupdatet naar " . $post_val['edit_site_name'] . " door " . $this->auth_user;
            $err_lvl = 0;
            
            $response_array['type']  = 'success';
            $response_array['title'] = 'Success';
            $response_array['body']  = 'Groep <b>' . $post_val['edit_site_name'] . '</b> geupdatet';
            
        } else {
            $msg                     = "Groep niet geupdatet";
            $err_lvl                 = 2;
            $response_array['type']  = 'error';
            $response_array['title'] = 'ERROR';
            $response_array['body']  = 'Groep <b>' . $post_val['edit_site_name'] . '</b> niet geupdatet';
            
        }
        
        logToFile(__FILE__, $err_lvl, $msg);
        
        
        // Return JSON array
        jsonArr($response_array);
    }
    
    public function newGroup($post_val)
    {

        $conn = $this->db_conn;
        
        $query_data = array(
            'group_name' => ucfirst($post_val['new_site_name'])
        );
        
        if ($conn->query("INSERT INTO site_group SET ?u", $query_data)) {
            
            // Log to file
            $msg     = "Nieuwe groep " . $post_val['new_site_name'] . " aangemaakt door " . $this->auth_user;
            $err_lvl = 0;
            
            $response_array['type']  = 'success';
            $response_array['title'] = 'Success';
            $response_array['body']  = "Nieuwe groep <b>" . $post_val['new_site_name'] . "</b> aangemaakt";
            
        } else {
            $msg                     = "Nieuwe groep " . $post_val['new_site_name'] . " niet aangemaakt ";
            $err_lvl                 = 2;
            $response_array['type']  = 'error';
            $response_array['title'] = 'ERROR';
            $response_array['body']  = 'groep niet aangemaakt';
            
        }
        
        logToFile(__FILE__, $err_lvl, $msg);
        
        
        // Return JSON array
        jsonArr($response_array);
    }
    
    public function deleteGroup($post_val)
    {
        
        $conn = $this->db_conn;
		$get_location_count = $conn->getOne("SELECT COUNT(*) FROM site_group_location WHERE `group_id` = ?i", $post_val['site_id']);
        if ($get_location_count) {
            $response_array['type']  = 'warning';
            $response_array['title'] = 'Let op';
            $response_array['body']  = 'Groep kan niet verwijderd worden<br> omdat er <b>'.$get_location_count.'</b> locatie(s) aan gekoppeld zitten';
        } else {
            $site_name = $conn->getOne("SELECT group_name FROM site_group WHERE group_id = ?i", $post_val['site_id']);
            
            if ($conn->query("DELETE FROM site_group WHERE group_id = ?i", $post_val['site_id'])) {
				
				$number = $conn->getOne("SELECT MAX( `group_id` ) FROM site_group");
				$conn->query("ALTER TABLE site_group AUTO_INCREMENT = ?i", $number +1);
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
        }
        
        jsonArr($response_array);
    }
    
	public function getTableGroup()
    {

        $db   = @new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS, array(\PDO::ATTR_PERSISTENT => true));
        
        $columns = array(
            array(
                'db' => "group_id",
                'dt' => 'DT_RowClass'
            ),
            array(
                'db' => "group_id",
                'dt' => 0
            ),
            array(
                'db' => "group_name",
                'dt' => 1
            ),			
            array(
                'db' => "group_id",
                'dt' => 2,
                'formatter' => function($d, $row)
                {
                    $conn  = $this->db_conn;
                    $count = $conn->getOne("SELECT COUNT(`location_id`) FROM site_group_location WHERE `group_id` = ?i", $d);
                    return ($count > 0) ? '<span class="badge badge-success">' . $count . '</span>' : '';
                }
            ),
            array(
                'db' => "group_id",
                'dt' => 3,
                'formatter' => function($d, $row)
                {
                    $conn  = $this->db_conn;
                    $count = $conn->getOne("SELECT COUNT(`user_id`) FROM site_group_users WHERE `group_id` = ?i", $d);
                    return ($count > 0) ? '<span class="badge badge-success">' . $count . '</span>' : '';
                }
            ),			
            array(
                'db' => "group_id",
                'dt' => 4,
                'formatter' => function($d, $row)
                {
                    $edit = "<a class='label label-success' id='edit' value='" . $row[0] . "' rel='" . $row[2] . "'>Edit</a>";
                    $dele = "<a class='label label-danger' id='delete' value='" . $row[0] . "' rel='" . $row[2] . "' >Delete</a>";
                    return $edit . ' ' . $dele;
                }
            )
        );
        
        echo json_encode(SSP::complex($_GET, $db, 'site_group', 'group_id', $columns, $whereResult = null, $whereAll = null));
    }
 
	public function getSelectGroup()
	{

        $conn = $this->db_conn;
        
        $result_site = $conn->query("SELECT `group_id`, `group_name` FROM site_group");
        
        $select = array();
        while ($site_row = $conn->fetch($result_site)) {
            $select[$site_row['group_id']] = $site_row['group_name'];
        };
                
        return $select;
   	
	}

    public function addUserToGroup($post_val)
    {

        $conn = $this->db_conn;
        
        $zones = implode(',', $post_val['add_zones']);
        
        $query_data = array(
            'group_id' => $post_val['select_site']
        );
        foreach ($post_val['add_zones'] as $zone) {
            if ($conn->getOne("SELECT `user_id` FROM site_group_users WHERE `user_id` = ?s", $zone)) {
				
                $conn->query("UPDATE site_group_users SET ?u WHERE `user_id` = ?s", $query_data, $zone);
                
                $msg     = "User: " . $zone . " group gewijzigd naar: " . $post_val['select_site'] . "  door: " . $this->auth_user;
                $err_lvl = 0;
                
                $response_array['type']  = 'success';
                $response_array['title'] = 'Success';
                $response_array['body']  = 'Groep voor user <b>' . $zones . '</b> gewijzigd';
            } else {
                $query_data['user_id'] = $zone;
                $conn->query("INSERT site_group_users SET ?u", $query_data);
                
                $msg     = "User: " . $zone . " toegevoegd aan groep " . $post_val['select_site'] . " door: " . $this->auth_user;
                $err_lvl = 0;
                
                $response_array['type']  = 'success';
                $response_array['title'] = 'Success';
                $response_array['body']  = 'User(s) toegevoegd aan <b>groep' . $post_val['select_site'] . '</b>';
            }
            logToFile(__FILE__, $err_lvl, $msg);
        }
        
        
        
        
        jsonArr($response_array);
    }	
	
    public function getGroupsSelect()
    {

        $conn = $this->db_conn;
        
        $result_site = $conn->query("SELECT `group_id`, `group_name` FROM site_group");
        
        $option = array();
        while ($site_row = $conn->fetch($result_site)) {
            $option[$site_row['group_id']] = $site_row['group_name'];
        }
        ;
        
        if ($result_site) {
            $response_array = array(
                'status' => 1,
                'get_sites' => $option
            );
        } else {
            $response_array['status'] = 0;
        }
        
        jsonArr($response_array);
    }
    
    public function getUsersSelect()
    {

        $conn = $this->db_conn;
        
        $result = $conn->query("SELECT `user_id`, `user_email` FROM app_users");
        
        $option = array();
        while ($row = $conn->fetch($result)) {
            $site = $conn->getOne("SELECT `group_id` FROM site_group_users WHERE `user_id` = ?s", $row['user_id']);
            if ($site) {
                $site_name = 'in group: ' . $conn->getOne("SELECT `group_name` FROM site_group WHERE `group_id` = ?i", $site);
				$in_site = 1;
            } else {
                $site_name = 'NEW';
				$in_site = 0;
            }
            $option[$row['user_id']] = array( 
				'in_site' => $in_site,
				'text' => 'User: ' . $row['user_email'] . ' ' . $site_name
			);
        }
        
        if ($result) {
            $response_array = array(
                'status' => 1,
                'get_zones' => $option
            );
        } else {
            $response_array['status'] = 0;
        }
        
        jsonArr($response_array);
    }
    
	
}