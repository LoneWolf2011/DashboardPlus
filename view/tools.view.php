    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5 >Settings</h5>
					</div>
					<div class="ibox-content">
						<button class="btn btn-primary" onclick="popupWindow('/mdb/view/tools/graphs/', 'pie', 1980, 1080 ); return false;" >Open responds tijden</button>				
						<button class="btn btn-primary" onclick="popupWindow('/mdb/view/tools/graphs/signal/', 'line', 1980, 1080 ); return false;" >Open signaal load</button>				
					</div>
				</div>
            </div>		
		
        </div>
		<div class="row">
            <div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5 ></h5>
					</div>
					<div class="ibox-content">
			
					</div>
				</div>
            </div>
			
            <div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5 ></h5>
					</div>
					<div class="ibox-content">

					</div>
				</div>
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
		var refresh = 5000;
		var interval;
		
		getOperatorThresholds(url_str, 1);
		getOperatorThresholds(url_str, 2);
		getOperatorResponeTime(url_str);
		
		interval = setInterval( function () {
			getOperatorThresholds(url_str);
			getOperatorThresholds(url_str, 2);
			getOperatorResponeTime(url_str);
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
			height:550
		},		
		type: 'pie'	
	});
	var piechart2 = c3.generate({
		bindto: '#piechart2',
		data: {
			columns: []
		},
		size: {
			height:550
		},		
		type: 'pie'	
	});	
	var compchart = c3.generate({
		bindto: '#comp_chart',
		data: {
			x: 'x',
			columns: []
		},
		type: 'spline',
		size: {
			height:550
		},			
		axis: {
			x: {
				type: 'category',
				tick: {
					rotate: 75,
					multiline: false,					
					//count: 10,
					fit: true,
					centered: true
				}
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
	
	function getOperatorResponeTime(url){
		$.ajax({
			type: 'GET',
			url: url+"?get=responsetime",
			success: function(data) {
				if(data.status != 0){
					compchart.load({
						columns: [
							data.hours,
							data.avg
						],
					type: 'area-spline'							
					});
					compchart.data.names({
						avg: 'Gemiddelde response tijd prio 1 & 2 (sec)'
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
						piechart1.load({
							columns: [
								data.green,
								data.orange,
								data.red
							],					
							colors: {
								green: '#1ab394',
								orange: '#f8ac59',
								red: '#ed5565'
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
						piechart2.load({
							columns: [
								data.green,
								data.orange,
								data.red
							],					
							colors: {
								green: '#1ab394',
								orange: '#f8ac59',
								red: '#ed5565'
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