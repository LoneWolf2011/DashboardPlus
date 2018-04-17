<?php

class User
{
    protected $succesMessage;
    protected $env_file = ROOT_PATH.'/Mdb/env.ini';
    protected $msg;
    
    function __construct($db_conn)
    {
        $this->db_conn   = $db_conn;
        $this->locale    = json_decode(file_get_contents(URL_ROOT . '/Src/lang/' . APP_LANG . '.json'), true);
        $this->auth_user = htmlentities($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8');
    }
    
    public function newUser($post_val)
    {
        $lang = $this->locale;
        $conn = $this->db_conn;
        
        $check_email = $conn->getOne('SELECT user_email FROM app_users WHERE user_email = ?s', $post_val['user_email']);
        if ($check_email) {
            $response_array['type']  = 'warning';
            $response_array['title'] = 'Let op';
            $response_array['body']  = '<b>' . $post_val['user_email'] . '</b> bestaat al, kies een ander email adres';
            jsonArr($response_array);
        }
        
        // Generate random password
        $gen_password = genPassSeed(2);
        $hash         = password_hash($gen_password, PASSWORD_DEFAULT);
        
        $query_data = array(
            'user_name' => $post_val['user_name'],
            'user_last_name' => $post_val['user_last_name'],
            'user_email' => $post_val['user_email'],
            'user_password' => $hash,
            'user_role' => $post_val['user_role']
        );
        
        if ($conn->query("INSERT INTO app_users SET ?u", $query_data)) {
            
            // Note: use the key names specified in the email template as array key
            $email_template = array(
                'user_name' => $post_val['user_name'] . " " . $post_val['user_last_name'],
                'app_name' => APP_NAME,
                'login_link' => '<a class="link" href="' . URL_ROOT . '">' . APP_NAME . '</a>',
                'gen_password' => $gen_password
            );
            
            $mail = new PHPmailer();
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->Port = SMTP_PORT;
            $mail->AddAddress($post_val['user_email']);
            $mail->SetFrom(APP_EMAIL);
            $mail->Subject = "Welkom bij " . APP_TITLE;
            $mail->MsgHTML(setEmailTemplate($email_template, 'email.new_user.php'));
            $mail->WordWrap = 80;
            
            if ($mail->Send()) {
                $send = 'Login email verzonden naar: <b>' . $post_val['user_email'] . '</b>';
            } else {
                $send = 'Login email NIET verzonden naar: <b>' . $post_val['user_email'] . '</b>';
            }
            
            // Log to file
            $msg     = "Nieuwe user " . $post_val['user_email'] . " aangemaakt door " . $this->auth_user;
            $err_lvl = 0;
            
            $response_array['type']  = 'success';
            $response_array['title'] = 'Success';
            $response_array['body']  = $send;
            
        } else {
            $msg                     = "Nieuwe user " . $post_val['user_email'] . " niet aangemaakt ";
            $err_lvl                 = 2;
            $response_array['type']  = 'error';
            $response_array['title'] = 'ERROR';
            $response_array['body']  = 'User niet aangemaakt';
            
        }
        
        logToFile(__FILE__, $err_lvl, $msg);
        
        
        // Return JSON array
        jsonArr($response_array);
    }
    
    public function updateUser($post_val)
    {
        $lang = $this->locale;
        
        $conn = $this->db_conn;
        
        $query_data = array(
            'user_name' => $post_val['user_name'],
            'user_last_name' => $post_val['user_last_name'],
            'user_email' => $post_val['user_email']
        );
        if (isset($_POST['user_status']) && !empty($_POST['user_status'])) {
            $query_data['user_status'] = $post_val['user_status'];
        }
        if (isset($_POST['user_role']) && !empty($_POST['user_role'])) {
            $query_data['user_role'] = $post_val['user_role'];
        }
        
        if ($conn->query("UPDATE app_users SET ?u WHERE user_id = ?i", $query_data, $post_val['user_id'])) {
            // Log to file
            $msg     = "User " . $post_val['user_email'] . " geupdatet door " . $this->auth_user;
            $err_lvl = 0;
            
            $response_array['type']  = 'success';
            $response_array['title'] = 'Success';
            $response_array['body']  = 'User <b>' . $post_val['user_email'] . '</b> succesvol geupdatet';
            
        } else {
            $msg                     = "User " . $post_val['user_email'] . " niet geupdatet";
            $err_lvl                 = 2;
            $response_array['type']  = 'error';
            $response_array['title'] = 'ERROR';
            $response_array['body']  = 'User niet geupdatet, probeer opnieuw';
            
        }
        
        logToFile(__FILE__, $err_lvl, $msg);
        
        // Return JSON array
        jsonArr($response_array);
    }
    
    public function deleteUser($post_val)
    {
        $lang = $this->locale;
        
        $conn       = $this->db_conn;
        $user_email = $conn->getOne("SELECT user_email FROM app_users WHERE user_id = ?i", $post_val['user_id']);
        
			$token = getApiToken();
			$ch = curl_init('http://'.WEB_API.'/api/users'); // INITIALISE CURL
	
			$data = json_encode(array('userId'=>$post_val)); 
			$authorization = "Authorization: Bearer ".$token; // **Prepare Autorisation Token**
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization )); // **Inject Token into Header**
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			$result = curl_exec($ch);
			curl_close($ch);
			$res = json_decode($result, true);	
			
        if ($res) {
			
		$conn->query("DELETE FROM app_users WHERE user_id = ?i", $post_val['user_id']);
			
            // Log to file
            $msg     = "User " . $user_email . " verwijderd door " . $this->auth_user;
            $err_lvl = 0;
            
            $response_array['type']  = 'success';
            $response_array['title'] = 'Success';
            $response_array['body']  = 'User <b>' . $user_email . '</b> verwijderd '. implode(',',$res);
            
        } else {
            $msg                     = "Nieuwe werkbon voor: ";
            $err_lvl                 = 2;
            $response_array['type']  = 'error';
            $response_array['title'] = 'ERROR';
            $response_array['body']  = 'User niet verwijderd';
        }
        
        logToFile(__FILE__, $err_lvl, $msg);
        
        // Return JSON array
        jsonArr($response_array);
    }
    
    public function updateUserPass($conn, $post_val)
    {
        $lang = $this->locale;
        
        $email_post    = $post_val['email'];
        $password_post = $post_val['password'];
        $new_user      = 0;
        
        // Make sure the user entered a valid E-Mail address 
        if (!filter_var($email_post, FILTER_VALIDATE_EMAIL)) {
            $response_array['label'] = $lang['user']['acc_update']['msg']['err_email']['label'];
            $response_array['text']  = $lang['user']['acc_update']['msg']['err_email']['text'];
            $response_array['type']  = 'error';
            
            // Return JSON array
            jsonArr($response_array);
            //die("Invalid E-Mail Address"); 
        }
        
        // If the user is changing their E-Mail address, we need to make sure that 
        // the new value does not conflict with a value that is already in the system. 
        // If the user is not changing their E-Mail address this check is not needed. 
        if ($email_post != $this->auth_user) {
            // Define our SQL query 
            $query = " 
					SELECT 
						1 
					FROM app_users 
					WHERE 
						user_email = :email 
				";
            
            // Define our query parameter values 
            $query_params = array(
                ':email' => $email_post
            );
            
            try {
                // Execute the query 
                $stmt   = $conn->prepare($query);
                $result = $stmt->execute($query_params);
            }
            catch (PDOException $ex) {
                // Note: On a production website, you should not output $ex->getMessage(). 
                // It may provide an attacker with helpful information about your code.  
                $msg = 'Regel: ' . $ex->getLine() . ' Bestand: ' . $ex->getFile() . ' Error: ' . $ex->getMessage();
                logToFile(__FILE__, 1, $msg);
                
                $response_array['label'] = $lang['user']['acc_update']['msg']['err']['label'];
                $response_array['text']  = $lang['user']['acc_update']['msg']['err']['text'];
                $response_array['type']  = 'error';
                
                // Return JSON array
                jsonArr($response_array);
                die();
            }
            
            // Retrieve results (if any) 
            $row = $stmt->fetch();
            if ($row) {
                $response_array['label'] = $lang['user']['acc_update']['msg']['err_email_in_use']['label'];
                $response_array['text']  = $lang['user']['acc_update']['msg']['err_email_in_use']['text'];
                $response_array['type']  = 'error';
                // Return JSON array
                jsonArr($response_array);
                
            }
        }
        
        // If the user entered a new password, we need to hash it and generate a fresh salt 
        // for good measure. 
        if (!empty($password_post)) {
            $password = password_hash($password_post, PASSWORD_ARGON2I);
        } else {
            // If the user did not enter a new password we will not update their old one. 
            $password = null;
            //$salt = null; 
        }
        
        // Initial query parameter values 
        $query_params = array(
            ':email' => $email_post,
            ':user_id' => $_SESSION[SES_NAME]['user_id'],
            ':new_user' => $new_user
        );
        
        // If the user is changing their password, then we need parameter values 
        // for the new password hash and salt too. 
        if ($password !== null) {
            $query_params[':password'] = $password;
            //$query_params[':salt'] = $salt; 
        }
        
        // Note how this is only first half of the necessary update query.  We will dynamically 
        // construct the rest of it depending on whether or not the user is changing 
        // their password. 
        $query = " 
				UPDATE app_users 
				SET 
					user_email = :email, 
					user_new = :new_user 
			";
        
        // If the user is changing their password, then we extend the SQL query 
        // to include the password and salt columns and parameter tokens too. 
        if ($password !== null) {
            $query .= " 
					, user_password = :password 
				";
        }
        
        // Finally we finish the update query by specifying that we only wish 
        // to update the one record with for the current user. 
        $query .= " 
				WHERE 
					user_id = :user_id 
			";
        
        try {
            // Execute the query 
            $stmt   = $conn->prepare($query);
            $result = $stmt->execute($query_params);
            
            $msg = "Password van user: " . $this->auth_user . " gewijzigd";
            logToFile(__FILE__, 0, $msg);
            
            $this->succesMessage = $lang['user']['acc_update']['msg']['suc']['label'];
            $this->msg           = $lang['user']['acc_update']['msg']['suc']['text'];
        }
        catch (PDOException $ex) {
            // Note: On a production website, you should not output $ex->getMessage(). 
            // It may provide an attacker with helpful information about your code.  
            $msg = 'Regel: ' . $ex->getLine() . ' Bestand: ' . $ex->getFile() . ' Error: ' . $ex->getMessage();
            logToFile(__FILE__, 1, $msg);
            
            $response_array['label'] = $lang['user']['acc_update']['msg']['err']['label'];
            $response_array['text']  = $lang['user']['acc_update']['msg']['err']['text'];
            $response_array['type']  = 'error';
            
            // Return JSON array
            jsonArr($response_array);
        }
        
        // Now that the user's E-Mail address has changed, the data stored in the $_SESSION 
        // array is stale; we need to update it so that it is accurate.
        $_SESSION[SES_NAME]['user_email'] = $email_post;
        $_SESSION[SES_NAME]['user_new']   = $new_user;
        
        $response_array['label'] = $this->succesMessage;
        $response_array['text']  = $this->msg;
        $response_array['type']  = 'success';
        
        // Return JSON array
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
                'db' => "user_id",
                'dt' => 'DT_RowClass'
            ),
            array(
                'db' => "user_name",
                'dt' => 0,
                'formatter' => function($d, $row)
                {
                    $conn    = $this->db_conn;
                    // display users who were active in last 10 minutes  
                    $minutes = 480;
                    $t       = date('Y-m-d H:i:s', time() - $minutes * 60);
                    if ($conn->getOne("select 1 from app_users WHERE user_last_access > '" . $t . "' AND `user_name` = '" . $row[1] . "' AND `user_status` NOT IN ('Blocked')")) {
                        $active = '<i class="fa fa-circle text-navy"></i>';
                    } else {
                        $active = '<i class="fa fa-circle text-danger"></i>';
                    }
                    return $active . ' ' . $d . ' ' . $row[6];
                }
            ),
            array(
                'db' => "user_email",
                'dt' => 1
            ),
            array(
                'db' => "user_last_access",
                'dt' => 2
            ),
            array(
                'db' => "user_role",
                'dt' => 3,
                'formatter' => function($d, $row)
                {
                    $conn = $this->db_conn;
                    return $conn->getOne('SELECT role_name FROM app_role WHERE id = ?i', $d);
                }
            ),
            array(
                'db' => "user_status",
                'dt' => 4,
                'formatter' => function($d, $row)
                {
                    
                    if ($d == 'Active') {
                        $status = '<span class="label label-success">' . $d . '</span>';
                    } else {
                        $status = '<span class="label label-danger">' . $d . '</span>';
                    }
                    return $status;
                }
            ),
            array(
                'db' => "user_last_name",
                'dt' => 5,
                'formatter' => function($d, $row)
                {
                    $edit = "<a class='label label-success' href='" . URL_ROOT . "/view/users/?id=" . $row[0] . "' >Edit</a>";
                    $dele = "<a class='label label-danger' id='delete' value='" . $row[0] . "' rel='" . $row[2] . "' >Delete</a>";
                    return $edit . ' ' . $dele;
                }
            ),
            array(
                'db' => "user_last_name",
                'dt' => 6
            )
        );
        
        echo json_encode(SSP::complex($_GET, $db, 'app_users', 'user_id', $columns, $whereResult = null, $whereAll = null));
    }
    
}