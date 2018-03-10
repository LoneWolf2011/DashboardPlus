    <div class="wrapper animated fadeInRight">
        <div class="row">
            <div class="col-lg-4">
				<h2>Reactietijd prio 1 <small> gemiddeld: <span id="avg_pie1"></span></small></h2>
				<div id="piechart1"></div>
            </div>

            <div class="col-lg-4">
				<h2>Reactietijd prio 2 <small> gemiddeld: <span id="avg_pie2"></span></small></h2>
				<div id="piechart2"></div>	
            </div>
            <div class="col-lg-4">
				<h2>Top 10 meeste signalen deze week </h2>
				<table class="table no-margins" id="signal_table" style="color: white;"></table>
            </div>			
        </div>
        <div class="row">
            <div class="col-lg-12">
				<h2>Signaal belasting<small>  <span></span></small></h2>
				<div id="sign_chart"></div>
            </div>
		
        </div>		
    </div>
	
	<input type="text" hidden id="url_query" value="<?= $_SERVER['QUERY_STRING']; ?>" />	
	<input type="text" hidden id="mac_adres"  />	
	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'Src/controllers/tools.controller.php';?>" />	
	
	<?php
		// View specific scripts
		array_push($arr_js, '/mdb/js/plugins/sparkline/jquery.sparkline.min.js');
		array_push($arr_js, '/mdb/js/plugins/d3/d3.min.js');
		array_push($arr_js, '/mdb/js/plugins/c3/c3.min.js');
		
	?>		
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.$js.'"></script>';
		}		
	?>	

	<script>
	$(document).ready(function () {	
		var url_str = $('#url_string').val();
		// 1 hour
		var refresh = 3600000;
		var interval;
		
		getOperatorThresholds(url_str, 1);
		getOperatorThresholds(url_str, 2);
		getSignalLoad(url_str);
		getLocationSignalCount(url_str);
		
		interval = setInterval( function () {
			getOperatorThresholds(url_str, 1);
			getOperatorThresholds(url_str, 2);
			getSignalLoad(url_str);
		}, refresh );	
		
		
		// Change comp chart style
		$("#chart_style").change(function(){
			var type_style = $(this).val();
			piechart.transform(type_style)		
		});	
	});	
	
	var piechart1 = c3.generate({
		bindto: '#piechart1',
		data: {
			columns: []
		},
		size: {
			height:500
		},		
		type: 'pie'	
	});
	var piechart2 = c3.generate({
		bindto: '#piechart2',
		data: {
			columns: []
		},
		size: {
			height:500
		},		
		type: 'pie'	
	});	
	var compchart = c3.generate({
		bindto: '#sign_chart',
		data: {
			x: 'x',
			xFormat: '%H:%M',
			columns: []
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
			pattern: ["#1ab394",  "#d3d3d3", "#1C84C6", "#bababa", "#79d2c0","#1ab394"]
		},		
		zoom: {
			enabled: true
		}			
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

	
	function getLocationSignalCount(url){
		$.ajax({
			type: 'GET',
			url: url+"?get=locationsignalcount",
			success: function(data) {
				if(data.status != 0){
					$('#signal_table').html(data.rows);		
				} else {
					$('#signal_table').html('');	
				}						
			}
		});		
	}
	
	function getSignalLoad(url){
		$.ajax({
			type: 'GET',
			url: url+"?get=signalload",
			success: function(data) {
				if(data.status != 0){
					compchart.load({
						columns: [
							data.hours,
							data.signal
						],
						type: 'area-spline'						
					});
					compchart.ygrids([
						{value: data.avg_last, class: data.avg_last > data.avg_now ? 'gridorange': '', text: 'Gemiddelde vorige week ' + data.avg_last},
						{value: data.avg_now, class: data.avg_now > data.avg_last ? 'gridorange': 'gridgreen', text: 'Gemiddelde deze week ' + data.avg_now}
					]);					
					compchart.data.names({
						signal: 'Signaal belasting afgelopen 24h'
					});					
				} else {
					compchart.destroy();	
				}
				$('#spinner2').css('display','none');
						
			}
		});		
	}	
	
	function getOperatorThresholds(url, prio){
		$.ajax({
			type: 'POST',
			url: url+"?get=threshold",
			data: {prio: prio},
			success: function(data) {
				if(data.status != 0){
					if(data.prio == 1){
						$('#avg_pie1').html(data.avg);
						piechart1.load({
							columns: [
								data.green,
								data.orange,
								data.red
							],					
							colors: {
								green: 'rgba(26, 179, 148, 0.28)',
								orange: 'rgb(248, 172, 89, 0.28)',
								red: 'rgba(237, 85, 101, 0.28)'
							},						
							type: 'pie'						
						});
						piechart1.data.names({
							green: 'Binnen 60 sec', 
							orange: 'Binnen 120 sec', 
							red: 'Meer dan 120 sec'
						});
					}
					
					if(data.prio == 2){
						$('#avg_pie2').html(data.avg);
						piechart2.load({
							columns: [
								data.green,
								data.orange,
								data.red
							],					
							colors: {
								green: 'rgba(26, 179, 148, 0.28)',
								orange: 'rgb(248, 172, 89, 0.28)',
								red: 'rgba(237, 85, 101, 0.28)'
							},						
							type: 'pie'						
						});
						piechart2.data.names({
							green: 'Binnen 120 sec', 
							orange: 'Binnen 240 sec', 
							red: 'Meer dan 240 sec'
						});
					}					
				} else {
					piechart1.destroy();	
				}
				$('#spinner2').css('display','none');
						
			}
		});		
	}
	

	</script>