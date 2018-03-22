	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<div class="col-lg-4">
				<div class="widget yellow-bg p-lg text-center">
					<div class="m-b-md">
						<i class="fa fa-user fa-4x"></i>
						<h1 class="m-xs">520</h1>
						<h3 class="font-bold no-margins">
							Persons
						</h3>
						<small>count</small>
					</div>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="widget style1 navy-bg">
                    <div class="row vertical-align">
                        <div class="col-xs-3">
                            <h2>MAX</h2>
                        </div>
                        <div class="col-xs-9 text-right">
                            <h2 class="font-bold">104</h2>
                        </div>
                    </div>
                </div>
				<div class="widget style1 navy-bg">
                    <div class="row vertical-align">
                        <div class="col-xs-3">
                           <h2>MIN</h2>
                        </div>
                        <div class="col-xs-9 text-right">
                            <h2 class="font-bold">23</h2>
                        </div>
                    </div>
                </div>
				<div class="widget style1 navy-bg">
                    <div class="row vertical-align">
                        <div class="col-xs-3">
                            <h2>AVG</h2>
                        </div>
                        <div class="col-xs-9 text-right">
                            <h2 class="font-bold">546</h2>
                        </div>
                    </div>
                </div>				
			</div>			
		</div>	
		
		<div class="row">
			<div class="col-lg-12">
                <div class="ibox float-e-margins">
                  <div class="ibox-title">									  
                    <h5>Log naam <?= date("Y-m-d").".txt";?> <small><b>Folder:</b> <?= date("Y");?></small></h5>
                    <div class="clearfix"></div>
                  </div>
                  <div class="ibox-content"><div id="sign_chart"></div></div>
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