 <?php
		$db_conn = new SafeMySQL(SCS_DB_CONN);
 			$result = $db_conn->query("SELECT
					scs_account_address.SCS_Account_Nmbr,
					scs_account_address.SCS_Account_Address_Name,
					scs_account_address.SCS_Account_Address_Address,
					scs_account_address.SCS_Account_Address_Zip,
					scs_account_address.SCS_Account_Address_City,
					scs_account_status.SCS_Account_Stat_Connection_Path,
					scs_account_status.SCS_Account_Stat_Active_DateTime,
					scs_account_status.SCS_Account_Stat_Last_Signal,
					scs_account_info.SCS_Account_CallerID_1,
					scs_account_info.SCS_Account_Receiver_1,
					scs_account_info.SCS_Account_Surveilance_Code,
					scs_account_info.SCS_Account_All_Okay_Word,
					scs_account_info.SCS_Account_serial,
					scs_account_info.SCS_account_SIM_card
				FROM scs_account_address
				INNER JOIN scs_account_status ON scs_account_address.SCS_Account_Nmbr = scs_account_status.SCS_Account_Nmbr
				LEFT JOIN scs_account_info ON scs_account_status.SCS_Account_Nmbr = scs_account_info.SCS_Account_Nmbr
				WHERE scs_account_address.SCS_Account_Address_Type = 2
				AND scs_account_status.SCS_Account_Stat_Active = 1
				AND scs_account_address.SCS_Account_Nmbr LIKE '%0101001051%'");
			
			while ($line = $db_conn->fetch($result)) {			
				$row = $line;
			};
	?>

    <div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
            <div class="col-md-6">
                <div class="ibox float-e-margins">
                  <div class="ibox-title">
				  <span class="pull-right"><span data-i18n="[html]tickets.update.submitted_by">Submitted by door</span>: <b>RGO</b> <span data-i18n="[html]tickets.update.checked_by">Checked by</span>: <b>RBL</b></span>
                               
                    <h2><a href="javascript:history.back()" class="text-primary"><i class="fa fa-arrow-left"></i> </a><span data-i18n="[html]tickets.update.label">Ticket</span> <b>#012</b> <small></small></h2>

                    <div class="clearfix"></div>
                  </div>
                  <div class="ibox-content">
					<table width="100%">
					<tr>
						<td>
							<label for="OMS"><b data-i18n="[html]tickets.create.txt_scs">SCS nr</b></label> 
						</td>
						<td>
							<?= @$row['SCS_Account_Nmbr']; ?>
						</td>
						<td>
							<label for="Dienst"><b data-i18n="[html]tickets.create.txt_scs">Service:</b></label> 
						</td>
						<td>
							
						</td>
					</tr>
					<tr>
						<td>
							<label for="locatie"><b data-i18n="[html]tickets.create.txt_location">Location name:</b></label>
						</td>
						<td>	
							<?= @$row['SCS_Account_Address_Name']; ?>
						</td>
						<td>
							<label for="adres"><b data-i18n="[html]tickets.create.txt_address">Address:</b></label>
						</td>
						<td>							
							<?= @$row['SCS_Account_Address_Address']; ?>
						</td>
					</tr>
					
					<tr>
						<td>
							<label for="postcode"><b data-i18n="[html]tickets.create.txt_zipcode">Zipcode:</b></label> 
						</td>
						<td>							
							<?= @$row['SCS_Account_Address_Zip']; ?>
						</td>
						<td>
							<label for="plaats"><b data-i18n="[html]tickets.create.txt_city">City:</b></label>
						</td>
						<td>							
							<?= @$row['SCS_Account_Address_City']; ?>
						</td>
					</tr>	
					<tr><td><br></td></tr>
					<tr>
						<td colspan='4'>
							<div class="x_title">
								<h2><span data-i18n="[html]tickets.update.ticket_for"> Ticket for</span> <b>STRUKTON</b></h2>
							
								<div class="clearfix"></div>
							</div>
						</td>
					</tr>

						<tr><td><label for="storing"><b data-i18n="[html]tickets.create.txt_storing">Issue:</b></label></td><td colspan="3">Bateria do gerador de nevoeiro com defeito</td></tr>
						<tr><td><label for="actie"><b data-i18n="[html]tickets.create.txt_action">Actie:</b></label></td><td colspan="3">Substitua a bateria</td></tr>
					
						<tr><td><label for="cp"><b data-i18n="[html]tickets.create.txt_cp">Contactpersoon:</b></label></td><td>Chave no local</td>
						<td><label for="cptel"><b data-i18n="[html]tickets.create.txt_cptel">Telefoonnr:</b></label></td><td>0612345678</td></tr>

						<tr><td colspan="4"><label for="comment"><b data-i18n="[html]tickets.create.txt_comment">Extra comment:</b></label><br>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.</td></tr>
						
					</table>
					<br>
					<h2 ><span data-i18n="[html]tickets.update.status">Status:</span> <label class="label label-success" data-i18n="[html]tickets.state.pending">Pending</label></h2>
				
				  </div>
				
				  <div class="ibox chat-view">
                  <div class="ibox-title">
						<small class="pull-right">
							<i class="fa fa-clock-o"> </i>
							<span data-i18n="[html]location.tab.update">Last update </span> 26.01.18 18:39:23
						</small>				  
                    <h2><span data-i18n="[html]tickets.update.updates">Updates:</span><small></small></h2>
                    <div class="clearfix"></div>
                  </div>
						<div class="chat-discussion">

                            <div class="chat-message left">
                                <img class="message-avatar" src="<?= URL_ROOT_IMG . 'img_blue.jpg';?>" alt="">
                                <div class="message">
                                    <a class="message-author" href="#"> a.hof@asb.nl </a>
									<span class="message-date"> Mon Jan 26 2018 - 18:39:23 </span>
                                    <span class="message-content">
									Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                                    </span>
                                </div>
                            </div>
                            <div class="chat-message right">
                                <img class="message-avatar" src="<?= URL_ROOT_IMG . 'img_green.jpg';?>" alt="">
                                <div class="message">
                                    <a class="message-author" href="#"> admin@asb.nl </a>
                                    <span class="message-date">  Fri Jan 25 2018 - 11:12:36 </span>
                                    <span class="message-content">
									Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                                    </span>
                                </div>
                            </div>
                            <div class="chat-message right">
                                <img class="message-avatar" src="<?= URL_ROOT_IMG . 'img_green.jpg';?>" alt="">
                                <div class="message">
                                    <a class="message-author" href="#"> admin@asb.nl </a>
                                    <span class="message-date">  Fri Jan 25 2018 - 11:12:36 </span>
                                    <span class="message-content">
									Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                                    </span>
                                </div>
                            </div>
                            <div class="chat-message left">
                                <img class="message-avatar" src="<?= URL_ROOT_IMG . 'img_blue.jpg';?>" alt="">
                                <div class="message">
                                    <a class="message-author" href="#"> r.vangolen@asb.nl </a>
                                    <span class="message-date">  Fri Jan 25 2018 - 11:12:36 </span>
                                    <span class="message-content">
									Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                                    </span>
                                </div>
                            </div>
                            <div class="chat-message right">
                                <img class="message-avatar" src="<?= URL_ROOT_IMG . 'img_green.jpg';?>" alt="">
                                <div class="message">
                                    <a class="message-author" href="#"> admin@asb.nl </a>
                                    <span class="message-date">  Fri Jan 25 2018 - 11:12:36 </span>
                                    <span class="message-content">
									Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                                    </span>
                                </div>
                            </div>
                            <div class="chat-message left">
                                <img class="message-avatar" src="<?= URL_ROOT_IMG . 'img_blue.jpg';?>" alt="">
                                <div class="message">
                                    <a class="message-author" href="#"> n.simon@asb.nl </a>
                                    <span class="message-date">  Fri Jan 25 2018 - 11:12:36 </span>
                                    <span class="message-content">
									Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.
                                    </span>
                                </div>
                            </div>
                        </div>
				  </div>
				</div>						
            </div>						

            <div class="col-md-6">
                <div class="ibox float-e-margins">
				
					<div class="ibox-title">
					<h2><span data-i18n="[html]tickets.update.create.label">Update ticket</span><small></small></h2>
				
					<div class="clearfix"></div>
					</div>
					<div class="ibox-content">

					<form id="update_wb_form" >
				
						<div class="form-group">
							<label class="control-label" ><span data-i18n="[html]tickets.update.create.label">External ticketid:</span></label>			
							<input type="text" class="form-control" name="ticketnr"  data-i18n="[placeholder]tickets.create.placeholder">
						</div>
				
						<div class="form-group">
							<label class="control-label" ><span data-i18n="[html]tickets.update.create.label">Update comment:</span><font color='red'>*</font></label>			
							<textarea class="form-control" name="extra_comment_update" rows="5" cols="40" data-i18n="[placeholder]tickets.create.placeholder"></textarea>
						</div>
						
						<div class="form-group" >
							<label class="control-label"><span data-i18n="[html]tickets.update.create.label">Update status:</span><font color='red'>*</font></label>
							<select class="form-control selectpicker"  name="status_update" onchange='UPDATE(this.value);' >
								<option data-i18n="[html]tickets.create.dropdown">  </option>
								<optgroup label="Werkbon opties..."></option>
								<option value="Open">Open</option>
								<option value="On hold">On hold</option>
								<option value="Opnieuw geopend">Opnieuw openen</option>
								<option value="Opnieuw verzonden">Opnieuw verzenden</option>									
								<optgroup label="Werkbon acties..."></option>				
								<option value="Geannuleerd">Werkbon annuleren</option>									
								<!--<option value="Escaleren">Werkbon escaleren</option>-->									
								<option value="Doorzetten">Werkbon doorzetten</option>	
								<option value="Gesloten">Werkbon sluiten</option>				
							</select>
						</div>

							<a class="btn btn-primary" name="save_button" value="Verzenden" id="send"><i class='fa fa-save fa-fw'></i> <span data-i18n="[html]tickets.buttons.update">Update</span></a>
										
					</form>
					</div>
                
				</div>
			</div>
	     
		</div>
	</div>
		
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.$js.'"></script>';
		}		
	?>	
	
    <script>
    $(document).ready(function() {

        $('#send').click(function(){
            swal({
                title: "Succes!",
                text: "This is an demo, but your ticket would have been updated!",
                type: "success"
            });
        });
		$('.chat-discussion').slimScroll({
			height: '300px',
			railOpacity: 0.4,
			wheelStep: 10
		});	
	});
	</script>	