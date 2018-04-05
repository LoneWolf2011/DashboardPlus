	<?php
		$site_conn = new SafeMySQL();

	?>
    <div class="wrapper wrapper-content animated fadeInRight">
        <h2 class="m-b-xs"><i class="pe pe-7s-browser text-warning m-r-xs"></i> Zone <?= $_GET['id'];?></h2>
		
		<div id="devices"></div>
		<div id="avg_wait_chart'.$i.'"></div>
		
    </div>
	
	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'/Src/controllers/zone.controller.php';?>" />
	<input type="text" hidden id="url_site" value="<?= (isset($_GET['site'])) ? $_GET['site'] : 1;?>" />
	<input type="text" hidden id="url_zone" value="<?= (isset($_GET['id'])) ? $_GET['id'] : '';?>" />

	<?php
		// View specific scripts
		array_push($arr_js, '/js/plugins/d3/d3.min.js');
		array_push($arr_js, '/js/plugins/c3/c3.min.js');
		
	?>	
	
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>	

	<script>
	$(document).ready(function() {
		
		getZoneDetails();

	});

		
	var url_str = $('#url_string').val();
	var url_zone = $('#url_zone').val();

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

	function getZoneDetails() {
		ajaxObj.options.url = url_str + "?get=details&zone="+url_zone;
		$.ajax(ajaxObj.options).done(function(data) {
			if (data.status != 0) {
				$('#devices').empty();
				$('#devices').html(data.devices);
				
				var ticksValues = [15,30,45,60];
				var ticks;
				$.each( data.devices_wait_time, function( key, value ) {
					var chart = c3.generate({
						bindto: '#avg_wait_chart'+key,
						data: {
							columns: [
								['min', value]
							],
							type: 'gauge'
						},
						gauge: {
							label: {
								format: function(value, ratio) {
									return value+' min';
								},
								show: false // to turn off the min/max labels.
							},
							min: 0, // 0 is default, //can handle negative min e.g. vacuum / voltage / current flow / rate of change
							max: 60, // 100 is default
							units: ' min',
							width: 10 // for adjusting arc thickness
						},
						color: {
							pattern: ['#5cb85c', '#f0ad4e', '#f0ad4e', '#d9534f'], // the three color levels for the percentage values.
							threshold: {
								unit: 'value', // percentage is default
								max: 100, // 100 is default
								values: [15, 20, 35, 40]
							}
						},
						size: {
							height: 150
						}
					});


					ticks = d3.select('.c3-chart-arcs')
					.append('g')
					.classed('ticks', true)
					.selectAll("line")
					.data(ticksValues).enter()
					.append("line")
					.attr("x1", 0)
					.attr("x2", 0)
					.attr("y1", -chart.internal.radius+8)
					.attr("y2", -chart.internal.radius)
					.attr("transform", function (d) {
						var min = chart.internal.config.gauge_min,
							max = chart.internal.config.gauge_max,
							ratio = d / (max - min),
							rotate = (ratio * 180) - 90;
						return "rotate(" + rotate + ")";
					})
					.attr('data-reached', function(d){ 
						var val = chart.internal.data.targets[0].values[0].value;
						return d < val ? 'yes' : 'no';
					});	
				
				});	
				
				
			} else {}
		}).fail(ajaxObj.fail).always(ajaxObj.get(getZoneDetails, 2000));
	}
	</script>	