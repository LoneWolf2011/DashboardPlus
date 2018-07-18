    <div class="wrapper animated fadeInRight">
		<div class="row">
			<div class="col-lg-12">
				<h2>Poort monitor (RMC/ASB/KPN)<small>  </small></h2>
				<div id="p_monitor_asb"></div>		
			</div>			
        </div>
		<div class="row">
			<div class="col-lg-12">
				<div id="p_monitor_gw"></div>		
			</div>			
        </div>		
        <div class="row">
            <div class="col-lg-12">
				<h2>Signaal belasting 24h<small></small></h2>
				<div id="sign_chart"></div>
            </div>
        </div>		
    </div>
							
	<input type="text" hidden id="url_query" value="<?= $_SERVER['QUERY_STRING']; ?>" />	
	<input type="text" hidden id="mac_adres"  />	
	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'/Src/controllers/tools.controller.php';?>" />	
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
		var refresh = 40000;
		var interval;
		
		getPortMonitoring(url_str);
		//getLocationSignalCount(url_str);
		getSignalLoad(url_str);
				
		interval = setInterval( function () {
			//getLocationSignalCount(url_str);
			getSignalLoad(url_str);
			getPortMonitoring(url_str);
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
			pattern: ["#1ab394", "#1C84C6",  "#d3d3d3", "#1C84C6", "#bababa", "#79d2c0","#1ab394"]
		},		
		zoom: {
			enabled: true
		}			
	});	

	var sparklineCharts = function(){
		//console.log("#ip_line"+id_class);
        $(".sparkline").sparkline('html', {
            type: 'line',
            width: '100%',
            height: '50',
            lineColor: '#1ab394',
            fillColor: "transparent"
        });		
		
		$('.p_status').sparkline('html',{
			type: 'tristate',
			barWidth: '10%',
			barSpacing: '2',
			height: '50',
			colorMap: {'4': '#f0ad4e', '2': '#ed5565', '1': '#007E33' , '0': 'gray'},
			tooltipFormat: 'Status: <b>{{value:levels}}</b>',
			tooltipValueLookups: {
				levels: $.range_map({ '4': 'Signal', '2': 'Down', '1': 'Up', '0': 'Disabled'})
			}						
		});
	
	};
				
	function getPortMonitoring(url){
		$.ajaxq("monitoring",{
			type: 'GET',
			url: url+"?get=port&feps",
			success: function(data) {
				if(data.status != 0){
					$('#p_monitor_asb').html(data.block_asb);
					$('#p_monitor_gw').html(data.block_gw);
					//$('#p_monitor_aoip').html(data.blocks_gw);
					sparklineCharts();				
				} else {
					$('#p_monitor_asb').html('');
					$('#p_monitor_gw').html('');
					//$('#p_monitor_aoip').html('');
				}						
			}
		});		
	}


	function getSignalLoad(url){
		$.ajaxq("signalload",{
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
						signal: 'Signaal belasting',
						trend: 'Trend'
					});					
				} else {
					compchart.destroy();	
				}
						
			}
		});		
	}
	
	</script>