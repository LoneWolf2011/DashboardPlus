<?php

class Settings
{
    protected $succesMessage;
    
    function __construct($db_conn)
    {
        $this->db_conn   = $db_conn;
        $this->locale    = json_decode(file_get_contents(URL_ROOT . '/Src/lang/' . APP_LANG . '.json'), true);
        $this->auth_user = htmlentities($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');
    }
    
    public function updateSiteZones($post_val)
    {
        $lang = $this->locale;
        $conn = $this->db_conn;
        
        $zones = implode(',', $post_val['add_zones']);
        
        $query_data = array(
            'site_id' => $post_val['select_site']
        );
        foreach ($post_val['add_zones'] as $zone) {
            if ($conn->getOne("SELECT `Group` FROM sensor_sites_group WHERE `Group` = ?s", $zone)) {
                $conn->query("UPDATE sensor_sites_group SET ?u WHERE `Group` = ?s", $query_data, $zone);
                
                $msg     = "Zone: " . $zone . " site gewijzigd naar: " . $post_val['select_site'] . "  door: " . $this->auth_user;
                $err_lvl = 0;
                
                $response_array['type']  = 'success';
                $response_array['title'] = 'Success';
                $response_array['body']  = 'Site voor zones <b>' . $zones . '</b> gewijzigd';
            } else {
                $query_data['Group'] = $zone;
                $conn->query("INSERT sensor_sites_group SET ?u", $query_data);
                
                $msg     = "Zone: " . $zone . " toegevoegd aan site " . $post_val['select_site'] . " door: " . $this->auth_user;
                $err_lvl = 0;
                
                $response_array['type']  = 'success';
                $response_array['title'] = 'Success';
                $response_array['body']  = 'Zones toegevoegd aan <b>site' . $post_val['select_site'] . '</b>';
            }
            logToFile(__FILE__, $err_lvl, $msg);
        }
        
        
        
        
        jsonArr($response_array);
    }
    
    public function updateSite($post_val)
    {
        $lang = $this->locale;
        $conn = $this->db_conn;
        
        
        $query_data = array(
            'site_name' => $post_val['edit_site_name'],
            'site_location' => $post_val['edit_site_location'],
            'site_address' => $post_val['edit_site_address'],
            'site_zipcode' => $post_val['edit_site_zipcode'],
            'site_city' => $post_val['edit_site_city']
        );
        
        if ($conn->query("UPDATE sensor_sites SET ?u WHERE site_id = ?s", $query_data, $post_val['site_id'])) {
            
            // Log to file
            $msg     = "Site naam geupdatet naar " . $post_val['edit_site_name'] . " door " . $this->auth_user;
            $err_lvl = 0;
            
            $response_array['type']  = 'success';
            $response_array['title'] = 'Success';
            $response_array['body']  = 'Site <b>' . $post_val['edit_site_name'] . '</b> geupdatet';
            
        } else {
            $msg                     = "Site naam niet geupdatet";
            $err_lvl                 = 2;
            $response_array['type']  = 'error';
            $response_array['title'] = 'ERROR';
            $response_array['body']  = 'Site <b>' . $post_val['edit_site_name'] . '</b> niet geupdatet';
            
        }
        
        logToFile(__FILE__, $err_lvl, $msg);
        
        
        // Return JSON array
        jsonArr($response_array);
    }
    
    public function newSite($post_val)
    {
        $lang = $this->locale;
        $conn = $this->db_conn;
        
        $query_data = array(
            'site_name' => ucfirst($post_val['new_site_name']),
            'site_location' => ucfirst($post_val['new_site_location']),
            'site_address' => ucfirst($post_val['new_site_address']),
            'site_zipcode' => ucfirst($post_val['new_site_zipcode']),
            'site_city' => ucfirst($post_val['new_site_city'])
        );
        
        if ($conn->query("INSERT INTO sensor_sites SET ?u", $query_data)) {
            
            // Log to file
            $msg     = "Nieuwe site " . $post_val['new_site_name'] . " aangemaakt door " . $this->auth_user;
            $err_lvl = 0;
            
            $response_array['type']  = 'success';
            $response_array['title'] = 'Success';
            $response_array['body']  = "Nieuwe site <b>" . $post_val['new_site_name'] . "</b> aangemaakt";
            
        } else {
            $msg                     = "Nieuwe site " . $post_val['new_site_name'] . " niet aangemaakt ";
            $err_lvl                 = 2;
            $response_array['type']  = 'error';
            $response_array['title'] = 'ERROR';
            $response_array['body']  = 'User niet aangemaakt';
            
        }
        
        logToFile(__FILE__, $err_lvl, $msg);
        
        
        // Return JSON array
        jsonArr($response_array);
    }
    
    public function deleteSite($post_val)
    {
        $lang = $this->locale;
        
        $conn = $this->db_conn;
        if ($conn->getOne("SELECT 1 FROM sensor_events WHERE `Group` IN (SELECT `Group` FROM sensor_sites_group WHERE site_id = ?i) limit 1", $post_val['site_id'])) {
            $response_array['type']  = 'warning';
            $response_array['title'] = 'Let op';
            $response_array['body']  = 'Site kan niet verwijderd worden<br> omdat er zones aan gekoppeld zitten';
        } else {
            $site_name = $conn->getOne("SELECT site_name FROM sensor_sites WHERE site_id = ?i", $post_val['site_id']);
            
            if ($conn->query("DELETE FROM sensor_sites WHERE site_id = ?i", $post_val['site_id'])) {
                // Log to file
                $msg     = $site_name . " verwijderd door " . $this->auth_user;
                $err_lvl = 0;
                
                $response_array['type']  = 'success';
                $response_array['title'] = 'Success';
                $response_array['body']  = '<b>' . $site_name . '</b> verwijderd';
                
            } else {
                $msg                     = "Site niet verwijderd";
                $err_lvl                 = 2;
                $response_array['type']  = 'error';
                $response_array['title'] = 'ERROR';
                $response_array['body']  = 'Site niet verwijderd';
            }
            logToFile(__FILE__, $err_lvl, $msg);
        }
        
        jsonArr($response_array);
    }
    
    public function getSitesSelect()
    {
        $lang = $this->locale;
        $conn = $this->db_conn;
        
        $result_site = $conn->query("SELECT `site_id`, `site_name` FROM sensor_sites");
        
        $option = array();
        while ($site_row = $conn->fetch($result_site)) {
            $option[$site_row['site_id']] = $site_row['site_name'];
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
    
    public function getZonesSelect()
    {
        $lang = $this->locale;
        $conn = $this->db_conn;
        
        //$result = $conn->query("SELECT `site_id`, `Group` FROM sensor_sites_group");			
        //
        //$option = array();
        //while ($row = $conn->fetch($result)) {				
        //	$option[$row['Group']] = 'Zone: '.$row['Group'].' in site: '.$conn->getOne("SELECT `site_name` FROM sensor_sites WHERE site_id = ?s", $row['site_id']);
        //};	
        
        $result = $conn->query("SELECT `Group` FROM sensor_events GROUP BY `Group`");
        
        $option = array();
        while ($row = $conn->fetch($result)) {
            $site = $conn->getOne("SELECT `site_id` FROM sensor_sites_group WHERE `Group` = ?s", $row['Group']);
            if ($site) {
                $site_name = 'in site: ' . $conn->getOne("SELECT `site_name` FROM sensor_sites WHERE `site_id` = ?i", $site);
            } else {
                $site_name = 'NEW';
            }
            $option[$row['Group']] = 'Zone: ' . $row['Group'] . ' ' . $site_name;
        }
        ;
        
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
    
    public function getTable()
    {
        
        $lang = $this->locale;
        $db   = @new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS, array(
            \PDO::ATTR_PERSISTENT => true
        ));
        
        $columns = array(
            array(
                'db' => "site_id",
                'dt' => 'DT_RowClass'
            ),
            array(
                'db' => "site_id",
                'dt' => 0
            ),
            array(
                'db' => "site_name",
                'dt' => 1
            ),
            array(
                'db' => "site_location",
                'dt' => 2
            ),
            array(
                'db' => "site_address",
                'dt' => 3
            ),
            array(
                'db' => "site_zipcode",
                'dt' => 4
            ),
            array(
                'db' => "site_city",
                'dt' => 5
            ),
            array(
                'db' => "site_id",
                'dt' => 6,
                'formatter' => function($d, $row)
                {
                    $conn  = $this->db_conn;
                    $count = $conn->getOne("SELECT COUNT(`Group`) FROM sensor_sites_group WHERE `site_id` = ?i", $d);
                    return ($count > 0) ? '<span class="badge badge-success">' . $count . '</span>' : '';
                }
            ),
            array(
                'db' => "site_name",
                'dt' => 7,
                'formatter' => function($d, $row)
                {
                    $edit = "<a class='label label-success' id='edit' value='" . $row[0] . "' rel='" . $row[2] . "'>Edit</a>";
                    $dele = "<a class='label label-danger' id='delete' value='" . $row[0] . "' rel='" . $row[2] . "' >Delete</a>";
                    return $edit . ' ' . $dele;
                }
            )
        );
        
        echo json_encode(SSP::complex($_GET, $db, 'sensor_sites', 'site_id', $columns, $whereResult = null, $whereAll = null));
    }
    
}