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
				AND scs_account_address.SCS_Account_Nmbr LIKE '%".preg_replace("/[^0-9]/","", $_SERVER['QUERY_STRING'])."%'");
			
			while ($line = $db_conn->fetch($result)) {			
				$row = $line;
			};
	?>

	<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
				<div class="col-lg-6">
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<h5><span data-i18n="[html]tickets.create.label">Create ticket</span> <small>#<?= preg_replace("/[^0-9]/","", $_SERVER['QUERY_STRING']); ?></small></h5>
						</div>
						<div class="ibox-content">
							<div class="row">
						<form name="new_wb" id="NewWerkbonForm" >
		
							<div class="col-md-12">
								<div class="ln_solid"></div>
							</div>
							
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label" for="first-name"><span data-i18n="[html]tickets.create.txt_scs">SCS nr</span><font color="red">*</font></label>
									<input type="text" class="form-control" name="OMS" value="<?= preg_replace("/[^0-9]/","", $_SERVER['QUERY_STRING']); ?>"  id="OMSNR" data-inputmask="'mask': '99-99-999999'" maxlength="12" data-i18n="[placeholder]tickets.create.placeholder" >
								</div>									
							</div>									
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label" for="first-name"><span data-i18n="[html]tickets.create.txt_service">Dienst</span><font color="red">*</font></label>
									<input type="text" class="form-control" name="dienst" id="DIENST"  data-i18n="[placeholder]tickets.create.placeholder"   >
								</div>								
							</div>								
	
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label" for="first-name"><span data-i18n="[html]tickets.create.txt_location">Locatie</span><font color="red">*</font></label>
									<input type="text" class="form-control" name="locatie" id="LOCATIE"  data-i18n="[placeholder]tickets.create.placeholder" value="<?= @$row['SCS_Account_Address_Name']; ?>">
								</div>									
							</div>									
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label" for="first-name"><span data-i18n="[html]tickets.create.txt_address">Adres</span><font color="red">*</font></label>
									<input type="text" class="form-control" name="adres"  id="ADRES" data-i18n="[placeholder]tickets.create.placeholder"  value="<?= @$row['SCS_Account_Address_Address']; ?>">
								</div>								
							</div>							
	
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label" ><span data-i18n="[html]tickets.create.txt_zipcode">Postcode</span><font color="red">*</font></label>
									<input type="text" class="form-control" name="postcode" id="POSTCODE"  data-i18n="[placeholder]tickets.create.placeholder" value="<?= @$row['SCS_Account_Address_Zip']; ?>">
								</div>									
							</div>									
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label"><span data-i18n="[html]tickets.create.txt_city">Plaats</span><font color="red">*</font></label>
									<input type="text" class="form-control" name="plaats" id="PLAATS" data-i18n="[placeholder]tickets.create.placeholder" value="<?= @$row['SCS_Account_Address_City']; ?>">
								</div>								
							</div>							
							<div class="col-md-12">
								<div class="ln_solid"></div>
							</div>
							<div id="txt"></div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label" ><span data-i18n="[html]tickets.create.txt_for">Werkbon voor</span><font color="red">*</font></label>
									<select id="voor" name="extern" class="form-control"  onchange="STATUS(this.value)">
										<option value="" data-i18n="[html]tickets.create.dropdown">Selecteer...</option>
										<option value="KPN">KPN</option>
										<option value="ASB">ASB</option>
										<option value="STRUKTON">STRUKTON</option>
										<option value="ACCI">ACCI</option>
									</select>
								</div>									
							</div>									
							
							<div class="clearfix"></div>
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label" ><span data-i18n="[html]tickets.create.txt_storing">Storing</span><font color="red">*</font></label>
									<div id="storing_wijzigen" > 				
										<select name="storing" class="form-control" onchange="Storing(this.value)" >
											<option value="" data-i18n="[html]tickets.create.dropdown">Selecteer...</option>	
											<option value="Geen lijnsync">Geen lijnsync</option>	
											<option value="GPRS uitval">GPRS uitval</option>	
											<option value="RAM uitval">RAM uitval</option>	
											<option value="ACCU uitval">ACCU uitval</option>	
											<option value="Voeding uitval">Voeding uitval</option>	
											<option value="Router uitval">Router uitval</option>	
											<option value="NVR comm uitval">NVR comm uitval</option>	
											<option value="Totale uitval">Totale uitval</option>	
											<option value="anders">Anders</option>	
										</select>
									</div><span id="searchclear" style="display:none;" class="fa fa-remove"></span>
								</div>									
							</div>									
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label" ><span data-i18n="[html]tickets.create.txt_action">Actie</span><font color="red">*</font></label>
									<input type="text" class="form-control"	name="actie" data-i18n="[placeholder]tickets.create.placeholder">
								</div>								
							</div>	
	
							<div class="col-md-6" id="CP" style="display:block">
								<div class="form-group">
									<label class="control-label"><span data-i18n="[html]tickets.create.txt_cp">Contactpersoon</span><font color="red">*</font></label>
									<input type="text" class="form-control" name="cp" id="CP_input" data-i18n="[placeholder]tickets.create.placeholder">
								</div>									
							</div>									
							<div class="col-md-6" id="CPTEL" style="display:block">
								<div class="form-group">
									<label class="control-label" ><span data-i18n="[html]tickets.create.txt_cptel">Contactpersoon telnr</span><font color="red">*</font></label>
									<input type="text" class="form-control" name="cptel" id="CPTEL_input" data-i18n="[placeholder]tickets.create.placeholder" >
								</div>								
							</div>							
							
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label"> <span data-i18n="[html]tickets.create.txt_comment">Extra commentaar</span></label>
									<textarea name="comment" class="resizable_textarea form-control"  data-i18n="[placeholder]tickets.create.placeholder"></textarea>
								</div>								
							</div>
									
							<div class="col-md-12">
								<div class="ln_solid"></div>
								<div class="form-group">
									<input type="text" hidden name="regio" id="REGIO" >
									<div id="div_send_button" style="display:block"> 
										<a class="btn btn-primary" name="save_button" value="Verzenden" id="send"><i class='fa fa-envelope fa-fw'></i> <span data-i18n="[html]tickets.buttons.send">Send</span></a>
									</div>	
										
								</div>
							</div>
	
						</form>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<h5><span data-i18n="[html]tickets.label"> Tickets</span> <small></small></h5>
						</div>
						<div class="ibox-content">
							<div class="table-responsive">
																			
								<table class="table table-hover datatable">
									<thead>
										<tr>
											<th data-i18n="[html]tickets.table.th1">TicketID</th>
											<th data-i18n="[html]tickets.table.th2">LocationID</th>
											<th data-i18n="[html]tickets.table.th3">Service</th>
											<th data-i18n="[html]tickets.table.th4">Last update</th>
											<th data-i18n="[html]tickets.table.th5">SLA</th>
											<th data-i18n="[html]tickets.table.th6">Status</th>
										</tr>
									</thead>
									<tbody>
										<tr >
											<td><a href="ticket_view/?012" class="text-info">#010</a></td>
											<td>0101001053</td>
											<td>ASB</td>
											<td>2018-02-24</td>
											<td><span class="pie">1/8</span> 8h</td>
											<td><label class="label label-success" data-i18n="[html]tickets.state.pending">Pending</label></td>
										</tr>									
										<tr>
											<td><a href="ticket_view/?012" class="text-info">#011</a></td>
											<td>0101001062</td>
											<td>ASB</td>
											<td>2018-02-24</td>
											<td><span class="pie">1.5/8</span> 8h</td>
											<td><label class="label label-success" data-i18n="[html]tickets.state.pending">Pending</label></td>
										</tr>									
										<tr>
											<td><a href="ticket_view/?012" class="text-info">#012</a></td>
											<td>0101001051</td>
											<td>STRUKTON</td>
											<td>2018-02-24</td>
											<td><span class="pie">3/8</span> 8h</td>
											<td><label class="label label-success" data-i18n="[html]tickets.state.pending">Pending</label></td>
										</tr>
										<tr class="alert-box warning">
											<td><a href="ticket_view/?012" class="text-info">#009</a></td>
											<td>0101001060</td>
											<td>ASB</td>
											<td>2018-02-24</td>
											<td><span class="pie">9/8</span> 8h +1</td>
											<td><label class="label label-warning" data-i18n="[html]tickets.state.pending">Pending</label></td>
										</tr>
										<tr class="alert-box warning">
											<td><a href="ticket_view/?012" class="text-info">#007</a></td>
											<td>0101001052</td>
											<td>ASB</td>
											<td>2018-02-23</td>
											<td><span class="pie">9/8</span> 8h +1</td>
											<td><label class="label label-warning" data-i18n="[html]tickets.state.pending">Pending</label></td>
										</tr>
										<tr class="alert-box danger">
											<td><a href="ticket_view/?012" class="text-info">#008</a></td>
											<td>0101001049</td>
											<td>ASB</td>
											<td>2018-02-23</td>
											<td><span class="pie">9/8</span> 8h +4</td>
											<td><label class="label label-danger" data-i18n="[html]tickets.state.pending">Pending</label></td>
										</tr>										
										<tr>
											<td><a href="ticket_view/?123" class="text-info">#123</a></td>
											<td>0101001064</td>
											<td>STRUKTON</td>
											<td>2018-02-19</td>
											<td><span class="pie" >6/8</span> 8h</td>
											<td><label class="label label-default" data-i18n="[html]tickets.state.closed">Closed</label></td>
										</tr>										
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>				
            </div>
        </div>
    </div>
	
	
	<?php
		// View specific scripts
		array_push($arr_js, '/mdb/js/plugins/dataTables/datatables.min.js');
		
	?>		
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
                text: "This is an demo, but your ticket would have been created!",
                type: "success"
            });
        });
	
		$.extend( true, $.fn.dataTable.defaults, {
			language: {
				url: '/mdb/js/plugins/dataTables/'+$('html').attr('lang')+'.json'
			},
			iDisplayLength: 10,
			deferRender: true,
			order: [[ 3, "desc"]],
			lengthMenu: [ 10, 20, 25 ]
		} );		
		
		
		$(".datatable").DataTable({
			fnInitComplete: function(oSettings, json) {
				$("span.pie").peity("pie", {
					fill: ['#1ab394', '#d7d7d7', '#ffffff']
				});
				var lang_code = $('html').attr('lang');
			
				$.i18n.init({
					resGetPath: '/mdb/src/lang/__lng__.json',
					load: 'unspecific',
					fallbackLng: false,
					lng: lang_code
				}, function (t){
					$('#i18container').i18n();
				});				
				
			}
		});
	});
	</script>