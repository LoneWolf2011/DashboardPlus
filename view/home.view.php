	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<div class="col-lg-4">
				<div class="widget yellow-bg p-lg text-center">
					<div class="m-b-md">
						<i class="fa fa-user fa-4x"></i>
						<h1 class="m-xs">520</h1>
						<h3 class="font-bold no-margins">
							Current count
						</h3>
						<small>total</small>
					</div>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="widget style1 navy-bg">
                    <div class="row vertical-align">
                        <div class="col-xs-3">
                            <h2><i class="fa fa-user"></i> <small style="color:inherit;">max</small></h2>
                        </div>
                        <div class="col-xs-9 text-right">
                            <h2 class="font-bold">104</h2>
                        </div>
                    </div>
                </div>
				<div class="widget style1 navy-bg">
                    <div class="row vertical-align">
                        <div class="col-xs-3">
                           <h2><i class="fa fa-user"></i> <small style="color:inherit;">min</small></h2>
                        </div>
                        <div class="col-xs-9 text-right">
                            <h2 class="font-bold">23</h2>
                        </div>
                    </div>
                </div>
				<div class="widget style1 navy-bg">
                    <div class="row vertical-align">
                        <div class="col-xs-3">
                            <h2><i class="fa fa-user"></i> <small style="color:inherit;">avg</small></h2>
                        </div>
                        <div class="col-xs-9 text-right">
                            <h2 class="font-bold">546</h2>
                        </div>
                    </div>
                </div>				
			</div>

			<div class="col-lg-5">
				<table class="table table-hover">
					<thead>
					<tr>
						<th>Device#</th>
						<th>Max wait time</th>
						<th>Value</th>
					</tr>
					</thead>
					<tbody>
					<tr>
						<td>1</td>
						<td>25 min</td>
						<td class="text-navy"> <i class="fa fa-level-up"></i> 40% </td>
					</tr>
					<tr>
						<td>2</td>
						<td>15 min</td>
						<td class="text-warning"> <i class="fa fa-level-down"></i> -20% </td>
					</tr>
					<tr>
						<td>3</td>
						<td>10 min</td>
						<td class="text-navy"> <i class="fa fa-level-up"></i> 26% </td>
					</tr>
					</tbody>
				</table>			
		</div>	
		</div>	
		
		<div class="row">
			<div class="col-lg-12">
                <div class="ibox float-e-margins">
					<div class="ibox-title">									  
						<h5>Person count (24h) <small></small> <a class="fullscreen-link"><i class="fa fa-expand"></i></a></h5>
						<div class="clearfix"></div>
					</div>
					<div class="ibox-content">
						<div class="row">
							<div class="col-lg-8">				
								<div id="sign_chart"></div>
							</div>
							<div class="col-lg-4">				
								<div id="time_chart"></div>
							</div>							
						</div>
					</div>
                </div>
			</div>		
		</div>
		
	</div>

	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'/Src/controllers/home.controller.php';?>" />
	
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

	
		var url_str = $('#url_string').val();
		// 1 hour
		var refresh = 3600000;
		var interval;
		

		getSignalLoad(url_str);
		
		interval = setInterval( function () {
			getSignalLoad(url_str);
		}, refresh );	
		
		// Set chart zoom on mobile
		if ($(this).width() < 769) {
			compchart.zoom([0, 5]); 
		}
                $("#sparkline1").sparkline([34, 43, 43, 35, 44, 32, 44, 52], {
                    type: 'line',
                    width: '100%',
                    height: '50',
                    lineColor: '#f6a821',
                    fillColor: "transparent"
                });		
	});	
	
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
			columns: []
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
			pattern: ["#f6a821", "#1C84C6",  "#d3d3d3", "#1C84C6", "#bababa", "#79d2c0","#1ab394"]
		},		
		zoom: {
			enabled: true
		}			
	});		

	function getSignalLoad(url){
		$.ajax({
			type: 'GET',
			url: url+"?get=signalload",
			success: function(data) {
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
					compchart.ygrids([
						{value: data.avg_last, class: data.avg_last > data.avg_now ? 'gridorange': '', text: 'Gemiddelde vorige week ' + data.avg_last, position: 'start'},
						{value: data.avg_now, class: data.avg_now > data.avg_last ? 'gridorange': 'gridgreen', text: 'Gemiddelde deze week ' + data.avg_now}
					]);					
					compchart.data.names({
						signal: 'Movement count',
						trend: 'Trend'
					});					
				} else {
					compchart.destroy();	
				}
						
			}
		});		
	}	
	
	</script>