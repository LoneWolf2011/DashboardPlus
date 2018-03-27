	<?php
		$site_conn = new SafeMySQL();

	?>
    <div class="wrapper wrapper-content animated fadeInRight">
        <h2 class="m-b-xs"><i class="pe pe-7s-browser text-warning m-r-xs"></i> Zone <?= $_GET['id'];?></h2>
		
		<div id="devices"></div>

    </div>
	
	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'/Src/controllers/zone.controller.php';?>" />
	<input type="text" hidden id="url_site" value="<?= (isset($_GET['site'])) ? $_GET['site'] : 1;?>" />
	<input type="text" hidden id="url_zone" value="<?= (isset($_GET['id'])) ? $_GET['id'] : '';?>" />
	
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
			} else {}
		}).fail(ajaxObj.fail).always(ajaxObj.get(getZoneDetails, 10000));
	}
	</script>	