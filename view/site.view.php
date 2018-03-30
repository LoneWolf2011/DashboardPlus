	<?php
		$site_conn = new SafeMySQL();
	?>
	<div class="wrapper wrapper-content animated fadeInRight">
		<h2 class="m-b-xs"><i class="pe pe-7s-browser text-warning m-r-xs"></i> Site</h2>
		<div class="row">
			<div class="col-lg-2">
				<div class="ibox float-e-margins">
					<div class="ibox-content">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Site</th>
									<th><?= $site_conn->getOne("SELECT `site_name` FROM sensor_sites WHERE site_id = ?s", $_GET['site']);?></th>
								</tr>
							</thead>
							<tbody id="location_tr"></tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-content">
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>ZoneID</th>
										<th>Name</th>
										<th>Devices</th>
										<th>Current wait time</th>
										<th>Current queue</th>
										<th>Show graph</th>
									</tr>
								</thead>
								<tbody id="table_rows"></tbody>
							</table>
						</div>
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
						<h5>Person count site: <?= $site_conn->getOne("SELECT `site_name` FROM sensor_sites WHERE site_id = ?s", $_GET['site']);?> <small></small> <a class="fullscreen-link"><i class="fa fa-expand"></i></a></h5>
						<div class="clearfix"></div>
					</div>
					<div class="ibox-content">
						<div class="row">
							<div class="col-lg-12">
								<div id="sign_chart"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'/Src/controllers/site.controller.php';?>" />
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
	$(document).ready(function() {
		getCount();
		getSignalLoad();
		getZonesTable();
		
		var zone_name_arr = [];
		
		$('#table_rows').on('click', '.zone_graph', function() {
			var zone_data = $(this).attr('value');
			var zone_name = $(this).attr('rel');
			var zone_arr = zone_data.split(';');
			zone_arr.unshift(zone_name);
			if ($.inArray(zone_name, zone_name_arr) == -1) {
				//add to array
				zone_name_arr.push(zone_name);
			} else {
				zone_name_arr.splice($.inArray(zone_name, zone_name_arr), 1);
			}
			//console.log(zone_name_arr);
			compchart.load({
				columns: [
					zone_arr
				],
				type: 'bar'
			});
			compchart.groups([
				zone_name_arr
			]);
		});
		
		// Set chart zoom on mobile
		if ($(this).width() < 769) {
			compchart.zoom([0, 5]);
		}
	});
	
	var url_str = $('#url_string').val();
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
			height: 500
		},
		axis: {
			x: {
				type: 'category'
			}
		},
		color: {
			pattern: ["#f6a821", "#e6ee9c", "#90a4ae", "4dd0e1", "#388e3c ", "#00897b", "#ff5722"]
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

	function getCount() {
		ajaxObj.options.url = url_str + "?get=peoplecount&site=" + $('#url_site').val();
		$.ajax(ajaxObj.options).done(function(data) {
			if (data.status != 0) {
				$('#c_all').html(data.c_all);
				$('#c_total').html(data.c_total);
				$('#location_tr').html(data.location);
			} else {}
		}).fail(ajaxObj.fail).always(ajaxObj.get(getCount, 5000));
	}

	function getSignalLoad() {
		ajaxObj.options.url = url_str + "?get=signalload&site=" + $('#url_site').val(),
		$.ajax(ajaxObj.options).done(function(data) {
			if (data.status != 0) {
				compchart.load({
					columns: [
						data.hours,
						data.signal,
						data.fw,
						data.bw,
						data.queue,
						data.trend
					],
					type: 'area-spline',
					colors: {
						trend: '#fff'
					},
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
					fw: 'People out',
					bw: 'People in',
					queue: 'People in queue',
					trend: 'Trend'
				});
			} else {
				compchart.destroy();
			}
		}).fail(ajaxObj.fail).always(ajaxObj.get(getSignalLoad, 3600000));
	}

	function getZonesTable() {
		ajaxObj.options.url = url_str + "?get=zonestable&site=" + $('#url_site').val();
		$.ajax(ajaxObj.options).done(function(data) {
			if (data.status != 0) {
				//$('#table_rows').html(data.rows);
				$('#table_rows').empty();
				$.each(data.rows, function(value, val) {
					$('#table_rows').append($('<tr><td>' + val[0] + '</td><td>' + val[1] + '</td><td>' + val[2] + '</td><td>' + val[3] + '</td><td>' + val[4] + '</td><td>' + val[5] + '</td></tr>'));
				});
			} else {}
		}).fail(ajaxObj.fail).always(ajaxObj.get(getZonesTable, 10000));
	}
	
	</script>