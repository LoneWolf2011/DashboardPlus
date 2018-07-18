	<?php 
		$wb_conn = new SafeMySQL();
		$search = preg_replace("/[^A-Z0-9-]/","", $_GET['id']);
		$row = $wb_conn->getRow("SELECT * FROM app_customer_tickets WHERE ticket_nr = ?s",$search);
		
		if($row['ticket_external_ticket_nr'] == "") {
			$ticket 		= "";
			$ticket_naam 	= "";
		} else {
			$ticket 		= $row['ticket_external_ticket_nr'];
			$ticket_naam 	= "Ticketnr:";    
		}			
		if ($row["ticket_submitted"] == 1) {
			$display 		= "display:none";
			$displaytext 	= "<strong>Werkbon is al een keer verzonden<br> Vraag de admin om het formulier handmatig te verzenden</strong>";
		} else {
			$display 		= "display:block";
			$displaytext 	= "";
		}
		if ($row["ticket_on_hold"] == 1 && $row["ticket_date_on_hold"] != '') {
			$display_hold 	= "display:block;";
		} else {
			$display_hold 	= "display:none;";
		}		
		if ($row["ticket_status"] == "Geannuleerd" && $row["ticket_sub_status"] != '') {
			$display_geannuleerd 	= "display:block;";
		} else {
			$display_geannuleerd 	= "display:none;";
		}

		$label_submit_again 	= ($row['ticket_submit_again'] == 1) ? '<span data-toggle="tooltip" title="Opnieuw verzonden" class="label label-info"><span class="fa fa-envelope"></span></span>' : '';	
		
		if (strtotime($row['ticket_created_date']) < strtotime('-14 day') && $row['ticket_status'] == "Open") {
			$tijd_text 			= "Staat meer dan 14 dagen open!";
			$color 				= 'label-danger';						
		} elseif (strtotime($row['ticket_created_date']) < strtotime('-5 day') && $row['ticket_status'] == "Open") {
			$tijd_text 			= "Staat meer dan 5 dagen open!";
			$color 				= 'label-warning';
		} elseif($row['ticket_status'] == "Open" || $row['ticket_status'] == "Opnieuw geopend" || $row['ticket_status'] == "Opnieuw verzonden") {
			$tijd_text 			= "Open";
			$color 				= 'label-success';									
		} elseif ($row['ticket_status'] == 'Gesloten'){
			$tijd_text 			= "Gesloten op ". $row['ticket_closed_date'];
			$color 				= 'label-default';
		} elseif($row['ticket_status'] == "Aangevraagd") {
			$tijd_text 			= "Aangevraagd";
			$color 				= 'label-info';
		} elseif($row['ticket_status'] == "Geannuleerd") {
			$tijd_text 			= "Geannuleerd reden: ".$row['ticket_sub_status'];
			$color 				= 'label-danger';			
		} elseif($row['ticket_status'] == "Totaal uitval" && $row['ticket_total_failure'] == 1) {
			$tijd_text 			= "Totaal uitval";
			$color 				= 'label-danger';
		} elseif($row['ticket_status'] == "On hold") {
			$tijd_text 			= "On hold tot ".date('d-m-Y', strtotime($row['ticket_date_on_hold']));
			$color 				= 'label-default';				
		} else {
			$tijd_text 			= "ONBEKENDE STATUS";
			$color 				= 'label-default';	
		}		
	?>
					<table width="100%">
					<tr>
						<td>
							<label for="OMS"><b data-i18n="[html]tickets.create.txt_scs">SCS nr</b></label> 
						</td>
						<td>
							<?= @$row['ticket_customer_scsnr']; ?>
						</td>
						<td>
							<label for="Dienst"><b data-i18n="[html]tickets.create.txt_service">Service:</b></label> 
						</td>
						<td>
							<?= @$row['ticket_service']; ?>
						</td>
					</tr>
					<tr>
						<td>
							<label for="locatie"><b data-i18n="[html]tickets.create.txt_location">Location name:</b></label>
						</td>
						<td>	
							<?= @$row['ticket_customer_location']; ?>
						</td>
						<td>
							<label for="adres"><b data-i18n="[html]tickets.create.txt_address">Address:</b></label>
						</td>
						<td>							
							<?= @$row['ticket_customer_address']; ?>
						</td>
					</tr>
					
					<tr>
						<td>
							<label for="postcode"><b data-i18n="[html]tickets.create.txt_zipcode">Zipcode:</b></label> 
						</td>
						<td>							
							<?= @$row['ticket_customer_zipcode']; ?>
						</td>
						<td>
							<label for="plaats"><b data-i18n="[html]tickets.create.txt_city">City:</b></label>
						</td>
						<td>							
							<?= @$row['ticket_customer_city']; ?>
						</td>
					</tr>	
					<tr><td><br></td></tr>
					<tr>
						<td colspan='4'>
							<div class="x_title">
								<h2><span data-i18n="[html]tickets.update.ticket_for"> Ticket for</span> <b><?= @$row['ticket_extern']; ?></b></h2>
							
								<div class="clearfix"></div>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<label for="Ticketnr"><b><?= $ticket_naam;?></b></label>
						</td>
						<td>
							<?= $ticket;?>
						</td>
					</tr>
						<tr><td><label for="storing"><b data-i18n="[html]tickets.create.txt_storing">Issue:</b></label></td><td colspan="3"><?= @$row['ticket_failure']; ?></td></tr>
						<tr><td><label for="actie"><b data-i18n="[html]tickets.create.txt_action">Actie:</b></label></td><td colspan="3"><?= @$row['ticket_action']; ?></td></tr>
					
						<tr><td><label for="cp"><b data-i18n="[html]tickets.create.txt_cp">Contactpersoon:</b></label></td><td><?= @$row['ticket_cp']; ?></td>
						<td><label for="cptel"><b data-i18n="[html]tickets.create.txt_cptel">Telefoonnr:</b></label></td><td><?= @$row['ticket_cp_tel']; ?></td></tr>

						<tr><td colspan="4"><label for="comment"><b data-i18n="[html]tickets.create.txt_comment">Extra comment:</b></label>
						<br><?= @$row['ticket_comment']; ?></td></tr>
						
					</table>
					<br>
					<h2 ><span data-i18n="[html]tickets.update.status">Status:</span> 
					<span class="badge-xl <?= $color;?> pull-right"><?= $tijd_text;?></span> <?= $label_submit_again;?></h2>
					<small class="pull-right">
						<i class="fa fa-clock-o"> </i>
						<span data-i18n="[html]location.tab.update">Last update </span>: <?= date('D d M Y, H:i:s',strtotime($row['ticket_changed_date'])); ?>
					</small>