    <div class="wrapper animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
				<h2>Actieve taken<small>  <span></span></small></h2>
				<table class="table" id="p_events" style="color: white;"></table>
            </div>
		
        </div>		
    </div>
	
	<input type="text" hidden id="url_query" value="<?= $_SERVER['QUERY_STRING']; ?>" />	
	<input type="text" hidden id="mac_adres"  />	
	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'Src/controllers/tools.controller.php';?>" />	
	
	
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.$js.'"></script>';
		}		
	?>	

	<script>
	$(document).ready(function () {	
		var url_str = $('#url_string').val();
		// 1 hour
		var refresh = 5000;
		var interval;
		
		getPendingEvents(url_str);
		
		interval = setInterval( function () {
			getPendingEvents(url_str);
		}, refresh );	
		

	});	
	
	function getPendingEvents(url){
		$.ajax({
			type: 'GET',
			url: url+"?get=events&tasks",
			success: function(data) {
				if(data.status != 0){
					$('#p_events').html(data.rows);		
				} else {
					$('#p_events').html('');	
				}						
			}
		});		
	}
	

	</script>