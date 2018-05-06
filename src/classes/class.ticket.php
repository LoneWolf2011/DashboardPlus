<?php
/**
* Class and Function List:
* Function list:
* - __construct()
* - ticketSaveUpdate()
* - ticketCreateNew()
* - getTable()
* - setStatusText()
* - setStatus()
* - getTicketRow()
* Classes list:
* - Ticket
*/
class Ticket
{
    protected $db_conn;
    protected $pdf;
    protected $succesMessage;

    function __construct($db_conn)
    {
        $this->db_conn = $db_conn;
        $this->wb_link = URL_ROOT . "/view/ticket/ticket_view/?id=";
        $this->auth_user = htmlentities($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');
        // create new TCPDF document
        //$this->pdf 				= new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        //$this->mailer 			= new PHPmailer();
        
    }

    public function ticketSaveUpdate($post_val)
    {

        $conn = $this->db_conn;
        $row_wb = $this->getTicketRow($conn, $post_val['ID']);

        $updates_text_serial = stripslashes($row_wb['ticket_update_text']);
        // Roep status text functie aan; regelt de sub-labels in het user commentaar
        $status_text = $this->setStatusText($post_val, $row_wb);

        //Indien status geloten is worden de initialen van de sessie meegestuurd
        if ($post_val['status_update'] == "Gesloten")
        {
            $gesloten_door = $this->auth_user;
            $gesloten = 1;
            $date_gesloten = date("Y-m-d H:i:s");
        }
        else
        {
            $gesloten_door = "";
            $gesloten = 0;
            $date_gesloten = "0000-00-00 00:00:00";
        }

        $update_txt_query = array(
            'ticket_nr' => $row_wb['ticket_nr'],
            'ticket_update_text_label' => $status_text,
            'ticket_update_by' => $this->auth_user,
            'ticket_update_date' => date("Y-m-d H:i:s") ,
            'ticket_update_text' => strip_tags(ucfirst($post_val['extra_comment_update']))
        );

        // Key moeten hetzelfde zijn als de kolom namen uit de database.
        $query_data = array(
            'ticket_changed_date' => date("Y-m-d H:i:s") ,
            'ticket_changed_by' => $this->auth_user,
            'ticket_closed_date' => $date_gesloten,
            'ticket_closed_by' => $gesloten_door,
            'ticket_closed' => $gesloten,
            'ticket_external_ticket_nr' => empty($post_val['ticketnr']) ? $row_wb['ticket_external_ticket_nr'] : $post_val['ticketnr'],
            'ticket_extra_comment' => strip_tags($post_val['extra_comment_update']) ,
            'ticket_status' => $this->setStatus($post_val, $row_wb) ,
            'ticket_updates' => 1
        );

        // Indien status Doorzetten
        if ($post_val['status_update'] == "Geannuleerd")
        {
            $query_data['ticket_sub_status'] = $post_val['reden_geannuleerd'];
            $msg_type = 'success';
            $msg_title = 'Succes';
            $this->succesMessage .= "Werkbon geannuleerd omdat: <b>" . $post_val['reden_geannuleerd'] . "</b><br>";
        }

        // Indien status Doorzetten
        if ($post_val['status_update'] == "Doorzetten")
        {
            $query_data['ticket_extern'] = $post_val['doorzetten_naar'];
            $query_data['ticket_put_through'] = 1;
            $query_data['ticket_put_through_too'] = $post_val['doorzetten_naar'];
            $msg_type = 'success';
            $msg_title = 'Succes';
            $this->succesMessage .= "Werkbon doorgezet naar: <b>" . $post_val['doorzetten_naar'] . "</b><br>";
        }

        // Indien status on hold
        if ($post_val['status_update'] == "On hold")
        {
            $query_data['ticket_on_hold'] = 1;
            $query_data['ticket_date_on_hold'] = date('Y-m-d', strtotime($post_val['datum_on_hold']));
            $msg_type = 'success';
            $msg_title = 'Succes';
            $this->succesMessage .= "Werkbon on hold tot: <b>" . date('d-m-Y', strtotime($post_val['datum_on_hold'])) . "</b><br>";
        }
        else
        {
            $query_data['ticket_on_hold'] = 0;
        }

        // Indien een totaal uitval gesloten wordt stuur herstel mail naar brandweer
        if ($row_wb['ticket_status'] != "Gesloten" && $post_val['status_update'] == "Gesloten" && $post_val['totaal_uitval'] == 1)
        {
            // Send mail naar brandweer wanneer totaal uitval
            //$send_mail_brandweer = $this->mailBw($row_wb, $query_data, 1);
            //$this->succesMessage .= $send_mail_brandweer['succesmessage'];
            
        }

        // Als de bon status 'gesloten' is voer de query NIET uit
        if ($row_wb['ticket_status'] == 'Gesloten' && $post_val['status_update'] == 'Gesloten')
        {
            // Denied message als de bon status gesloten is, refreshed page na 5 sec
            $msg_type = 'error';
            $msg_title = 'DENIED';
            $this->succesMessage .= "De bon is al gesloten. Updates worden niet meer opgeslagen.<br>Open de bon opnieuw indien nodig.<br>";

        }
        else
        {

            $name = $conn->getOne("SELECT * FROM app_customer_tickets WHERE ticket_id = ?i", $post_val['ID']);

            if ($name)
            {
                $conn->query("UPDATE app_customer_tickets SET ?u  WHERE ticket_id = ?i", $query_data, $post_val['ID']);
                $conn->query("INSERT INTO app_customer_tickets_updates SET ?u", $update_txt_query);
                $msg_type = 'success';
                $msg_title = 'Succes';
                $this->succesMessage .= "Succesvol werkbon <b>ASB-WB" . $post_val['ID'] . "</b> geupdatet";

            }
            else
            {
                // Kan alleen bestaande updaten
                // TO DO: Indien er toch opgeslagen wordt geef error weer.
                die();
            }

        }

        if ($post_val['status_update'] == "Geannuleerd")
        {
            // Send mail naar aanvrager
            //$send_mailAanvrager = $this->mailAanvrager($row_wb, $query_data);
            //$this->succesMessage .= $send_mailAanvrager['succesmessage'];
            
        }

        // Log to file
        $msg = "WerkbonID: " . $post_val['ID'] . ". " . $row_wb['ticket_customer_scsnr'] . " geupdatet door: " . $query_data['ticket_changed_by'] . " Status: " . $post_val['status_update'];
        $err_lvl = 0;
        // JSON response
        $response_array['type'] = $msg_type;
        $response_array['title'] = $msg_title;
        $response_array['body'] = $this->succesMessage;
        logToFile(__FILE__, $err_lvl, $msg);

        // Return JSON array
        jsonArr($response_array);
    }

    public function ticketCreateNew($post_val)
    {

        $conn = $this->db_conn;
        // Indien werkbon voor KPN zet de status op Open
        $status = ($post_val['extern'] == 'KPN') ? "Open" : "Open";

        if ($post_val['totaal_uitval'] == 1)
        {
            $status = 'Totaal uitval';
        }

        // Key moeten hetzelfde zijn als de kolom namen uit de database.
        $query_data = array(
            'ticket_customer_scs' => substr(($post_val['OMS']) , -6) ,
            'ticket_customer_scsnr' => $post_val['OMS'],
            'ticket_created_by' => $this->auth_user,
            'ticket_extern' => $post_val['extern'],
            'ticket_service' => ucfirst($post_val['dienst']) ,
            'ticket_customer_region' => $post_val['regio'],
            'ticket_created_date' => date("Y-m-d H:i:s") ,
            'ticket_date' => date("D d F Y H:i:s") ,
            'ticket_customer_location' => $post_val['locatie'],
            'ticket_customer_address' => $post_val['adres'],
            'ticket_customer_zipcode' => $post_val['postcode'],
            'ticket_customer_city' => $post_val['plaats'],
            'ticket_failure' => ucfirst($post_val['storing']) ,
            'ticket_action' => ucfirst($post_val['actie']) ,
            'ticket_cp' => ucfirst($post_val['cp']) ,
            'ticket_cp_tel' => $post_val['cptel'],
            'ticket_status' => $status,
            'ticket_filename' => "WB_" . $post_val['extern'] . "_" . $post_val['dienst'] . "_" . $post_val['OMS'] . "_" . date("dmy.Hi") . ".txt",
            'ticket_comment' => ucfirst($post_val['comment'])
        );

        if ($post_val['totaal_uitval'] == 1)
        {
            $query_data['ticket_total_failure'] = 1;
        }

        //if($post_val['extern'] == 'KPN' || $post_val['extern'] == 'ACCI') {
        //	$query_data['ticket_customer_serviceid']		= $post_val['serviceid'];
        //	if($post_val['extern'] == 'KPN') {
        //		$query_data['ticket_external_ticket_nr']		= $post_val['ticketnr'];
        //	}
        //} else {
        //	$query_data['ticket_customer_serviceid']		= "";
        //}
        $conn->query("INSERT INTO app_customer_tickets SET ?u", $query_data);

        // ID dat wordt toegekend aan de nieuwe regel in de database
        $last_id = $conn->insertId();
        // Ticket referentie nr
        $wb_id = "ASB-WB" . $last_id;

        // Opslaan van het referentie nummer in de database
        $conn->query("UPDATE app_customer_tickets SET ticket_nr = ?s  WHERE ticket_id = ?i ", $wb_id, $last_id);
        // Create txt file
        //$filename 	= $this->createTxtFile($post_val, $wb_id);
        if ($conn->affectedRows() == 1)
        {
            // Log to file
            $msg = "Nieuwe werkbon voor: " . $query_data['ticket_customer_scsnr'] . " aangemaakt door: " . $query_data['ticket_created_by'] . ".";
            $err_lvl = 0;
            $response_array['type'] = 'success';
            $response_array['title'] = 'Success';
            $response_array['body'] = '<Strong>Check werkbon: </strong><br><a class="link" href="' . $this->wb_link . $wb_id . '">Werkbon refnr: ' . $wb_id . '</a><br/>Vergeet de inhoud van de werkbon niet te kopieren naar een SCS werkbon';
            //$response_array['href'] 	= URL_ROOT.'RMC/Werkbon/';
            //$response_array['button'] 	= '<a href="'.$this->wb_link.$last_id.'" class="btn btn-success" ><i class="fa fa-arrow-right fa-fw"></i> Ga naar bon</a>';
            
        }
        else
        {
            $msg = "Nieuwe werkbon voor: " . $query_data['ticket_customer_scsnr'] . " aangemaakt door: " . $query_data['ticket_created_by'] . ".";
            $err_lvl = 2;
            $response_array['type'] = 'error';
            $response_array['title'] = '<span class="glyphicon glyphicon-remove"></span> ERROR';
            $response_array['body'] = '<li>Werkbon niet aangemaakt!</li>';
            //$response_array['href'] 	= URL_ROOT.'RMC/Werkbon/';
            
        }

        logToFile(__FILE__, $err_lvl, $msg);

        // Return JSON array
        jsonArr($response_array);
    }

    public function getTable()
    {

        $db = new \PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS, array(
            \PDO::ATTR_PERSISTENT => true
        ));

        $columns = array(
            array(
                'db' => "ticket_id",
                'dt' => 'DT_RowClass',
                'formatter' => function ($d, $row)
                {
                    if ($row[10] == "Gesloten")
                    {
                        $tr_row = "alert-box success";
                    }
                    elseif ($row[10] == "Totaal uitval")
                    {
                        $tr_row = "alert-box danger";
                    }
                    elseif ($row[7] == "Geannuleerd")
                    {
                        $tr_row = "alert-box danger";
                        // 14 dagen
                        
                    }
                    elseif (strtotime($row[9]) < strtotime('-14 day'))
                    {
                        $tr_row = "alert-box danger";
                        // 5 dagen
                        
                    }
                    elseif (strtotime($row[9]) < strtotime('-5 day'))
                    {
                        $tr_row = "alert-box warning";
                    }
                    elseif ($row[7] == "Aangevraagd")
                    {
                        $tr_row = "alert-box info";
                    }
                    elseif ($row[7] == "On hold")
                    {
                        $tr_row = "alert-box open";
                    }
                    else
                    {
                        $tr_row = "alert-box open";
                    }
                    return $tr_row;
                }
            ) ,
            array(
                'db' => "ticket_nr",
                'dt' => 0,
                'formatter' => function ($d, $row)
                {
                    return "<a class='text-navy' href='" . $this->wb_link . $d . "' >" . $d . " </a>";
                }
            ) ,
            array(
                'db' => "ticket_customer_scsnr",
                'dt' => 1
            ) ,
            array(
                'db' => "ticket_customer_location",
                'dt' => 2,
                'formatter' => function ($d, $row)
                {
                    return stripslashes(substr($d, 0, 13)) . "...";
                }
            ) ,
            array(
                'db' => "ticket_customer_serviceid",
                'dt' => 3
            ) ,
            array(
                'db' => "ticket_service",
                'dt' => 4
            ) ,
            array(
                'db' => "ticket_extern",
                'dt' => 5
            ) ,
            array(
                'db' => "ticket_failure",
                'dt' => 6,
                'formatter' => function ($d, $row)
                {
                    return substr($d, 0, 13) . "...";
                }
            ) ,
            array(
                'db' => "ticket_external_ticket_nr",
                'dt' => 7
            ) ,
            array(
                'db' => "ticket_created_date",
                'dt' => 8
            ) ,

            array(
                'db' => "ticket_status",
                'dt' => 9,
                'formatter' => function ($d, $row)
                {
                    if ($d == "Gesloten")
                    {
                        $tr_row = "alert-box primary";
                        $colorstatus = 'label-default';
                        $title = "";
                        $status_txt = $d;
                    }
                    elseif ($d == "Totaal uitval")
                    {
                        $tr_row = "alert-box danger";
                        $colorstatus = "label-danger";
                        $title = "";
                        $status_txt = "Totaal uitval";
                    }
                    elseif ($d == "Geannuleerd")
                    {
                        $tr_row = "alert-box danger";
                        $colorstatus = 'label-danger';
                        $title = "";
                        $status_txt = $d;
                        // 14 dagen
                        
                    }
                    elseif (strtotime($row[9]) < strtotime('-14 day'))
                    {
                        $tr_row = "alert-box danger";
                        $colorstatus = 'label-danger';
                        $title = "Langer dan 14 dagen open";
                        if ($d == "Opnieuw verzonden" || $d == "Opnieuw geopend")
                        {
                            $status_txt = "Open";
                        }
                        else
                        {
                            $status_txt = $d;
                        }
                        // 5 dagen
                        
                    }
                    elseif (strtotime($row[9]) < strtotime('-5 day'))
                    {
                        $tr_row = "alert-box warning";
                        $colorstatus = 'label-warning';
                        $title = "Langer dan 5 dagen open";
                        if ($d == "Opnieuw verzonden" || $d == "Opnieuw geopend")
                        {
                            $status_txt = "Open";
                        }
                        else
                        {
                            $status_txt = $d;
                        }
                    }
                    elseif ($d == "Aangevraagd")
                    {
                        $tr_row = "alert-box info";
                        $colorstatus = 'label-info';
                        $title = "";
                        $status_txt = $d;
                    }
                    elseif ($d == "On hold")
                    {
                        $tr_row = "alert-box open";
                        $colorstatus = 'label-default';
                        $title = "";
                        $status_txt = $d;
                    }
                    else
                    {
                        $tr_row = "alert-box open";
                        $colorstatus = 'label-success';
                        $title = "";
                        $status_txt = "Open";
                    }

                    if ($row[11] == 1)
                    {
                        $submit_again = "<span class='label label-info'><span data-toggle='tooltip' title='Opnieuw verzonden' class='fa fa-envelope'></span></span>";
                    }
                    else
                    {
                        $submit_again = "";
                    }
                    if ($row[12] == 1)
                    {
                        $ingepland = "<span class='label label-default'><span data-toggle='tooltip' title='Ingepland' class='fa fa-calendar'></span></span>";
                    }
                    else
                    {
                        $ingepland = "";
                    }
                    return "<span data-toggle='tooltip' title='" . $title . "' class='label " . $colorstatus . "'>" . $status_txt . "</span> " . $submit_again . " " . $ingepland;
                }
            ) ,
            array(
                'db' => "ticket_submit_again",
                'dt' => 10
            ) ,
            array(
                'db' => "ticket_planned",
                'dt' => 11
            )
        );

        $date = date("Y-m-d", strtotime('-5 days'));

        if (isset($_GET['soort']))
        {
            $soort_actie = $_GET['soort'];
        }

        if ($soort_actie == "all")
        {
            $whereAll = null;
        }
        elseif ($soort_actie == "new")
        {
            $whereAll = "status IN ('Aangevraagd', 'Open', 'Totaal uitval', 'Opnieuw verzonden')";
        }
        elseif ($soort_actie == "close")
        {
            $whereAll = "status IN ('Gesloten')";
        }
        elseif ($soort_actie == "cancel")
        {
            $whereAll = "status IN ('Geannuleerd')";
        }
        elseif ($soort_actie == "kpn")
        {
            $whereAll = "extern = 'KPN' AND ticketnr IS NULL AND status IN ('Open','Opnieuw verzonden')";
        }
        elseif ($soort_actie == "hold")
        {
            $whereAll = "status = 'On hold' ";
        }
        elseif ($soort_actie == "actie")
        {
            $whereAll = "CAST(date_gewijzigd AS DATE) < '" . $date . "' AND status IN ('Open','Opnieuw verzonden') AND date_gewijzigd != '0000-00-00 00:00:00' AND extern != 'STRUKTON' AND extern NOT IN ('STRUKTON')";
        }
        else
        {
            $whereAll = null;
        }

        echo json_encode(SSP::complex($_GET, $db, 'app_customer_tickets', 'ticket_id', $columns, $whereResult = null, $whereAll = null));
    }

    protected function setStatusText($post_val, $row_wb = '')
    {
        $status_text = date("D d M Y H:i:s") . ' ';

        // Indien $ticketnr niet leeg wordt de post value van $ticketnr toegevoegd aan $status_text
        // voeg status status_text toe aan message.
        if (!empty($post_val['ticketnr']))
        {
            $status_text .= 'Ticketnr ' . strtolower($row_wb['ticket_external_ticket_nr']) . ': ' . $post_val['ticketnr'] . ' ';
        }

        if ($post_val['status_update'] == "On hold")
        {
            $status_text .= '<span class="badge badge-warning">Bon on hold tot ' . date('d-m-Y', strtotime($post_val['datum_on_hold'])) . '</span> ';
        }
        elseif ($post_val['status_update'] == "Doorzetten")
        {
            $status_text .= '<span class="badge badge-info">Bon doorgezet naar ' . $post_val['doorzetten_naar'] . '</span> ';
        }
        elseif ($post_val['status_update'] == "Opnieuw geopend")
        {
            $status_text .= '<span class="badge badge-info">Bon opnieuw geopend</span> ';
        }
        elseif ($post_val['status_update'] == 'Opnieuw verzonden')
        {
            $status_text .= '<span class="badge badge-info">Opnieuw verzonden naar ' . $row_wb['ticket_external_ticket_nr'] . '</span>';
        }
        elseif ($post_val['status_update'] == "Geannuleerd")
        {
            $status_text .= '<span class="badge badge-danger">Bon geannuleerd ' . $post_val['reden_geannuleerd'] . '</span> ';
        }
        elseif ($post_val['status_update'] == "Gesloten")
        {
            $status_text .= '<span class="badge badge-success">Bon gesloten</span> ';
        }
        elseif ($post_val['status_update'] == 'Open' && @$post_val['wb_soort'] == 'CONTROLE')
        {
            $status_text .= '<span class="badge badge-info">Verzonden naar ' . $row_wb['ticket_external_ticket_nr'] . '</span>';
        }
        else
        {
            $status_text .= '';
        }

        return $status_text;
    }

    protected function setStatus($post_val, $row_wb = '')
    {
        if ($post_val['status_update'] == "Geannuleerd")
        {
            $status = "Geannuleerd";
        }
        elseif ($post_val['status_update'] == "Gesloten")
        {
            $status = "Gesloten";
        }
        elseif ($post_val['totaal_uitval'] == 1)
        {
            $status = "Totaal uitval";
        }
        elseif ($post_val['status_update'] == "Doorzetten")
        {
            $status = "Open";
        }
        elseif ($post_val['status_update'] == "Opnieuw geopend" || $post_val['status_update'] == "Opnieuw verzonden")
        {
            $status = "Open";
        }
        elseif ($post_val['status_update'] == "Open (Not send)")
        {
            $status = "Open";
            // Log to file
            //$msg = "Werkbon: ".$row_wb['ticket_customer_scsnr'] ." controle zonder verzenden door: ".$row_wb['ticket_checked_by'];
            //logToFile(__FILE__,0,$msg);
            
        }
        else
        {
            $status = $post_val['status_update'];
        }
        return $status;
    }

    protected function getTicketRow($db_conn, $post_id)
    {
        return $db_conn->getRow("SELECT * FROM app_customer_tickets WHERE ticket_id = ?i", $post_id);
    }
}

