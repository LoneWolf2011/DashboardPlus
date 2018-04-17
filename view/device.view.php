	<?php 		array_push($arr_css, '/css/plugins/dataTables/datatables_responsive.min.css');?>
	<div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-5">
        
						<div class="row">
							<div class="col-lg-12">
								<div class="m-b-md">
									<h2>Device # <?= preg_replace("/[^0-9]/","", $_SERVER['QUERY_STRING']); ?></h2>
									<h3 id="location_name"></h3>
									<?php if(isset($_GET['err'])){ ?>
									<a href="<?= URL_ROOT;?>/view/ticket/?<?= preg_replace("/[^0-9]/","", $_SERVER['QUERY_STRING']); ?>"class="btn btn-success btn-xs" data-i18n="[html]tickets.create.label">Create ticket</a>
									<?php }; ?>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-5">
								<dl class="dl-horizontal">
									<dt data-i18n="[html]location.connection">Connection:</dt> <dd id="conn_status"></dd>
								</dl>
							</div>
							<div class="col-xs-7" id="cluster_info">
								<dl class="dl-horizontal" id="location_status"> </dl>				
											
							</div>						
						</div>
						<div class="row">
							<!--<div class="col-xs-12">
								<div class="col-lg-12">
									<dl class="dl-horizontal">					
										<dt data-i18n="[html]location.address">Address:</dt> <dd id="location_address"></dd>
										<dt data-i18n="[html]location.zipcode">Postalcode:</dt> <dd id="location_zip"></dd>
										<dt data-i18n="[html]location.city">City:</dt> <dd id="location_city"></dd>
									</dl>
								</div>
							</div>-->
							<div class="col-xs-12">
								<div class="col-xs-5">
									<dl class="dl-horizontal">
									<dl class="dl-horizontal" >
										<dt data-i18n="[html]location.mac">MAC:</dt> <dd id="location_mac"> </dd>
										<dt data-i18n="[html]location.udid">UDID:</dt> <dd id="location_udid"></dd>
										<dt data-i18n="[html]location.lijn">Line name:</dt> <dd id="location_lijn"></dd>
									</dl>
									</dl>
								</div>
								<div class="col-xs-7" id="cluster_info">
									<dl class="dl-horizontal" >
										<dt data-i18n="[html]location.serie">Serial nr:</dt> <dd id="location_serie"> </dd>
										<dt data-i18n="[html]location.first">First signal:</dt> <dd id="location_first"> </dd>
										<dt data-i18n="[html]location.last">Last seen:</dt> <dd id="location_last"></dd>
									</dl>				
								</div>						
							</div>				
						</div>				
						<h3 >Actions</h3>
						<div class="row" id="actions"></div>	
							
                    </div>
					
					<div class="col-lg-7">
						<script async defer src="https://maps.googleapis.com/maps/api/js?&key=<?= GOOGLE_API;?>"></script>
						<!--<script async defer src="Z:\google.js?sensor=false&key=<?= GOOGLE_API;?>"></script>-->

						<div class="google-map" id="map" style="height:350px;"></div>					
					</div>
					
				</div>
				<div class="row">
					<div class="col-lg-5">
						<div class="ibox float-e-margins" id="values" >
							<div class="ibox-title">
								<h5 data-i18n="[html]location.values.table.txt">Location values</h5>
							</div>
							<div class="ibox-content">
								<table class="table table-hover no-margins datatable_values" >
									<thead>
									<tr>
										<th data-i18n="[html]location.values.table.th1">Name</th>
										<th data-i18n="[html]location.values.table.th2">Value</th>
									</tr>
									</thead>
									<tbody id="table_rows"></tbody>
								</table>
							</div>
						</div>					
					</div>
                    <div class="col-lg-7">
						
						<div class="ibox float-e-margins" id="pie_chart">
							<div class="ibox-content">
								<div>
									<span class="pull-right text-right">
										<span data-i18n="[html]location.tab.location">Location</span>: <b><?= preg_replace("/[^0-9]/","", $_SERVER['QUERY_STRING']); ?></b>
										<br/>
										<span data-i18n="[html]location.events.count">Total event(s)</span>: <b><span id="events_count_week"></span></b>
									</span>
									<h3 class="font-bold no-margins" data-i18n="[html]location.events.label">
										Events this week
									</h3>
									<small><span data-i18n="[html]location.events.week">Week</span> #<?= date('W');?></small>
								</div>
								<div class="m-t-sm">
					
									<div class="row">
										<div class="col-lg-12">
										<table class="table table-hover jambo_table bulk_action datatable">
											<thead>
												<tr>
													<th >Code</th>
													<th >Zone</th>													
													<th >Status name</th>
													<th >Status value</th>
													<th >Text</th>
													<th >Datetime</th>
												</tr>
											</thead>
											<tbody><!--JSON RES--></tbody>
										</table>
										</div>
									</div>
					
								</div>
					
								<div class="m-t-md">
									<small class="pull-right">
										<i class="fa fa-clock-o"> </i>
										<span data-i18n="[html]location.tab.update">Updated on</span> <?= date('y.m.d H:i:s');?>
									</small>
									<small>
										<b></b> 
									</small>
								</div>
					
							</div>
						</div>			
							
                    </div>
                </div>
            </div>
        </div>	
		
    </div>
	
	<input type="text" hidden id="url_query" value="<?= $_SERVER['QUERY_STRING']; ?>" />
	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'/Src/controllers/device.controller.php';?>" />	
	<?php
		// View specific scripts
		array_push($arr_js, '/js/plugins/dataTables/datatables.min.js');
		array_push($arr_js, '/js/plugins/dataTables/datatables_responsive.min.js');
		
	?>		
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>	


	<script>
	$(document).ready(function () {
		var refresh = 5000;	
		
		var device_id = $('#url_query').val();
		var url_str = $('#url_string').val();
		var interval;
		
		getLocation();
		getLocationStatus();
		getLocationActions();

		$.extend( true, $.fn.dataTable.defaults, {
			language: {
				url: <?= json_encode(URL_ROOT);?>+'/js/plugins/dataTables/'+$('html').attr('lang')+'.json'
			},
			iDisplayLength: 10,
			deferRender: true,
			order: [[ 5, "desc"]],
			lengthMenu: [ 10, 20, 25 ],
			responsive: true
			
		} );		
	
		
		table_active = $(".datatable").DataTable({	
			ajax: url_str+"?get=table&id="+device_id,
			fnInitComplete: function(oSettings, json) {
				$('#ibox1').children('.ibox-content').toggleClass('sk-loading');
				clearInterval(interval);
				interval = setInterval( function () {
					table_active.ajax.reload( null, false ); 
				}, refresh );
				$.i18n.init({
					resGetPath: <?= json_encode(URL_ROOT);?>+'/src/lang/__lng__.json',
					load: 'unspecific',
					fallbackLng: false,
					lng: $('html').attr('lang')
				}, function (t){
					$('#i18container').i18n();
				});				
				
			}			
		});		
		
	});	

	// Global vars for maps
	var err_class;
	var err_icon;
	var err_conn;
	var err_txt;
	var url = <?= json_encode(URL_ROOT_IMG);?>+'/GoogleMapsMarkers/';
	var markers_arr = [];	
	var map;
	var markerCluster = null;
	var infoWindow = null;
	//var center = {lat: 51.467384, lng: 5.449035};	
	var center = {lat: <?= json_encode((int)APP_LAT) ;?>, lng: <?= json_encode((int)APP_LNG) ;?>};
				
	map = new google.maps.Map(document.getElementById('map'), {
			center: center,
			zoom: 8, 
		
			// Style for Google Maps
			//styles: [{"stylers":[{"hue":"#18a689"},{"visibility":"on"},{"invert_lightness":true},{"saturation":40},{"lightness":10}]}]
			styles: [
			{
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#242f3e"
				}
				]
			},
			{
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#746855"
				}
				]
			},
			{
				"elementType": "labels.text.stroke",
				"stylers": [
				{
					"color": "#242f3e"
				}
				]
			},
			{
				"featureType": "administrative.land_parcel",
				"stylers": [
				{
					"visibility": "off"
				}
				]
			},
			{
				"featureType": "administrative.locality",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#d59563"
				}
				]
			},
			{
				"featureType": "administrative.neighborhood",
				"stylers": [
				{
					"visibility": "off"
				}
				]
			},
			{
				"featureType": "poi",
				"elementType": "labels.text",
				"stylers": [
				{
					"visibility": "off"
				}
				]
			},
			{
				"featureType": "poi",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#d59563"
				}
				]
			},
			{
				"featureType": "poi.business",
				"stylers": [
				{
					"visibility": "off"
				}
				]
			},
			{
				"featureType": "poi.park",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#263c3f"
				}
				]
			},
			{
				"featureType": "poi.park",
				"elementType": "labels.text",
				"stylers": [
				{
					"visibility": "off"
				}
				]
			},
			{
				"featureType": "poi.park",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#6b9a76"
				}
				]
			},
			{
				"featureType": "road",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#38414e"
				}
				]
			},
			{
				"featureType": "road",
				"elementType": "geometry.stroke",
				"stylers": [
				{
					"color": "#212a37"
				}
				]
			},
			{
				"featureType": "road",
				"elementType": "labels",
				"stylers": [
				{
					"visibility": "off"
				}
				]
			},
			{
				"featureType": "road",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#9ca5b3"
				}
				]
			},
			{
				"featureType": "road.highway",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#746855"
				}
				]
			},
			{
				"featureType": "road.highway",
				"elementType": "geometry.stroke",
				"stylers": [
				{
					"color": "#1f2835"
				}
				]
			},
			{
				"featureType": "road.highway",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#f3d19c"
				}
				]
			},
			{
				"featureType": "transit",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#2f3948"
				}
				]
			},
			{
				"featureType": "transit.station",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#d59563"
				}
				]
			},
			{
				"featureType": "water",
				"elementType": "geometry",
				"stylers": [
				{
					"color": "#17263c"
				}
				]
			},
			{
				"featureType": "water",
				"elementType": "geometry.fill",
				"stylers": [
				{
					"color": "#539a4b"
				},
				{
					"saturation": -100
				}
				]
			},
			{
				"featureType": "water",
				"elementType": "labels.text",
				"stylers": [
				{
					"visibility": "off"
				}
				]
			},
			{
				"featureType": "water",
				"elementType": "labels.text.fill",
				"stylers": [
				{
					"color": "#515c6d"
				}
				]
			},
			{
				"featureType": "water",
				"elementType": "labels.text.stroke",
				"stylers": [
				{
					"color": "#17263c"
				}
				]
			}
			]
    });

	var infowindow = new google.maps.InfoWindow();		
	
	var device_id = $('#url_query').val();
	var url_str = $('#url_string').val();	
	
	
	function getLocation(){
		$.ajax({
			type: 'POST',
			url: url_str+"?get=location&id="+device_id,
			data: {ID: device_id},
			success: function(data) {
				if(data.path_status == 0){
					err_icon = url+'red_Marker'+data.first_char+'.png';
				} else if(data.path_status == 2){
					err_icon = url+'yellow_Marker'+data.first_char+'.png';
				} else if(data.path_status == 3){
					err_icon = url+'blue_Marker'+data.first_char+'.png';					
				} else {
					err_icon = url+'darkgreen_Marker'+data.first_char+'.png';
				}				
				
				$('#location_name').html(data.location_name);
				$('#location_address').html(data.location_address);
				$('#location_zip').html(data.location_zip);
				$('#location_city').html(data.location_city);
				$('#location_first').html(data.location_first);
				$('#location_last').html(data.location_last);
				$('#location_mac').html(data.location_mac);
				$('#events_count_week').html(data.events_count_week);
				$('#mac_adres').attr('value',data.location_mac);
				$('#location_udid').html(data.location_udid);
				$('#location_serie').html(data.location_serie);
				$('#location_sim').html(data.location_sim);
				$('#location_lijn').html(data.location_lijn);
				$('#location_serviceid').html(data.location_serviceid);
				$('#conn_status').html(data.conn_status);
				// Maps
				map.setCenter({lat:data.lat, lng:data.lng});
				zoom = map.getZoom();
				if (zoom < 13) map.setZoom(13);	
								
				var marker = new google.maps.Marker({
					position: new google.maps.LatLng(data.lat, data.lng),
					map: map,
					icon: err_icon
				});
				
				//Attach click listener to marker
				google.maps.event.addListener(marker, 'click', (function(key) {
					infowindow.setContent(data.info);
					infowindow.open(map, marker);
				}));
			}
		});			
	}	
	
	function getLocationActions(){
		$.ajax({
			type: 'GET',
			url: url_str+"?get=actions&id="+device_id,
			success: function(data) {
				$.each(data.actions, function(value, val) {
					$('#actions').append($('<div class="col-xs-6 col-lg-3">'+val+'</div>'));
				});								
			}
		});			
	}		
	
	function getLocationStatus(){
		$.ajax({
			type: 'GET',
			url: url_str+"?get=status&id="+device_id,
			success: function(data) {
				$.each(data.status, function(value, val) {
					$('#table_rows').append($('<tr><td>' + val[0] + '</td><td>' + val[1] +  '</td></tr>'));
				});				
			}
		});			
	}	

	function sendAction(action){
    	swal({
    		html: true,
    		title: "Weet je het zeker?",
    		text: "Execute: <b>" + action + "</b>",
    		type: "warning",
    		showCancelButton: true,
    		confirmButtonColor: "#DD6B55",
    		confirmButtonText: "Yes, execute",
    		closeOnConfirm: false
    	}, function() {
			$.ajax({
				type: 'POST',
				url: url_str+"?execute&id="+device_id,
				data: {execute:action},
				success: function(data) {
					swal({
						html: true,
						title: data.title,
						text: data.msg,
						type: data.type
					});				
				}
			});	
    	});		

	}
	</script>	