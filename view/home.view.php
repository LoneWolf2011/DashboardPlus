	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
				<div class="row">
					<div class="col-lg-5">
						<div class="row">
						
							<div class="col-xs-6 col-sm-6 col-md-6">
								<div class="ibox float-e-margins">
									<div class="ibox-title">
										<span class="label label-success pull-right" data-i18n="[html]home.events.day.label">24H</span>
										<h5 data-i18n="[html]home.events.h5">Events</h5>
									</div>
									<div class="ibox-content">
										<h1 class="no-margins" id="e_day"></h1>
										<div class="stat-percent font-bold" id="e_day_percent"> </div>
										<small data-i18n="[html]home.events.day.small_label">Total</small>
									</div>
								</div>
							</div>
							<div class="col-xs-6 col-sm-6 col-md-6">
								<div class="ibox float-e-margins">
									<div class="ibox-title">
										<span class="label label-success pull-right" data-i18n="[html]home.events.week.label">Weekly</span>
										<h5 data-i18n="[html]home.events.h5">Events</h5>
									</div>
									<div class="ibox-content">
										<h1 class="no-margins" id="e_week"></h1>
										<div class="stat-percent font-bold" id="e_week_percent"> </div>
										<small data-i18n="[html]home.events.week.small_label">Total </small>
									</div>
								</div>
							</div>
							<!--<div class="col-md-4">
								<div class="ibox float-e-margins">
									<div class="ibox-title">
										<span class="label label-success pull-right" data-i18n="[html]home.events.month.label">Monthly</span>
										<h5>Events</h5>
									</div>
									<div class="ibox-content">
										<h1 class="no-margins" id="e_month"></h1>
										<div class="stat-percent font-bold text-danger">20% <i class="fa fa-level-down"></i></div>
										<small data-i18n="[html]home.events.month.small_label">Total</small>
									</div>
								</div>
							</div>-->							
						</div>
						<div class="ibox float-e-margins" id="ibox1">

							<div class="ibox-content no-padding">
								<div class="sk-spinner sk-spinner-wave">
									<div class="sk-rect1"></div>
									<div class="sk-rect2"></div>
									<div class="sk-rect3"></div>
									<div class="sk-rect4"></div>
									<div class="sk-rect5"></div>
								</div>								
								<div class="tabs-container">
									<ul class="nav nav-tabs">
										<li class="active"><a data-toggle="tab" href="#tab-1" ><span data-i18n="[html]home.locations.active.label"> Active locations</span> <span id="count_active"></span></a></li>
										<li class=""><a data-toggle="tab" href="#tab-2" ><span data-i18n="[html]home.locations.problem.label">Problem locations</span> <span  id="count_problem"></span></a></li>
										<!--<li class=""><a data-toggle="tab" href="#tab-3" ><span data-i18n="[html]home.locations.device.label">230v / Battery problem</span> <span id="count_device"></span> </a></li>-->
										<li class=""><a data-toggle="tab" href="#tab-4" ><span data-i18n="[html]home.locations.notactive.label">Offline locations</span> <span  id="count_inactive"></span></a></li>										
									</ul>
									<div class="tab-content">
										<div id="tab-1" class="tab-pane active">
											<div class="panel-body" id="first_pane">
											
												<div class="table-responsive">
																								
													<table class="table table-hover datatable">
														<thead>
															<tr>
																<th data-i18n="[html]home.locations.active.table.th1">Connection</th>
																<th data-i18n="[html]home.locations.active.table.th2">ID</th>
																<th data-i18n="[html]home.locations.active.table.th3">Location</th>
																<th data-i18n="[html]home.locations.active.table.th4">Last seen</th>
															</tr>
														</thead>
														<tbody><!--JSON RES--></tbody>
													</table>
												</div>							
			
											</div>
										</div>
										<div id="tab-2" class="tab-pane" >
											<div class="panel-body" id="second_pane">
												<div class="table-responsive">
													<table class="table table-hover datatable_problem">
														<thead>
															<tr>
																<th data-i18n="[html]home.locations.problem.table.th1">Conn</th>
																<th data-i18n="[html]home.locations.problem.table.th2">ID</th>
																<th data-i18n="[html]home.locations.problem.table.th3">Location</th>
																<th data-i18n="[html]home.locations.problem.table.th4">Last seen</th>
															</tr>
														</thead>
														<tbody></tbody>
													</table>
												</div>
												
											</div>
										</div>
										<!--<div id="tab-3" class="tab-pane" >
											<div class="panel-body" id="second_pane">
												<div class="table-responsive">
													<table class="table table-hover datatable_device_state">
														<thead>
															<tr>
																<th data-i18n="[html]home.locations.device.table.th1">Conn</th>
																<th data-i18n="[html]home.locations.device.table.th2">ID</th>
																<th data-i18n="[html]home.locations.device.table.th3">Location</th>
																<th data-i18n="[html]home.locations.device.table.th4">Last seen</th>
															</tr>
														</thead>
														<tbody></tbody>
													</table>
												</div>
												
											</div>
										</div>-->									
										
										<div id="tab-4" class="tab-pane" >
											<div class="panel-body" id="second_pane">
												<div class="table-responsive">
													<table class="table table-hover datatable_inactive">
														<thead>
															<tr>
																<th data-i18n="[html]home.locations.notactive.table.th1">Conn</th>
																<th data-i18n="[html]home.locations.notactive.table.th2">ID</th>
																<th data-i18n="[html]home.locations.notactive.table.th3">Location</th>
																<th data-i18n="[html]home.locations.notactive.table.th4">Last seen</th>
															</tr>
														</thead>
														<tbody><!--JSON RES--></tbody>
													</table>
												</div>
												
											</div>
										</div>
										
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-7">
						<div class="ibox float-e-margins">
							<div class="ibox-title">
								<h5 data-i18n="[html]home.actions.text">Actions</h5>
							</div>
							<div class="ibox-content">
								<div id="inline-actions">
									<div class="row">
										<div class="col-sm-4 col-md-4">
											<span data-i18n="[html]home.actions.mapzoom">Set map zoom level:</span>
												<select class="form-control m-b" id="zoom_level" >
													<option value="7">Default</option>
													<option value="8">8</option>
													<option value="9">9</option>
													<option value="10">10</option>
													<option value="11">11</option>
													<option value="12">12</option>
													<option value="13">13</option>
													<option value="14">14</option>
												</select>
										</div >																	
										<div class="col-sm-4 col-md-4">				
											<span >Filter status:</span>
												<select class="select2 form-control m-b" id="type" onchange="filterStatus(this.value);">
													<option value="">All</option>
													<option value="Trouble">Trouble</option>
													<option value="0">Disconnected</option>
													<option value="1">Connected</option>
													<option value="2">Backup disconnected</option>
													<option value="3">IP disconnected</option>
													<option value="4">No path status</option>
												</select>
										</div >
										<div class="col-sm-4 col-md-4">				
											<span >Filter location:</span>
												<select class="select2 form-control m-b" id="type" onchange="filterLocation(this.value);">
													<option value="">All</option>
													<option value="DIGI">Digialarm</option>
													<option value="Brand">Brand</option>
													<option value="ING">ING</option>
													<option value="RAC">RAC</option>
												</select>
										</div >										
									</div >
									<div class="row">	
										<div class="col-xs-12 col-sm-12 col-md-6">
											<span data-i18n="[html]home.actions.mapcontrols">Map controls:</span>
											<div class="btn-group">
												<button type="button" class="btn btn-default btn-sm" onclick="refreshNow();"  ><i class="fa fa-refresh"></i> <span data-i18n="[html]home.actions.buttons.refresh">Refresh</span></button>									
												<button type="button" class="btn btn-default btn-sm" onclick="centerControl(center);"  ><i class="fa fa-map-marker"></i> <span data-i18n="[html]home.actions.buttons.center">Center</span></button>									
											</div>
										</div>
										<div class="col-xs-12 col-sm-12 col-md-6">
											<span >Cluster controls:</span>
											<div class="btn-group">	
												<button type="button" class="btn btn-default btn-sm" onclick="setClusters();"  ><i class="fa fa-circle-o"></i> <span data-i18n="[html]home.actions.buttons.cluster">Cluster</span></button>									
												<button type="button" class="btn btn-default btn-sm" onclick="clearClusters();" ><i class="fa fa-remove"></i> <span data-i18n="[html]home.actions.buttons.clear">Clear</span></button>							
											</div>									
										</div>									
									</div>									

								</div>
							</div>
						</div>		

						<div class="google-map" id="map" style="height:600px;"></div>
					</div>
					
				</div>		
	
			</div>		
		</div>
		
	</div>
	
	<?php
		// View specific scripts
		array_push($arr_js, '/js/plugins/sparkline/jquery.sparkline.min.js');
		array_push($arr_js, '/js/plugins/dataTables/datatables.min.js');
		array_push($arr_js, '/js/google_style_dark.js');
		
	?>
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>	
	<script async defer src="https://maps.googleapis.com/maps/api/js?&key=<?= GOOGLE_API;?>&callback=initMap"></script>
	
	<!--<script async defer src="<?= URL_ROOT;?>/js/google.js?callback=initMap"></script>-->
	
    <script>
    $(document).ready(function() {
		
		$('#ibox1').children('.ibox-content').toggleClass('sk-loading');
		var refresh = 100000;		
		getEventsCount();
		setInterval( function () {
			getEventsCount(); 
		}, refresh );
		
		var interval;

		var table_active;
		var table_problem;
		var table_device_state;
		var table_inactive;
		var lang_code = $('html').attr('lang');
		
		$.extend( true, $.fn.dataTable.defaults, {
			language: {
				url: <?= json_encode(URL_ROOT);?>+'/js/plugins/dataTables/'+$('html').attr('lang')+'.json'
			},
			iDisplayLength: 10,
			deferRender: true,
			order: [[ 3, "desc"]],
			lengthMenu: [ 10, 20, 25 ],
			processing: true,
			serverSide: true,
			responsive: true
			
		} );		
	
		
		table_active = $(".datatable").DataTable({	
			ajax: url_str+"?get=list&state=active",
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
					lng: lang_code
				}, function (t){
					$('#i18container').i18n();
				});				
				
			}			
		});

		// On tab switch 1
		$('a[href="#tab-1"]').on('shown.bs.tab', function (e) {
			clearInterval(interval);
			interval = setInterval( function () {
				table_active.ajax.reload( null, false ); 
			}, refresh );						
		});
			
		// On tab switch 2
		$('a[href="#tab-2"]').on('shown.bs.tab', function (e) {

			// If datatable not initialized 
			if ( ! $.fn.DataTable.isDataTable( '.datatable_problem' ) ) {
				// Show loading
				$('#ibox1').children('.ibox-content').toggleClass('sk-loading');
				// Initialize table
				table_problem = $(".datatable_problem").DataTable({
					ajax: url_str+"?get=list&state=problem",
					fnInitComplete: function(oSettings, json) {
						$('#ibox1').children('.ibox-content').toggleClass('sk-loading');
						clearInterval(interval);
						interval = setInterval( function () {
							table_problem.ajax.reload( null, false ); 
						}, refresh );						
					}			
				});	
								
			};
						clearInterval(interval);
						interval = setInterval( function () {
							table_problem.ajax.reload( null, false ); 
						}, refresh );				
		});

		// On tab switch 3
		$('a[href="#tab-3"]').on('shown.bs.tab', function (e) {
			// If datatable not initialized 
			if ( ! $.fn.DataTable.isDataTable( '.datatable_device_state' ) ) {
				// Show loading
				$('#ibox1').children('.ibox-content').toggleClass('sk-loading');
				// Initialize table
				table_device_state = $(".datatable_device_state").DataTable({
					ajax: url_str+"?get=list&rms",
					fnInitComplete: function(oSettings, json) {
						$('#ibox1').children('.ibox-content').toggleClass('sk-loading');
						clearInterval(interval);
						interval = setInterval( function () {
							table_device_state.ajax.reload( null, false ); 
						}, refresh );						
					}			
				});	
								
			};
						clearInterval(interval);
						interval = setInterval( function () {
							table_device_state.ajax.reload( null, false ); 
						}, refresh );				
		});
		
		// On tab switch 4
		$('a[href="#tab-4"]').on('shown.bs.tab', function (e) {
			// If datatable not initialized 
			if ( ! $.fn.DataTable.isDataTable( '.datatable_inactive' ) ) {
				// Show loading
				$('#ibox1').children('.ibox-content').toggleClass('sk-loading');
				// Initialize table
				 table_inactive = $(".datatable_inactive").DataTable({
					ajax: url_str+"?get=list&state=inactive",
					fnInitComplete: function(oSettings, json) {
						$('#ibox1').children('.ibox-content').toggleClass('sk-loading');
						clearInterval(interval);
						interval = setInterval( function () {
							table_inactive.ajax.reload( null, false ); 
						}, refresh );						
					}			
				});	
								
			};
						clearInterval(interval);
						interval = setInterval( function () {
							table_inactive.ajax.reload( null, false ); 
						}, refresh );				
			
		});
		
    });

	function popupWindow(url, title, w, h) {
		// Create reference to new window
		var newWindow = window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=yes, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
		// Position secondary screen
		var left = 2100;
		var top = 100;
		
		if(newWindow.location.href === 'about:blank')
		{
			newWindow.location.href = url
		}
		//console.log(newWindow.location.href);
		return newWindow;
	}
	
	
	var url_str = $('#url_string').val();
	function getEventsCount(){
		$.ajax({
			type: 'GET',
			url: url_str+'?get=event_count',
			//data: {ID: location_id},
			success: function(data) {
				$('#e_day').html(data.day.count);
				$('#e_day_percent').html(data.day.percent);
				$('#e_week').html(data.week.count);
				$('#e_week_percent').html(data.week.percent);
				
				//var c_active = (data.scs_active_count != '0') ? $('#count_active').html(data.scs_active_count).addClass('badge badge-primary') : '';
				//var c_problem = (data.scs_problem_count != '0') ? $('#count_problem').html(data.scs_problem_count).addClass('badge badge-warning') : '';
				//var c_storing = (data.rms_storing_count != '0') ? $('#count_device').html(data.rms_storing_count).addClass('badge badge-warning') : '';
				//var c_inactive = (data.scs_inactive_count != '0') ? $('#count_inactive').html(data.scs_inactive_count).addClass('badge badge-danger') : '';
			}
		});		
	}
	
	// Global vars for maps
	var url = <?= json_encode(URL_ROOT_IMG);?>+'/GoogleMapsMarkers/';
	var center = {lat: <?= json_encode((int)APP_LAT) ;?>, lng: <?= json_encode((int)APP_LNG) ;?>};
	var markers_arr = [];	
	var map;
	var markerCluster = null;
	var infoWindow = null;
			
	//A repository for markers (and the data from which they were contructed).
	var locations = {};
	var locs;
	var timestamp;
	//initial dataset for markers
	$.getJSON( url_str+'?get=markers&all', callbackData);
	
	function callbackData(data){
		
		setMarkers(data);  //Create markers from the initial dataset served with the document.
		
		ajaxObj.options.url = url_str+'?get=markers&time='+data.updatetime;
		ajaxObj.updatetime = data.updatetime;
		ajaxObj.get(getMarkerData,5000); //Start the get cycle.	
	}

	// Set map zoom level
    $("#zoom_level").change(function(){
		var zoom_lvl = parseInt($(this).val(), 10);
		map.setZoom(zoom_lvl);
    });	
	
	// Init google maps
	function initMap(){	
		// OPEN STREET MAP
		var mapTypeIds = [];
		for(var type in google.maps.MapTypeId) {
			mapTypeIds.push(google.maps.MapTypeId[type]);
		}
		mapTypeIds.push("OSM");	
		
		map = new google.maps.Map(document.getElementById('map'), {
			center: center,
			zoom: 8, 
			styles: google_styles,
			mapTypeControlOptions: {
				mapTypeIds: mapTypeIds
			}		
        });

        map.mapTypes.set("OSM", new google.maps.ImageMapType({
            getTileUrl: function(coord, zoom) {
                // See above example if you need smooth wrapping at 180th meridian
                return "http://tile.openstreetmap.org/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
            },
            tileSize: new google.maps.Size(256, 256),
            name: "OpenStreetMap",
            maxZoom: 18
        }));
		
		infowindow = new google.maps.InfoWindow();
	}
		
	function setMarkers(locObj) {		

		$.each(locObj, function(key, loc) {
			
			if(!locations[key] && loc.lat!==undefined && loc.lng!==undefined) {
				//Marker has not yet been made (and there's enough data to create one).				
				//Create marker
				loc.marker = new google.maps.Marker({
					position: new google.maps.LatLng(loc.lat, loc.lng),
					category: loc.category,
					path: loc.path_status,
					map: map,
					icon: loc.icon
				});
				//Attach click listener to marker
				google.maps.event.addListener(loc.marker, 'click', (function(key) {
					return function() {
						if(locations[key]) {
							infowindow.setContent(locations[key].info);
							infowindow.open(map, locations[key].marker);
						}
					}
				})(key));				
				//Remember loc in the `locations` so its info can be displayed and so its marker can be deleted.
				locations[key] = loc;				
				// Fill array for clusters
				markers_arr.push(locations[key].marker);
			} else if(locations[key] && loc.remove) {
				//Remove marker from map
				if(locations[key].marker) {
					locations[key].marker.setMap(null);
				}
				//Remove element from `locations`
				delete locations[key];
			} else if(locations[key]) {
				//Update the previous data object with the latest data.
				$.extend(locations[key], loc);
				if(loc.lat!==undefined && loc.lng!==undefined) {
					//Update marker position (maybe not necessary but doesn't hurt).
					locations[key].marker.setPosition(
						new google.maps.LatLng(loc.lat, loc.lng)
					);
					
				}
				if(loc.path_status!==undefined ) {				
					locations[key].marker.setIcon(loc.icon);
				}
			}
		});
	
	}
	
	var ajaxObj = {
		options: {
			url: null,
			dataType: 'json'
		},
		delay: function(refresh_time) {
			return refresh_time;
		},
		errorCount: 0,
		errorThreshold: 5,
		ticker: null,
		updatetime: null,
		get: function(function_name, refresh_time) {
			if (ajaxObj.errorCount < ajaxObj.errorThreshold) { // Gets triggered for all objects!?
				ajaxObj.ticker = setTimeout(function_name, ajaxObj.delay(refresh_time));
				swal.close();
			}
		},
		fail: function(jqXHR, textStatus, errorThrown) {
			console.log(errorThrown);
			
			swal({
				html: true,
				title: textStatus,
				text: errorThrown,
				type: "error"
			});
			ajaxObj.errorCount++;
		}
	};
	
	//Ajax master routine
	function getMarkerData() {
		ajaxObj.options.url = url_str+'?get=markers&time='+ajaxObj.updatetime;
		$.ajaxq('markers',ajaxObj.options)
			// fires when ajax returns successfully
			.done(function(data){
				setMarkers(data);
				ajaxObj.updatetime = data.updatetime;
			}) 
			.fail(ajaxObj.fail)
			.always(ajaxObj.get(getMarkerData, 10000)); 		  
	}

	function refreshNow(){
		ajaxObj.get(getMarkerData,0);
		//map.setZoom(7);
	}	

	function filterStatus(status) {
		
		if(status == 'Trouble'){
			for (var i = 0, marker; marker = markers_arr[i]; i++) {
	
				if (marker.path == 2 || marker.path == 3 || marker.path == 0 || status.length === 0) {
					marker.setVisible(true);
				} else {
					marker.setVisible(false);
				}
			}			
		} else {
			for (var i = 0, marker; marker = markers_arr[i]; i++) {
	
				if (marker.path == status || status.length === 0) {
					marker.setVisible(true);
				} else {
					marker.setVisible(false);
				}
			}			
		}

	}	
	
	function filterLocation(category) {
		for (var i = 0, marker; marker = markers_arr[i]; i++) {
			// If is same category or category not picked
			if (marker.category == category || category.length === 0) {
				marker.setVisible(true);
			} else {
				marker.setVisible(false);
			}
		}
	}
	
	function setClusters(){
		// NOTE: clusters werken niet!!	
		var calc = function(markers_arr, numStyles) {
			for (var i = 0; i < markers_arr.length; i++) {
				if (markers_arr[i].getIcon().indexOf("red_MarkerD") > 1) {
					return {text: markers_arr.length, index: 2};
				}
			}
			return {text: markers_arr.length, index: 1};
		}			
		
		var mcOptions = {
			gridSize: 50, 
			maxZoom: 15, 
			styles: [{
				url: <?= json_encode(URL_ROOT);?>+'/js/plugins/markerclusterer/images/m1.png',
				textColor: 'white',
				height: 52,
				width: 52
			},
			{
				url: <?= json_encode(URL_ROOT);?>+'/js/plugins/markerclusterer/images/m3.png',
				textColor: 'white',
				height: 65,
				width: 65
			}]
		};

		markerCluster = new MarkerClusterer(map, markers_arr, mcOptions);		
		markerCluster.setCalculator(calc);
		//map.setZoom(7);		
	}
	
	function clearClusters(){
		markerCluster.clearMarkers();
		for (var i = 0, marker; marker = markers_arr[i]; i++) {
			marker.setMap(map);
		}
		
	}
	
    function centerControl(center) {
		map.setCenter(center);		
    }	
	
    function selectMarker(id){
		for (var i = 0, marker; marker = markers_arr[i]; i++) {
			if (marker.id == id || id.length === 0) {
				google.maps.event.trigger(markers_arr[i], 'click');
				map.panTo(marker.position);
				
				zoom = map.getZoom();
				if (zoom < 13) map.setZoom(13);				
			}
		}
        
    }	
	// *******************
	//test: simulated ajax
	/*var testLocs = {
		1: { info:'1. New Random info and new position', lat:0, lng:144.9634 },//update info and position
		2: { lat:0, lng:14.5144 },//update position
		3: { info:'3. New Random info' },//update info
		5: { info:'55555. Added', lat:0, lng:60 }//add new marker
	};*/
	//setTimeout(function() {
	//	setMarkers(locs);
	//}, ajaxObj.delay);
	// *******************
	
    </script>	

