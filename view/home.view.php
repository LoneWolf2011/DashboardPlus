	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<div class="panel m-b-none">
				<div class="panel-body">
					<h2 class="m-b-xs"><i class="pe pe-7s-graph1 text-warning m-r-xs"></i> Activity</h2>
					Real time person tracking
					<hr>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-5">
				<div class="panel">
					<div class="panel-body">

						<div id="serverMap"></div>

					</div>
				</div>
			</div>
			<div class="col-lg-7">
				<div class="panel panel-filled">
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table" id="logsTable">
								<thead>
									<tr>
										<th>IP</th>
										<th>Device type</th>
										<th>Time</th>
										<th>Out</th>
										<th>Queue</th>
									</tr>
								</thead>
								<tbody id="logsTableBody">
									
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'/Src/controllers/home.controller.php';?>" />
	<input type="text" hidden id="last_id" value="" />
	
	<?php
		// View specific scripts
		array_push($arr_js, '/js/plugins/moment/moment.js');
	?>			
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>	


<script>
    $(document).ready(function () {
		getSiteTable();
		refreshSitesTable();
		getSiteActivity();
    });
	
	var url_str = $('#url_string').val();
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

	function getSiteTable() {
		ajaxObj.options.url = url_str + "?get=gettable";
		$.ajax(ajaxObj.options).done(function(data) {
			if (data.status != 0) {
				$('#logsTableBody').html(data.rows);
				var id = data.last_id;
				$('#last_id').val(data.last_id);
			} else {}
		}).fail(ajaxObj.fail);
	}	

	function refreshSitesTable() {
		ajaxObj.options.url = url_str + "?get=refreshtable&id="+$('#last_id').val();
		$.ajax(ajaxObj.options).done(function(data) {
			if (data.status != 0) {
				if(data.row_count != 0){
					
					for(i=0; i < data.row_count; i++){
						$('#logsTable tbody tr:first').remove();
					}						
				}

				$('#logsTable').append(data.rows);	
				$('#last_id').val(data.last_id);
			} else {}
		}).fail(ajaxObj.fail).always(ajaxObj.get(refreshSitesTable,5000));
	}
	
	function getSiteActivity() {
		ajaxObj.options.url = url_str + "?get=sites";
		$.ajax(ajaxObj.options).done(function(data) {
			if (data.status != 0) {
				$('#serverMap').html(data.sites);
			} else {}
		}).fail(ajaxObj.fail).always(ajaxObj.get(getSiteActivity,5000));
	}	
	
</script>