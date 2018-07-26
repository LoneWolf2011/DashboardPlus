
    <div id="map"></div>
	
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
				
				if (loc.path_status == 1 || loc.path_status == 4) {
					locations[key].marker.setMap(null);
					delete locations[key];
				} else {
					locations[key].marker.setVisible(true);
				}				
				
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
					if (loc.path_status == 1 || loc.path_status == 4) {
						locations[key].marker.setVisible(false);
					} else {
						locations[key].marker.setVisible(true);
					}
					locations[key].marker.setIcon(loc.icon)
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
		ajaxObj.options.url = url_str+'?get=markers&info=no&time='+ajaxObj.updatetime;
		$.ajaxq('markers',ajaxObj.options)
			// fires when ajax returns successfully
			.done(function(data){
				setMarkers(data);
				ajaxObj.updatetime = data.updatetime;
				//filterStatus('Trouble');
			}) 
			.fail(ajaxObj.fail) // fires when an ajax error occurs
			.always(ajaxObj.get(getMarkerData, 10000)); // fires after ajax success or ajax error		  
	}
	
	</script>