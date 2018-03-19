<?php
	$wb_conn = new SafeMySQL();
	$search = preg_replace("/[^A-Z0-9-]/","", $_GET['id']);
	$row_updates = $wb_conn->getAll("SELECT * FROM app_customer_tickets_updates WHERE ticket_nr = ?s ORDER BY ticket_update_id DESC",$search);
	//print_r($row_updates);
	
	foreach($row_updates as $updates){
		
		if( $updates['ticket_update_by'] == htmlentities($_SESSION['db_user']['user_email'], ENT_QUOTES, 'UTF-8')){
			$position 	= 'right';
			$img 		= URL_ROOT_IMG.'img_green.jpg';
		}else {
			$position = 'left';
			$img 		= URL_ROOT_IMG.'img_blue.jpg';
		}
		echo '<div class="chat-message '.$position.'">
            <img class="message-avatar" src="'.$img.'" alt="">
            <div class="message">
                <a class="message-author" href="#"> '.$updates['ticket_update_by'].' </a>
				<span class="message-date"> '.stripslashes($updates['ticket_update_text_label']).' </span>
                <span class="message-content">
				'.nl2br(stripslashes($updates['ticket_update_text'])).'
                </span>
            </div>
        </div>';		
	}