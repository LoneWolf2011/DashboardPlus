<?php
/**
 * Class and Function List:
 * Function list:
 * - __construct()
 * - getMarkers()
 * - getList()
 * - getListRms()
 * - getEventCount()
 * - getMarkersDiv()
 * - setMarkersStatusArray()
 * - setMarkersDivLocations()
 * - setMarkersGrouped()
 * - setMarkersDivCount()
 * - getPathStatusTranslation()
 * Classes list:
 * - Home
 */
class Home
{
    /*
     *	$path_arr possible values used to show locations and grouped locations
     *	0 = disconnected
     *	1 = connected
     *	2 = backup disconnected
     *	3 = primair disconnected
     *	4 = no path defined
    */
    protected $path_arr = array(0,2,3);
    /*
     *	Constants values defined in env.ini
    */
    protected $enableGroupedThreshold = ENABLE_GROUPED_LOCATIONS;
    protected $groupedThreshold = GROUPED_LOCATIONS;
    protected $groupedThresholdWarning = GROUPED_LOCATIONS_WARNING;
    protected $groupedThresholdDanger = GROUPED_LOCATIONS_DANGER;

    function __construct($db_conn)
    {
        $this->db_conn = $db_conn;
    }
    
	/**
     * Get marker set based on locations in SCS who got latitude and longitude defined
     *
     * @param boolean $getall - get complete dataset of get changed dataset since $updatetime
     * @param string $updatetime - datetime string used to query locations changed since last update time
     * @return array|JSON
     */
    public function getMarkers($getall = false, $updatetime = '', $setinfo = true)
    {
        $conn = $this->db_conn;

        $datetime = date('YmdHis', strtotime('now'));

        // Initial query
        $sql = "SELECT
					scs_account_address.SCS_Account_Nmbr,
					scs_account_address.SCS_Account_Address_Name,
					scs_account_address.SCS_Account_Address_Address,
					scs_account_address.SCS_Account_Address_Zip,
					scs_account_address.SCS_Account_Address_City,
					scs_account_status.SCS_Account_Stat_Connection_Path,
					scs_account_status.SCS_Account_Stat_Last_Signal,
					scs_account_status.SCS_Account_Stat_Active,
					scs_account_info.SCS_Account_CallerID_1,
					scs_account_info.Latitude,
					scs_account_info.Longitude
				FROM scs_account_address
				INNER JOIN scs_account_status ON scs_account_address.SCS_Account_Nmbr = scs_account_status.SCS_Account_Nmbr
				LEFT JOIN scs_account_info ON scs_account_status.SCS_Account_Nmbr = scs_account_info.SCS_Account_Nmbr
				WHERE scs_account_address.SCS_Account_Address_Type = 2
				AND scs_account_status.SCS_Account_Stat_Active = 1
				AND scs_account_info.Latitude NOT IN ('-1', 0)
				AND scs_account_info.Longitude NOT IN ('-1', 0) ?p";

        // Select all the rows in the app_location_data table or last updated locations
        if ($getall)
        {
            $parsed_query = '';
        }
        else
        {
            $parsed_query = $conn->parse(" AND scs_account_status.SCS_Account_Stat_Last_Signal > ?s", $updatetime);
        }

        $result = $conn->query($sql, $parsed_query);
        if (!$result)
        {
            die('Invalid query: ' . $conn->connect_error);
        }

        $conn_local = new SafeMySQL();

        if ($result)
        {
            while ($row = $conn->fetch($result))
            {
				/*
                $query = "SELECT
							Diag_Scan_ID,
							Diag_date,
							I_MAC_ETH0,
							S_DEVICE_no_1_STATE,
							S_DEVICE_no_1_STATUS_BATTERY,
							S_DEVICE_no_1_STATUS_230V
							FROM rms_status_db WHERE Diag_Scan_ID = (SELECT MAX(Diag_Scan_ID) FROM rms_status_db 
							WHERE I_MAC_ETH0 = ?s)";

                $rms_status = $conn_local->getRow($query, $row['SCS_Account_CallerID_1']);*/

                $path_status = getPathStatus($row['SCS_Account_Stat_Connection_Path']);

                if ($path_status == 3)
                {
                    $conn_status = $this->getPathStatusTranslation(3);
                    $err_class = 'text-warning';
                }
                elseif ($path_status == 0)
                {
                    $conn_status = $this->getPathStatusTranslation(0);
                    $err_class = 'text-danger';
                }
                elseif ($path_status == 2)
                {
                    $conn_status = $this->getPathStatusTranslation(2);
                    $err_class = 'text-warning';
                }
				elseif ($path_status == 4)
                {
                    $conn_status = $this->getPathStatusTranslation(4);
                    $err_class = 'text-default';
                }
                else
                {
                    $conn_status = $this->getPathStatusTranslation(1);
                    $err_class = 'text-primary';
                }

                $device_status = '';
				/*
                if ($rms_status['S_DEVICE_no_1_STATUS_BATTERY'] == 'false' || $rms_status['S_DEVICE_no_1_STATUS_230V'] == 'false')
                {
                    if ($rms_status['S_DEVICE_no_1_STATUS_BATTERY'] == 'false')
                    {
                        $device_status .= '<br><b>' . LANG['connection']['batt_err'] . '</b>';
                    }
                    if ($rms_status['S_DEVICE_no_1_STATUS_230V'] == 'false')
                    {
                        $device_status .= '<br><b>' . LANG['connection']['230_err'] . '</b>';
                    }
                    $path_status = 2;
                }*/

                $is_letter = (ctype_alpha(substr(getCategory($row['SCS_Account_Nmbr']) , 0, 1)) == true) ? strtoupper(substr(getCategory($row['SCS_Account_Nmbr']) , 0, 1)) : strtoupper('A');
                $locs[$row['SCS_Account_Nmbr']] = array(
                    'path_status' => $path_status,
                    'icon' => URL_ROOT_IMG . '/GoogleMapsMarkers/' . $this->setMarker($path_status, $is_letter),
                    'lat' => $row['Latitude'],
                    'lng' => $row['Longitude'],
                    'category' => getCategory($row['SCS_Account_Nmbr']) 
                );
				if($setinfo)
				{
					$locs[$row['SCS_Account_Nmbr']]['info'] =  '<div><b>' . $row['SCS_Account_Address_Name'] . '</b><br>' . $row['SCS_Account_Address_Address'] . '<br><a class="text-info ' . $err_class . '" onclick="popupWindow(\'' . URL_ROOT . '/location/?' . $row['SCS_Account_Nmbr'] . '\', \'location\', 1980, 1080 ); return false;">#' . $row['SCS_Account_Nmbr'] . '</a><br><b>' . $conn_status . '</b>' . $device_status . '</div>';
				}

            }
            $locs['updatetime'] = date('YmdHis');
        }

        jsonArr($locs);
    }
	
    /**
     * Get server side list of SCS location status to be used in client side DataTable
     *
     * @param string $state - string to define the query. Possible states: active, problem, inactive
     * @return array|JSON
     */
    public function getList($state)
    {

        $db = new \PDO('mysql:host=' . SCS_DB_HOST . ';dbname=' . SCS_DB_NAME . ';charset=utf8', SCS_DB_USER, SCS_DB_PASS, array(
            \PDO::ATTR_PERSISTENT => true
        ));

        $clean_state = strtolower(preg_replace("/[^A-Za-z]/", "", $state));

        define('CLEAN_STATE', $clean_state);

        if ($clean_state == 'active')
        {
            $where = "SCS_Account_Stat_Active = 1 AND SCS_Account_Stat_Connection_Path LIKE '%1%'";
        }
        elseif ($clean_state == 'inactive')
        {
            $where = "SCS_Account_Stat_Active = 1 AND SCS_Account_Stat_Connection_Path IN ('????????')";
        }
        elseif ($clean_state == 'problem')
        {
            $where = "SCS_Account_Stat_Active = 1 AND SCS_Account_Stat_Connection_Path LIKE '%0%'";
        }

        $columns = array(
            array(
                'db' => "SCS_Account_Nmbr",
                'dt' => "DT_RowClass",
                'formatter' => function ($d, $row)
                {
                    //return "issue-info";
                    
                }
            ) ,
            array(
                'db' => "SCS_Account_Stat_Connection_Path",
                'dt' => 0,
                'formatter' => function ($d, $row)
                {

                    $path_status = getPathStatus($d);
                    if ($path_status == 3)
                    {
                        $conn_status = '<i class="fa fa-circle text-warning"></i> ' . LANG['connection']['prim'];
                    }
                    elseif ($path_status == 0)
                    {
                        $conn_status = '<i class="fa fa-circle text-danger"></i> ' . LANG['connection']['diss'];
                    }
                    elseif ($path_status == 2)
                    {
                        $conn_status = '<i class="fa fa-circle text-warning"></i> ' . LANG['connection']['back'];
                    }
					elseif ($path_status == 4)
                    {
                        $conn_status = '<i class="fa fa-circle text-default"></i> ' . LANG['connection']['nopath'];
                    }
                    else
                    {
                        $conn_status = '<i class="fa fa-circle text-navy"></i> ' . LANG['connection']['conn'];
                    }

                    return $conn_status;
                }
            ) ,
            array(
                'db' => "SCS_Account_Nmbr",
                'dt' => 1,
                'formatter' => function ($d, $row)
                {
                    $problem_link = (CLEAN_STATE == 'problem' || CLEAN_STATE == 'inactive') ? '&err' : '';
                    $path_status = getPathStatus($row[1]);

                    if ($path_status == 3)
                    {
                        $conn_class = 'text-default';
                    }
					elseif ($path_status == 4)
                    {
                        $conn_class = 'text-warning';
                    }
                    elseif ($path_status == 0)
                    {
                        $conn_class = 'text-danger';
                    }
                    elseif ($path_status == 2)
                    {
                        $conn_class = 'text-warning';
                    }
                    else
                    {
                        $conn_class = 'text-navy';
                    }

                    return '<a class="text-info ' . $conn_class . '" data-markerid="' . $d . '" onclick="popupWindow(\'' . URL_ROOT . '/location/?' . $d . $problem_link . '\', \'location\', 1980, 1080 ); return false;">' . $d . '</a>';
                }
            ) ,
            array(
                'db' => "SCS_Account_Name",
                'dt' => 2,
                'formatter' => function ($d, $row)
                {
                    $conn = new SafeMySQL(SCS_DB_CONN);

                    $latlong = $conn->getRow("SELECT
												scs_account_info.SCS_Account_Nmbr,
												scs_account_info.Latitude,
												scs_account_info.Longitude
												FROM scs_account_info
												WHERE scs_account_info.SCS_Account_Nmbr = ?s", $row[2]);

                    if ($latlong['Latitude'] != '-1' && $latlong['Longitude'] != '-1')
                    {
                        return '<a onclick="selectMarker(' . $row[2] . ');"><i class="fa fa-map-marker"></i></a> ' . $d;
                    }
                    else
                    {
                        return $d;
                    }

                }
            ) ,
            array(
                'db' => "SCS_Account_Stat_Last_Signal",
                'dt' => 3,
                'formatter' => function ($d, $row)
                {
                    if (!empty($d))
                    {
                        $last = '<small class="block text-muted"><i class="fa fa-clock-o"></i> ' . date('Y-m-d H:i:s', strtotime($d)) . '</small>';
                    }
                    else
                    {
                        $last = '';
                    }
                    return $last;
                }
            ) ,
            array(
                'db' => "SCS_Account_Stat_Connection_Path",
                'dt' => 4,
                'formatter' => function ($d, $row)
                {

                    $path_status = getPathStatus($d);

                    return (int)$path_status;
                }
            )
        );

        // Return JSON array
        jsonArr(SSP::complex($_GET, $db, 'scs_account_status', 'scs_account_nmbr', $columns, $whereResult = null, $whereAll = $where));

    }
	
    /**
     * Get server side list of RMS devices from local database to be used in client side DataTable
     *
     * @return array|JSON
     */
    public function getListRms()
    {
        $db = new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS, array(
            \PDO::ATTR_PERSISTENT => true
        ));

        $where = "S_DEVICE_no_1_STATUS_230V = 'false' OR  S_DEVICE_no_1_STATUS_BATTERY = 'false' GROUP BY D_account_code";

        $columns = array(
            array(
                'db' => "Diag_scan_id",
                'dt' => "DT_RowClass",
                'formatter' => function ($d, $row)
                {
                    //return "issue-info";
                    
                }
            ) ,
            array(
                'db' => "S_DEVICE_no_1_STATE",
                'dt' => 0,
                'formatter' => function ($d, $row)
                {

                    if ($d == '0' && ($row[6] == 'false' || $row[6] == '-'))
                    {
                        $conn_status = '<i class="fa fa-circle text-danger"></i> ' . LANG['connection']['multi_err'];
                    }
                    elseif (($row[5] == 'false' || $row[5] == '-') && ($row[6] == 'false' || $row[6] == '-'))
                    {
                        $conn_status = '<i class="fa fa-circle text-danger"></i> ' . LANG['connection']['multi_err'];
                    }
                    elseif ($d == '0' && ($row[5] == 'false' || $row[5] == '-'))
                    {
                        $conn_status = '<i class="fa fa-circle text-danger"></i> ' . LANG['connection']['multi_err'];
                    }
                    elseif ($row[6] == 'false' || $row[6] == '-')
                    {
                        $conn_status = '<i class="fa fa-circle text-warning"></i> ' . LANG['connection']['230_err'];
                    }
                    elseif ($row[5] == 'false' || $row[5] == '-')
                    {
                        $conn_status = '<i class="fa fa-circle text-warning"></i> ' . LANG['connection']['batt_err'];
                    }
                    elseif ($d == '0')
                    {
                        $conn_status = '<i class="fa fa-circle text-warning"></i> ' . LANG['connection']['main_err'];
                    }
                    else
                    {
                        $conn_status = '<i class="fa fa-circle text-danger"></i> ' . LANG['connection']['multi_err'];
                    }

                    return $conn_status;
                }
            ) ,
            array(
                'db' => "I_MAC_ETH0",
                'dt' => 1,
                'formatter' => function ($d, $row)
                {
                    $conn = new SafeMySQL(SCS_DB_CONN);
                    $account_code = $conn->getOne("SELECT SCS_Account_Nmbr FROM scs_account_info WHERE scs_account_info.SCS_Account_CallerID_1 LIKE ?s", '%'.$d.'%');
                    //return $location_name
                    return '<a class="text-info text-warning" data-markerid="' . $account_code . '" onclick="popupWindow(\'' . URL_ROOT . '/location/?' . $account_code . '&err\', \'location\', 1980, 1080, ); return false;">' . $account_code . '</a>';
                }
            ) ,
            array(
                'db' => "I_MAC_ETH0",
                'dt' => 2,
                'formatter' => function ($d, $row)
                {
                    $conn = new SafeMySQL(SCS_DB_CONN);

                    $location_name = $conn->getOne("SELECT
														scs_account_address.SCS_Account_Address_Name,
														scs_account_address.SCS_Account_Address_Address
													FROM scs_account_address
													INNER JOIN scs_account_info ON scs_account_address.SCS_Account_Nmbr = scs_account_info.SCS_Account_Nmbr
													WHERE scs_account_address.SCS_Account_Address_Type = 2
													AND scs_account_info.SCS_Account_CallerID_1 LIKE ?s", '%'.$d.'%');
                    return $location_name;
                }
            ) ,
            array(
                'db' => "Diag_date",
                'dt' => 3,
                'formatter' => function ($d, $row)
                {
                    if (!empty($d))
                    {
                        $last = '<small class="block text-muted"><i class="fa fa-clock-o"></i> ' . date('Y-m-d H:i:s', strtotime($d)) . '</small>';
                    }
                    else
                    {
                        $last = '';
                    }
                    return $last;
                }
            ) ,
            array(
                'db' => "S_DEVICE_no_1_STATUS_BATTERY",
                'dt' => 4
            ) ,
            array(
                'db' => "S_DEVICE_no_1_STATUS_230V",
                'dt' => 5
            )
        );
        // Return JSON array
        jsonArr(SSP::complex($_GET, $db, 'rms_status_db', 'Diag_scan_id', $columns, $whereResult = null, $whereAll = $where));

    }
    
	/**
     * Get SCS events counts from current week for 24h and current week
     *
     * @return array|JSON
     */
    public function getEventCount()
    {
        $year = date('Y');
        $week = date('W');
        $month = date('m');

        $db_name_this_week = 'events' . $year . '_' . $week;

        $scs_conn = $this->db_conn;
        $local_conn = new SafeMySQL();
        $conn = new SafeMySQL(array(
            'host' => SCS_DB_HOST,
            'user' => SCS_DB_USER,
            'pass' => SCS_DB_PASS,
            'db' => $db_name_this_week
        ));

        function percent($nieuweWaarde, $oudeWaarde)
        {
            if ($oudeWaarde != 0)
            {
                $percentage = round((($nieuweWaarde - $oudeWaarde) / $oudeWaarde) * 100, 0);
                if ($percentage > 0)
                {
                    return '<span class="text-danger">' . $percentage . '% <i class="fa fa-level-up"></i></span>';
                }
                elseif ($percentage == 0)
                {
                    return '<span>' . $percentage . '% </span> Sinds laatste week';
                }
                else
                {
                    return '<span class="text-navy">' . $percentage . '% <i class="fa fa-level-down"></i></span>';
                }
            }
            else
            {
                return '<span> 0% </span>';
            }
        }

        $count_this_day = $conn->getOne("SELECT COUNT(*) FROM `event_received` WHERE DATE(`DateTime`) > DATE_SUB(CURDATE(), INTERVAL 1 DAY);");
        $count_past_day = $conn->getOne("SELECT COUNT(*) FROM `event_received` WHERE DATE(`DateTime`) > DATE_SUB(CURDATE(), INTERVAL 2 DAY);");

        $last_week = date("W", strtotime("-1 week"));
        $db_name_past_week = 'events' . $year . '_' . $last_week;

        $conn_past_week = new SafeMySQL(array(
            'host' => SCS_DB_HOST,
            'user' => SCS_DB_USER,
            'pass' => SCS_DB_PASS,
            'db' => $db_name_past_week
        ));
        $count_this_week = $conn->getOne("SELECT COUNT(*) FROM `event_received`;");
        $count_past_week = $conn_past_week->getOne("SELECT COUNT(*) FROM `event_received`;");

        $response_array = array(
            'day' => ['count' => $count_this_day,
            'past' => $count_past_day - $count_this_day,
            'percent' => percent($count_this_day, $count_past_day - $count_this_day) ],
            'week' => ['count' => $count_this_week,
            'past' => $count_past_week - $count_this_week,
            'percent' => percent($count_this_week, $count_past_week - $count_this_week) ]
            //'scs_active_count' => $scs_conn->getOne("SELECT COUNT(*) FROM scs_account_status WHERE SCS_Account_Stat_Active = 1 AND SCS_Account_Stat_Connection_Path LIKE '%1%'"),
            //'scs_problem_count' => $scs_conn->getOne("SELECT COUNT(*) FROM scs_account_status WHERE SCS_Account_Stat_Active = 1 AND SCS_Account_Stat_Connection_Path LIKE '%0%'"),
            //'scs_inactive_count' => $scs_conn->getOne("SELECT COUNT(*) FROM scs_account_status WHERE SCS_Account_Stat_Active = 1 AND SCS_Account_Stat_Connection_Path IN ('????????','????')"),
            //'rms_storing_count' => $local_conn->getOne("SELECT COUNT(*) FROM rms_status_db  WHERE Diag_Scan_ID IN (SELECT MAX(Diag_Scan_ID) AS id FROM rms_status_db  GROUP BY `I_MAC_ETH0`) AND (S_DEVICE_no_1_STATUS_230V != 'true' OR S_DEVICE_no_1_STATUS_BATTERY != 'true')"),
            
        );

        jsonArr($response_array);
    }
    
	/**
     * Get all divs used to display in maps tools dashboard. Makes calls to multiple set methods
     *
     * @param boolean $getall - get complete dataset of get changed dataset since $updatetime
     * @param string $updatetime - datetime string used to query locations changed since last update time
     * @return array|JSON
     */
    public function getMarkersDiv()
    {
        $locations = $this->setMarkersDivLocations();
        $location_count = $this->setMarkersDivCount();
        $grouped_locations = $this->setMarkersGrouped();

        if ($grouped_locations)
        {
            $locs['status'] = 1;
            $locs['updatetime'] = date('YmdHis');

            $locs['locations'] = $locations;

            $locs['count'] = array(
                'conn' => ' <b>' . $location_count['connected'] . '</b>',
                'diss' => ' <b>' . $location_count['disconnected'] . '</b>',
                'prim' => ' <b>' . $location_count['primair'] . '</b>',
                'back' => ' <b>' . $location_count['backup'] . '</b>',
                'nopath' => ' <b>' . $location_count['nopath'] . '</b>'
            );

            $locs['grouped'] = $grouped_locations['blocks'];
            $locs['arr'] = $grouped_locations['arr'];

        }
        else
        {
            $locs['updatetime'] = date('YmdHis');
            $locs['status'] = 0;
        }

        jsonArr($locs);
    }
    
	/**
     * Get multidimensional array containing all locations from sql result with path status, group name and SCS nr
     *
     * @return multidimensional array|locations=>array|path,group,id
     */
    protected function setMarkersStatusArray()
    {
        $conn = $this->db_conn;

        $datetime = date('YmdHis', strtotime('now'));

        // Initial query
        $sql = "SELECT
					scs_account_status.SCS_Account_Group,
					scs_account_address.SCS_Account_Nmbr,
					scs_account_address.SCS_Account_Address_Name,
					scs_account_address.SCS_Account_Address_Address,
					scs_account_address.SCS_Account_Address_Zip,
					scs_account_address.SCS_Account_Address_City,
					scs_account_status.SCS_Account_Stat_Connection_Path,
					scs_account_status.SCS_Account_Stat_Last_Signal,
					scs_account_status.SCS_Account_Stat_Active,
					scs_account_info.SCS_Account_CallerID_1,
					scs_account_info.Latitude,
					scs_account_info.Longitude
				FROM scs_account_address
				INNER JOIN scs_account_status ON scs_account_address.SCS_Account_Nmbr = scs_account_status.SCS_Account_Nmbr
				LEFT JOIN scs_account_info ON scs_account_status.SCS_Account_Nmbr = scs_account_info.SCS_Account_Nmbr
				WHERE scs_account_address.SCS_Account_Address_Type = 2
				AND scs_account_status.SCS_Account_Stat_Active = 1
				AND scs_account_info.Latitude NOT IN ('-1', 0)
				AND scs_account_info.Longitude NOT IN ('-1', 0)";

        $result = $conn->query($sql);

        $locs = array();

        if ($result)
        {

            while ($row = $conn->fetch($result))
            {
                $path_status = getPathStatus($row['SCS_Account_Stat_Connection_Path']);

                if ($path_status == 3)
                {
                    // IP disconnected
                    $locs['locations'][] = array(
                        'path' => 3,
                        'group' => $row['SCS_Account_Group'],
                        'id' => $row['SCS_Account_Nmbr']
                    );
                }
                elseif ($path_status == 0)
                {
                    // Disconnected
                    $locs['locations'][] = array(
                        'path' => 0,
                        'group' => $row['SCS_Account_Group'],
                        'id' => $row['SCS_Account_Nmbr']
                    );
                }
				elseif ($path_status == 4)
                {
                    // No path status
                    $locs['locations'][] = array(
                        'path' => 4,
                        'group' => $row['SCS_Account_Group'],
                        'id' => $row['SCS_Account_Nmbr']
                    );
                }
                elseif ($path_status == 2)
                {
                    // Backup disconnected
                    $locs['locations'][] = array(
                        'path' => 2,
                        'group' => $row['SCS_Account_Group'],
                        'id' => $row['SCS_Account_Nmbr']
                    );
                }
                else
                {
                    // Connected
                    $locs['locations'][] = array(
                        'path' => 1,
                        'group' => $row['SCS_Account_Group'],
                        'id' => $row['SCS_Account_Nmbr']
                    );
                }
            }
        }
        else
        {
            $locs['status'] = 0;
        }

        return $locs;
    }
    
	/**
     * Get multidimensional array containing all locations where path is defined in $path_arr
     * and the group name does not appear in the array from method setMarkersGrouped()
     *
     * @return multidimensional array|locs=>array|loc_conn,loc_group,loc_id
     */
    protected function setMarkersDivLocations()
    {
        $locations = $this->setMarkersStatusArray();
        $locations_arr = $locations['locations'];

        $grouped_locations = $this->setMarkersGrouped();
        $grouped_arr = $grouped_locations['arr'];

        $locs = array();
        foreach ($locations_arr as $key)
        {
            // Return locations if path appears in $path_arr and group name does not appears in grouped array
            if (in_array($key['path'], $this->path_arr) && !array_key_exists($key['group'], $grouped_arr))
            {

                switch ($key['path'])
                {
                    case 0:
                        $conn_status = '<i class="fa fa-circle text-danger"></i> ' . $this->getPathStatusTranslation(0);
                    break;
                    case 1:
                        $conn_status = '<i class="fa fa-circle text-navy"></i> ' . $this->getPathStatusTranslation(1);
                    break;
                    case 2:
                        $conn_status = '<i class="fa fa-circle text-warning"></i> ' . $this->getPathStatusTranslation(2);
                    break;
                    case 3:
                        $conn_status = '<i class="fa fa-circle text-warning"></i> ' . $this->getPathStatusTranslation(3);
                    break;
					case 4:
                        $conn_status = '<i class="fa fa-circle text-default"></i> ' . $this->getPathStatusTranslation(4);
                    break;
                    default:
                        $conn_status = '';
                    break;
                }

                $locs[] = array(
                    'loc_conn' => $conn_status,
                    'loc_group' => $key['group'],
                    'loc_id' => $key['id']
                );

            }

        }

        return $locs;
    }
   
    /**
     * Get multidimensional array containing all locations grouped
     * based on the defined $path_arr and if the belong to the same group
     *
     * @return multidimensional array=>blocks,arr
     */
    protected function setMarkersGrouped()
    {
        // Get total location array
        $locations = $this->setMarkersStatusArray();
        $locations_arr = $locations['locations'];

        $group_arr = array();
        $blocks = '';
        // Check if grouped locations are enabled in env.ini
        if ($this->enableGroupedThreshold == true)
        {
            // Foreach location create group array
            foreach ($locations_arr as $key)
            {
                $group_arr[$key['group']] = array();
                $group_arr[$key['group']]['count'] = 0;
            }
            // Foreach location if group name equals group name and path appears in $path_arr
            // increment group count by 1
            foreach ($locations_arr as $key)
            {
                if ($key['group'] == $key['group'] && (in_array($key['path'], $this->path_arr)))
                {
                    $group_arr[$key['group']]['count'] += 1;
                    $group_arr[$key['group']]['key'] = $key['path'];
                }
            }

            // Foreach group define a blocked based on group settings defined in env.ini
            foreach ($group_arr as $key => $val)
            {
                // if group count is equal or greater than $groupedThreshold defined in env.ini
                // create block
                if ($val['count'] >= $this->groupedThreshold)
                {
                    if ($val['count'] > $this->groupedThresholdDanger)
                    {
                        $event_class = 'red-bg';
                        $event_icon = 'fa fa-minus-circle';
                    }
                    elseif ($val['count'] > $this->groupedThresholdWarning)
                    {
                        $event_class = 'yellow-bg';
                        $event_icon = 'fa fa-warning';
                    }
                    elseif ($val['count'] >= $this->groupedThreshold)
                    {
                        $event_class = 'blue-bg';
                        $event_icon = 'fa fa-info-circle';
                    }
                    else
                    {
                        $event_class = 'blue-bg';
                        $event_icon = 'fa fa-info-circle';
                    }

                    $event_text = $val;

                    $blocks .= '<div class="widget ' . $event_class . ' p-sm text-center">
							<div class="m-b-md">
								<i class="' . $event_icon . ' fa-3x"></i>
								<h1 class="m-xs">' . $val['count'] . '</h1>
								<h3 class="font-bold no-margins">
									' . substr($key, 0, 23) . '
								</h3>
								<small>' . $this->getPathStatusTranslation($val['key']) . '</small>
							</div>
						</div>';

                }
                else
                {
                    // if group count is lesser than $groupedThreshold remove group from group array
                    unset($group_arr[$key]);
                    // and define emtpy block
                    $blocks .= '';
                }

            }
        }

        return array(
            'blocks' => $blocks,
            'arr' => $group_arr
        );

    }
    
	/**
     * Get total count value based on locations in setMarkersStatusArray() method
     *
     * @return array=>connected, disconnected, backup, nopath
     */
    protected function setMarkersDivCount()
    {
        $locations = $this->setMarkersStatusArray();
        $locations_arr = $locations['locations'];

        $stat_nopath = 0;
        $stat_diss = 0;
        $stat_prim = 0;
        $stat_back = 0;
        $stat_conn = 0;

        foreach ($locations_arr as $key)
        {
            switch ($key['path'])
            {
                case 0:
                    $conn_status = $this->getPathStatusTranslation(0);
                    $stat_diss += 1;
                break;
                case 1:
                    $conn_status = $this->getPathStatusTranslation(1);
                    $stat_conn += 1;
                break;
                case 2:
                    $conn_status = $this->getPathStatusTranslation(2);
                    $stat_back += 1;
                break;
                case 3:
                    $conn_status = $this->getPathStatusTranslation(3);
                    $stat_prim += 1;
                break;
				case 4:
                    $conn_status = $this->getPathStatusTranslation(4);
                    $stat_nopath += 1;
                break;
                default:
                    $conn_status = '';
                    $stat_conn += 0;
                break;
            }
        }
        return array(
            'connected' => $stat_conn,
            'disconnected' => $stat_diss,
            'primair' => $stat_prim,
            'backup' => $stat_back,
            'nopath' => $stat_nopath
        );
    }
    
	/**
     * Get path connection translation
     *
     * @param integer $path_nr - path status integer.
     * Possible values:
     * 		0 = disconnected,
     * 		1 = connected,
     * 		2 = backup disconnected,
     * 		3 = IP disconnected,
     * 		4 = no path defined,
     * @return string
     */
    protected function getPathStatusTranslation($path_nr)
    {
        switch ($path_nr)
        {
            case 0:
                return LANG['connection']['diss'];
            break;
            case 1:
                return LANG['connection']['conn'];
            break;
            case 2:
                return LANG['connection']['back'];
            break;
            case 3:
                return LANG['connection']['prim'];
            break;
            case 4:
                return LANG['connection']['nopath'];
            break;			
            default:
                return '';
            break;
        }
    }

	protected function setMarker($path, $first_char)
	{
		$err_icon = '';
		if($path == 0){
			$err_icon = 'red_Marker'.$first_char.'.png';
		} else if($path == 1){
			$err_icon = 'darkgreen_Marker'.$first_char.'.png';	
		} else if($path == 2){
			$err_icon = 'yellow_Marker'.$first_char.'.png';
		} else if($path == 3){
			$err_icon = 'orange_Marker'.$first_char.'.png';	
		} else if($path == 4){
			$err_icon = 'blue_Marker'.$first_char.'.png';					
		} else {
			$err_icon = 'brown_Marker'.$first_char.'.png';
		}
		return $err_icon;
	}
	
}

