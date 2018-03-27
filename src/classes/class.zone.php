<?php
	class Zone {
		protected $succesMessage;
		protected $zone_nr;
		protected $date_now_day;
		protected $date_past_day;
		
		function __construct($db_conn,$zone_nr='') {
			$this->db_conn 	= $db_conn;
			$this->locale 	= json_decode(file_get_contents(URL_ROOT.'/Src/lang/'.APP_LANG.'.json'), true);
			$this->zone_nr 	= preg_replace("/[^A-Za-z0-9]/","",$zone_nr);

			$this->date_now_day = date('Y-m-d H:i:s');
			$this->date_past_day = date('Y-m-d H:i:s', strtotime('-24 hours'));			
		}
		
		public function getZoneDetails(){
			$conn = $this->db_conn;
			$groups = $conn->getRow("SELECT `Group`, GROUP_CONCAT(`Name` SEPARATOR ';') AS devices FROM sensor_status WHERE `Group` = ?s", $this->zone_nr);
			
			$devices_arr = explode(';',$groups['devices']);
			
			if($groups){
				
				$devices_row = array();
				foreach($devices_arr as $devices){
						
					$zone_row = $conn->getRow("SELECT * FROM sensor_status WHERE `Name` = ?s",$devices);	
						
					$total_count = $conn->getOne("SELECT `Value` FROM sensor_events WHERE `Name` = ?s AND `Group` = ?s ORDER BY id DESC LIMIT 1", $zone_row['Name'], $this->zone_nr);
					$total_bg = ($total_count > C_MIN_DANGER) ? 'red-bg' : (($total_count > C_MIN_WARNING) ? 'yellow-bg' : 'dark-bg');
					
					if($zone_row['Status_request'] != 'OK'){
						$active = '<i class="fa fa-circle text-danger"></i>';
					} else {
						$active = '<i class="fa fa-circle text-navy"></i>';;
					}						
					
					$location = 
					'<tr><th>Serial</th><td>'.$zone_row['Serial_number'].'</tr></td>
					<tr><th>IP address</th><td>'.$zone_row['IP_address'].'</tr></td>
					<tr><th>Uptime</th><td>'.gmdate('H:i:s',$zone_row['Uptime']).'</tr></td>';	
					;				
					
					$devices_row[] = '<div class="row">
						<div class="col-lg-6">
							<div class="ibox float-e-margins">
								<div class="ibox-content">
									<table class="table table-hover">
										<thead>
											<tr>
												<th>Device name</th>
												<th>'.$active.' '.$zone_row['Name'].'</th>
											</tr>
										</thead>
										<tbody >'.$location.'</tbody>
									</table>
								</div>
							</div>			
						</div>		
			
						<div class="col-lg-3">
							<div class="widget '.$total_bg.' p-lg text-center">
								<div class="m-b-md" style="color:white;">
									<i class="fa fa-user fa-4x"></i>
									<h1 class="m-xs" >'.$total_count.'</h1>
									<h3 class="font-bold no-margins">
										Current count
									</h3>
									<small>_</small>
								</div>
							</div>
						</div>			
					
						<div class="col-lg-3">
							<div class="widget style1 dark-bg">
								<div class="row vertical-align">
									<div class="col-xs-8">
										<h2><i class="fa fa-user"></i> <small style="color:inherit;">max (24h)</small></h2>
									</div>
									<div class="col-xs-4 text-right">
										<h2 class="font-bold" >10</h2>
									</div>
								</div>
							</div>
							<div class="widget style1 dark-bg">
								<div class="row vertical-align">
									<div class="col-xs-8">
										<h2><i class="fa fa-user"></i> <small style="color:inherit;">min (24h)</small></h2>
									</div>
									<div class="col-xs-4 text-right">
										<h2 class="font-bold" >0</h2>
									</div>
								</div>
							</div>
							<div class="widget style1 dark-bg">
								<div class="row vertical-align">
									<div class="col-xs-8">
										<h2><i class="fa fa-user"></i> <small style="color:inherit;">avg (24h)</small></h2>
									</div>
									<div class="col-xs-4 text-right">
										<h2 class="font-bold" >6</h2>
									</div>
								</div>
							</div>				
						</div>
					</div>';
				}
				
				$response_array = array(
					'status'	=> 1,
					'devices'	=> $devices_row,
					'test'	=> $groups
				);
			} else {
				$response_array['status'] = 0;
			}
			// Return response_array
			return $response_array;					
		}
	}