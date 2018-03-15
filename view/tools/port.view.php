    <div class="wrapper animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
				<h2>Poort monitor (ASB)<small>  </small></h2>
				<div id="p_monitor_asb"></div>
            </div>
		
        </div>
        <div class="row">
            <div class="col-lg-12">
				<h2>Poort monitor (KPN)<small>  </small></h2>
				<div id="p_monitor_kpn"></div>
            </div>
		
        </div>		
        <div class="row">
            <div class="col-lg-12">
				<h2>RMC monitor <small>  </small></h2>
				<div id="p_monitor_aoip"></div>
            </div>
		
        </div>		
    </div>
							
	<input type="text" hidden id="url_query" value="<?= $_SERVER['QUERY_STRING']; ?>" />	
	<input type="text" hidden id="mac_adres"  />	
	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'Src/controllers/tools.controller.php';?>" />	
	<?php
		// View specific scripts
		array_push($arr_js, '/mdb/js/plugins/sparkline/jquery.sparkline.min.js');
		
	?>	
	
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.$js.'"></script>';
		}		
	?>	

	<script>
	$(document).ready(function () {	
		var url_str = $('#url_string').val();
		var refresh = 20000;
		var interval;
		
		getPortMonitoring(url_str);
				
		interval = setInterval( function () {
			getPortMonitoring(url_str);

		}, refresh );	
		
		
	});	
	
	var sparklineCharts = function(){
		//console.log("#ip_line"+id_class);
		$('.p_status').sparkline('html',{
			type: 'tristate',
			barWidth: '10%',
			barSpacing: '2',
			height: '50',
			colorMap: {'4': '#f0ad4e', '2': '#ed5565', '1': '#007E33' , '0': '#e7eaec'},
			tooltipFormat: 'Status: <b>{{value:levels}}</b>',
			tooltipValueLookups: {
				levels: $.range_map({ '4': 'Signal', '2': 'Down', '1': 'Up', '0': 'Disabled'})
			}						
		});
	
	};
				
	function getPortMonitoring(url){
		$.ajax({
			type: 'GET',
			url: url+"?get=port&feps",
			async: false,
			success: function(data) {
				if(data.status != 0){
					$('#p_monitor_asb').html(data.block_asb);
					$('#p_monitor_kpn').html(data.block_kpn);
					$('#p_monitor_aoip').html(data.blocks_gw);
					sparklineCharts();
					
				} else {
					$('#p_monitor_asb').html('');
					$('#p_monitor_kpn').html('');
					$('#p_monitor_aoip').html('');
				}						
			}
		});		
	}
	
	</script>