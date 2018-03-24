	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">

			<div class="col-lg-3">
                <div class="ibox float-e-margins">
					<div class="ibox-content">
						<table class="table table-hover">
							<thead>
							<th>Site</th>
							<th><?= $_GET['site'];?></th>
							</thead>
							<tbody id="location_tr">
							</tbody>
						</table>
					</div>
                </div>			
			
			</div>		
		
			<div class="col-lg-5">
                <div class="ibox float-e-margins">
					<div class="ibox-content">
						<table class="table table-hover">
							<thead>
							<tr>
								<th>ZoneID</th>
								<th>Name</th>
								<th>Devices</th>
								<th>Current wait time</th>
							</tr>
							</thead>
							<tbody>
							<?php
								foreach($obj->getZones() as $key => $val ){
									echo '<tr><td>'.$val['link'].'</td><td>'.$val['zone'].'</td><td>'.implode('<br>',$val['devices']).'</td><td>'.$val['wait'].'</td><td><span class="btn btn-primary btn-xs zone_graph" rel="'.$val['zone'].'" value="'.$val['zone_count'].'">graph</span></td></tr>';
								}
							?>
							</tbody>
						</table>
					</div>
                </div>			
			
			</div>		
		
			<div class="col-lg-2">
				<div id="c_total"></div>
			</div>
			<div class="col-lg-2">
				<div id="c_all"></div>
			</div>
	
		</div>	
		
		<div class="row">
			<div class="col-lg-12">
                <div class="ibox float-e-margins">
					<div class="ibox-title">									  
						<h5>Person count Site<?= $_GET['site'];?> (24h)<small></small> <a class="fullscreen-link"><i class="fa fa-expand"></i></a></h5>
						<div class="clearfix"></div>
					</div>
					<div class="ibox-content">
						<div class="row">
							<div class="col-lg-12">				
								<div id="sign_chart"></div>
							</div>
							<!--<div class="col-lg-4">				
								<div id="time_chart"></div>
							</div>	-->						
						</div>
					</div>
                </div>
			</div>		
		</div>
		
	</div>

	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'/Src/controllers/home.controller.php';?>" />
	<input type="text" hidden id="url_site" value="<?= (isset($_GET['site'])) ? $_GET['site'] : 1;?>" />
	
	<?php
		// View specific scripts
		array_push($arr_js, '/js/plugins/sparkline/jquery.sparkline.min.js');
		array_push($arr_js, '/js/plugins/d3/d3.min.js');
		array_push($arr_js, '/js/plugins/c3/c3.min.js');
		
	?>		
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>	

	<script>
	$(document).ready(function () {	
		
		var zone_name_arr = [];
		
		$( '.zone_graph' ).on('click',function() {

			var zone_data = $(this).attr('value');
			var zone_name = $(this).attr('rel');
			var zone_arr = zone_data.split(';');
			zone_arr.unshift(zone_name);
			
			if($.inArray(zone_name, zone_name_arr) == -1) {
				//add to array
				zone_name_arr.push(zone_name);
			} else {
				zone_name_arr.splice($.inArray(zone_name, zone_name_arr),1);
			}	
			//console.log(zone_name_arr);
			
			compchart.load({
				columns:[
					zone_arr
				],
				type: 'bar'
			});	
			compchart.groups([ 
				zone_name_arr
			]);
			
		});	

		getSignalLoad();
		getCount();
		
		// Set chart zoom on mobile
		if ($(this).width() < 769) {
			compchart.zoom([0, 5]); 
		}
	});	
	
	var url_str = $('#url_string').val();
	
	var chart = c3.generate({
		bindto: '#time_chart',
		data: {
			columns: [
				['data', 60]
			],
			type: 'gauge',
			onclick: function (d, i) { console.log("onclick", d, i); },
			onmouseover: function (d, i) { console.log("onmouseover", d, i); },
			onmouseout: function (d, i) { console.log("onmouseout", d, i); }
		},
		gauge: {
			fullCircle: true, 
			label: {
				format: function(value, ratio) {
					return value;
				},
				show: false // to turn off the min/max labels.
			},
			startingAngle: 90,
			min: 0, // 0 is default, //can handle negative min e.g. vacuum / voltage / current flow / rate of change
			max: 100, // 100 is default
			units: ' %',
			width: 20 // for adjusting arc thickness
		},
		color: {
			pattern: ['#FF0000', '#f8ac59', '#1ab394'], // the three color levels for the percentage 1ab394 values.
			threshold: {
	//            unit: 'value', // percentage is default
	//            max: 200, // 100 is default
				values: [30, 60, 90, 100]
			}
		},
		size: {
			height: 180
		}
	});
	
	var compchart = c3.generate({
		bindto: '#sign_chart',
		data: {
			x: 'x',
			xFormat: '%H:%M',
			columns: [],
			order: false
		},
		point: {
			show: false
		},		
		type: 'spline',
		size: {
			height:500
		},		
		axis: {
			x: {
				type: 'category'
			}
		},
		
		color: {
			pattern: ["#f6a821", "#d3d3d3",  "#676B73", "rgba(246,168,33, 1)", "#1ab394", "#fff","#1ab394"]
		},		
		zoom: {
			enabled: true
		}			
	});		

	var ajaxObj = {
		options: {
			url: null,
			dataType: 'json' 
		},
		delay: 10000,
		errorCount: 0,
		errorThreshold: 5,
		ticker: null,
		updatetime: null,
		get: function(function_name) { 
			if(ajaxObj.errorCount < ajaxObj.errorThreshold) { // Gets triggered for all objects!?
				ajaxObj.ticker = setTimeout(function_name, ajaxObj.delay);
				 swal.close();
			}
		},
		fail: function(jqXHR, textStatus, errorThrown) {
			console.log(errorThrown);
			swal({
				html:true, 
				title: textStatus,
				text: errorThrown,
				type: "error"
			});		
			ajaxObj.errorCount++;
		}
	};	
	
	function getCount(){
		ajaxObj.options.url = url_str+"?get=peoplecount&site="+$('#url_site').val();
		
		$.ajax(ajaxObj.options)
			.done(function(data){
				if(data.status != 0){
					$('#c_all').html(data.c_all);
					$('#c_total').html(data.c_total);
					$('#location_tr').html(data.location);
				} else {
					
				}
			}) 
			.fail(ajaxObj.fail) 
			.always(ajaxObj.get(getCount));
		
	}	

	
	function getSignalLoad(){
		ajaxObj.options.url = url_str+"?get=signalload&site="+$('#url_site').val(),
		
		$.ajax(ajaxObj.options)
			.done(function(data){
				if(data.status != 0){
					compchart.load({
						columns: [
							data.hours,
							data.signal,
							data.trend
						],
						type: 'area-spline',
						types: {
							trend: 'line'
						}					
					});
					//compchart.ygrids([
					//	{value: data.avg_last, class: data.avg_last > data.avg_now ? 'gridorange': '', text: 'Gemiddelde vorige week ' + data.avg_last, position: 'start'},
					//	{value: data.avg_now, class: data.avg_now > data.avg_last ? 'gridorange': 'gridgreen', text: 'Gemiddelde deze week ' + data.avg_now}
					//]);					
					compchart.data.names({
						signal: 'Total count',
						trend: 'Trend'
					});					
				} else {
					compchart.destroy();	
				}
			}) 
			.fail(ajaxObj.fail) 
			.always(ajaxObj.get(getSignalLoad));
	}	
	
	</script>