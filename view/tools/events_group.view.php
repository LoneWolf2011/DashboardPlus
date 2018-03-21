    <div class="wrapper animated fadeInRight">
	
		<h2 id="p_h2"></h2>
        <div class="row" id="p_grouped">
						
        </div>	
        <div class="row">
            <div class="col-lg-12">
				<h2>Overige meldingen<small> </small></h2>
				<table class="table" id="p_events_task" style="color: white;"></table>
            </div>
		
        </div>		
    </div>
	
	<input type="text" hidden id="url_query" value="<?= $_SERVER['QUERY_STRING']; ?>" />	
	<input type="text" hidden id="mac_adres"  />	
	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'/Src/controllers/tools.controller.php';?>" />	
	
	
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>	

	<script>
	$(document).ready(function () {	
		var url_str = $('#url_string').val();
		// 1 sec
		var refresh = 1000;
		var interval;
		
		getGoupedEvents(url_str);
		getPendingEventsTasks(url_str);
		
		interval = setInterval( function () {
			getGoupedEvents(url_str);
			getPendingEventsTasks(url_str);
		}, refresh );	
		
	});	

	function getPendingEventsTasks(url){
		$.ajax({
			type: 'GET',
			url: url+"?get=events&tasks",
			async: false,
			success: function(data) {
				if(data.status != 0){
					$('#p_events_task').html(data.rows);			
				} else {
					$('#p_events_task').html('');
				}						
			}
		});		
	}
	
	function getGoupedEvents(url){
		$.ajax({
			type: 'GET',
			url: url+"?get=events&grouped",
			async: false,
			success: function(data) {
				if(data.status != 0){
					$('#p_grouped').html(data.blocks);		
					$('#p_h2').html('Gebundelde meldingen');		
				} else {
					$('#p_grouped').html('');	
					$('#p_h2').html('');	
				}						
			}
		});		
	}
	

	</script>