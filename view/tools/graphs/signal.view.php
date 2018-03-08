    <div class="wrapper wrapper-content animated fadeInRight">
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
		var refresh = 5000;
		var interval;
		
		getSignalLoad(url_str);
		
		interval = setInterval( function () {
			getSignalLoad(url_str);
		}, refresh );	
		
		
		// Change comp chart style
		$("#chart_style").change(function(){
			var type_style = $(this).val();
			piechart.transform(type_style)		
		});	
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
			height:800
		},		
		axis: {
			x: {
				type: 'category',
				tick: {
					rotate: 75,
					multiline: false,					
					//count: 10,
					fit: true,
					format: '%H:%M',
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
		

	</script>