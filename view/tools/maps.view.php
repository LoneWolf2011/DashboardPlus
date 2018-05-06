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
				<tr>
					<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/darkgreen_MarkerR.png"/><span id="div_alert_active"  ></span></td>
					<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/yellow_MarkerG.png"/><span id="div_alert_backup"  ></span></td>
					<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/red_MarkerO.png"/><span id="div_alert_diss"  ></span></td>	
					<td><img src="<?= URL_ROOT_IMG;?>/GoogleMapsMarkers/blue_MarkerZ.png"/><span id="div_alert_nopath"  ></span></td>	
				</tr>
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
	var err_class;
	var err_icon;
	var err_conn;
	var err_txt;
	var url = <?= json_encode(URL_ROOT_IMG);?>+'/GoogleMapsMarkers/';
	var markers_arr = [];	
	var map;
	var markerCluster = null;
	var infoWindow = null;
			
	// A repository for markers (and the data from which they were constructed).
	var locations = {};
	var locs;
	var timestamp;
	// initial dataset for markers
	$.getJSON( url_str+'?get=markers&all', callbackData);
	
	function callbackData(data){
		locs = data;
		timestamp = data.updatetime;
		
			setMarkers(locs);  //Create markers from the initial dataset served with the document.
			
			ajaxObj.options.url = url_str+'?get=markers&time='+timestamp;
			ajaxObj.updatetime = timestamp;
			ajaxObj.get(getMarkerData,0); //Start the get cycle.	
			ajaxObj.get(getMarkerTable,5000); 	
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
		
		var center = {lat: <?= json_encode((int)APP_LAT) ;?>, lng: <?= json_encode((int)APP_LNG) ;?>};
		map = new google.maps.Map(document.getElementById('map'), {
			disableDefaultUI:true,
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
		
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(document.getElementById('div_table'));	
        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(document.getElementById('div_grouped'));	
		
	}
	
	//When true, markers for all unreported locs will be removed. 
	// if false; removal must be specified in json data: scsnr: { remove: true }
	var auto_remove = false;

	function setMarkers(locObj) {
		if(auto_remove) {
			//Remove markers for all unreported locs, and the corrsponding locations entry.
			$.each(locations, function(key) {
				if(!locObj[key]) {
					if(locations[key].marker) {
						locations[key].marker.setMap(null);
					}
					delete locations[key];
				}
			});
		}

		$.each(locObj, function(key, loc) {
			
			if(!locations[key] && loc.lat!==undefined && loc.lng!==undefined) {
				//Marker has not yet been made (and there's enough data to create one).

				if(loc.path_status == 0){
					err_icon = url+'red_Marker'+loc.first_char+'.png';
				} else if(loc.path_status == 2){
					err_icon = url+'yellow_Marker'+loc.first_char+'.png';
				} else if(loc.path_status == 3){
					err_icon = url+'blue_Marker'+loc.first_char+'.png';					
				} else {
					err_icon = url+'darkgreen_Marker'+loc.first_char+'.png';
				}				
				
				//Create marker
				loc.marker = new google.maps.Marker({
					position: new google.maps.LatLng(loc.lat, loc.lng),
					category: loc.category,
					path: loc.path_status,
					id: loc.id,
					map: map,
					icon: err_icon
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
					if(loc.path_status == 0){
						err_icon = url+'red_Marker'+loc.first_char+'.png';
					} else if(loc.path_status == 2){
						err_icon = url+'yellow_Marker'+loc.first_char+'.png';
					} else {
						err_icon = url+'darkgreen_Marker'+loc.first_char+'.png';
					}					
					locations[key].marker.setIcon(err_icon)
				}
				//locations[key].info looks after itself.
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
		$.ajax(ajaxObj.options)
			//fires when ajax returns successfully
			.done(function(data){
				setMarkers(data);
				ajaxObj.updatetime = data.updatetime;
				
				//console.log(ajaxObj.updatetime);
			}) 
			.fail(ajaxObj.fail) //fires when an ajax error occurs
			.always(ajaxObj.get(getMarkerData, 10000)); //fires after ajax success or ajax error		  
	}

	function getMarkerTable() {
		ajaxObj.options.url = url_str+'?get=markers&div&time='+ajaxObj.updatetime;
		$.ajax(ajaxObj.options)
			.done(function(data){
				if(data.status == 1){
					$('#div_alert_active').html(data.count.conn);
					$('#div_alert_diss').html(data.count.diss);
					$('#div_alert_backup').html(data.count.back);
					$('#div_alert_nopath').html(data.count.nopath);
																	
					$('#div_table').css('display', 'block');
					$('#div_grouped').html(data.grouped);
				
					$('#table_rows').empty();
					$.each(data.locations, function(value, val) {
						console.log(value);
						$('#table_rows').append($('<tr><td>' + val['loc_conn'] + '</td><td>' + val['loc_group'] + '</td><td>' + val['loc_id'] + '</td></tr>'));
					});	
				}				
			}) 
			.fail(ajaxObj.fail) 
			.always(ajaxObj.get(getMarkerTable, 10000));	  
	}

	</script>