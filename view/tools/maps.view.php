
    <div id="map"></div>
	
	<div id="div_table" class="map-div-table">
	
		<div class="map-div">
				<h3>Realtime disconnects</h3>
			<table width="100%">	
				<tbody id="table_rows"></tbody>
			</table>
		</div>	

		<div class="map-div">
			<table width="100%">
				<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/darkgreen_MarkerX.png"/></td><td><span id="div_alert_active"  ></span></td><td>Verbonden</td></tr>
				<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/yellow_MarkerX.png"/></td><td><span id="div_alert_backup"  ></span></td><td>Backup uitval</td></tr>
				<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/orange_MarkerX.png"/></td><td><span id="div_alert_primair"  ></span></td><td>Primair uitval</td></tr>
				<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/red_MarkerX.png"/></td><td><span id="div_alert_diss"  ></span></td>	<td>Niet verbonden</td></tr>
				<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/blue_MarkerX.png"/></td><td><span id="div_alert_nopath"  ></span></td><td>Geen pathstatus</td></tr>	
			</table>
			<br>
			<table width="100%">
				<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/darkgreen_MarkerA.png"/></td><td>AOIP</td></tr>
				<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/darkgreen_MarkerB.png"/></td><td>BRAND</td></tr>
				<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/darkgreen_MarkerD.png"/></td><td>DIGIALARM</td></tr>
				<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/darkgreen_MarkerI.png"/></td><td>ING</td></tr>
				<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/darkgreen_MarkerN.png"/></td><td>B-NOTIFIED</td></tr>
				<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/darkgreen_MarkerM.png"/></td><td>MUSDONET</td></tr>
				<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/darkgreen_MarkerS.png"/></td><td>S&E</td></tr>
				<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/darkgreen_MarkerF.png"/></td><td>MISTMACHINE</tr>
			</table>
		</div>
			
	</div>

	<div id="div_grouped" class="map-div-grouped">
	
	</div>
	
	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'/Src/controllers/home.controller.php';?>" />	
	
	<?php
		// View specific scripts
		array_push($arr_js, '/js/google_style_dark.js');
		
	?>	
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>	

	<script async defer src="https://maps.googleapis.com/maps/api/js?&key=<?= GOOGLE_API;?>&callback=initMap"></script>	
	
	<script>
	// Global vars for maps
	var url_str = $('#url_string').val();
	var map;
	
	// A repository for markers (and the data from which they were constructed).
	var locations = {};	
		
	// initial dataset for markers
	$.getJSON( url_str+'?get=markers&info=no&all', callbackData);
	
	function callbackData(data){
		
		setMarkers(data);  //Create markers from the initial dataset served with the document.
		
		ajaxObj.options.url = url_str+'?get=markers&info=no&time='+data.updatetime;
		ajaxObj.updatetime = data.updatetime;
		ajaxObj.get(getMarkerData,0); //Start the get cycle.	
		ajaxObj.get(getMarkerTable,0); 	
	}
	
	// Init google maps
	function initMap(){	

		var center = {lat: <?= json_encode((int)APP_LAT) ;?>, lng: <?= json_encode((int)APP_LNG) ;?>};
		map = new google.maps.Map(document.getElementById('map'), {
			disableDefaultUI:true,
			center: center,
			zoom: 8, 
			styles: google_styles
        });
		
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(document.getElementById('div_table'));	
        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(document.getElementById('div_grouped'));	
		
	}
		
	function setMarkers(locObj) {		

		$.each(locObj, function(key, loc) {
			
			if(!locations[key] && loc.lat!==undefined && loc.lng!==undefined) {
				//Marker has not yet been made (and there's enough data to create one).				
				//Create marker
				loc.marker = new google.maps.Marker({
					position: new google.maps.LatLng(loc.lat, loc.lng),
					path: loc.path_status,
					map: map,
					icon: loc.icon
				});
				
				//Remember loc in the `locations` so its info can be displayed and so its marker can be deleted.
				locations[key] = loc;				
				
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
		ajaxObj.options.url = url_str+'?get=markers&info=no&time='+ajaxObj.updatetime;
		$.ajaxq('markers',ajaxObj.options)
			// fires when ajax returns successfully
			.done(function(data){
				setMarkers(data);
				ajaxObj.updatetime = data.updatetime;
			}) 
			.fail(ajaxObj.fail)
			.always(ajaxObj.get(getMarkerData, 10000)); 		  
	}

	function getMarkerTable() {
		ajaxObj.options.url = url_str+'?get=markers&div&time='+ajaxObj.updatetime;
		$.ajaxq('markers', ajaxObj.options)
			.done(function(data){
				if(data.status == 1){
					$('#div_alert_active').html(data.count.conn);
					$('#div_alert_diss').html(data.count.diss);
					$('#div_alert_primair').html(data.count.prim);
					$('#div_alert_backup').html(data.count.back);
					$('#div_alert_nopath').html(data.count.nopath);								
					$('#div_table').css('display', 'block');
					$('#div_grouped').html(data.grouped);
					$('#table_rows').empty();
					$.each(data.locations, function(value, val) {
						$('#table_rows').append($('<tr><td>' + val['loc_conn'] + '</td><td>' + val['loc_group'] + '</td><td>' + val['loc_id'] + '</td></tr>'));
					});	
				}				
			}) 
			.fail(ajaxObj.fail) 
			.always(ajaxObj.get(getMarkerTable, 10000));	  
	}

	</script>