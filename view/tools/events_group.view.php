    <div class="wrapper animated fadeInRight">
		<h2>Gebundelde meldingen<small>  <span></span></small></h2>
        <div class="row" id="p_grouped">
						
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
		// 1 sec
		var refresh = 1000;
		var interval;
		
		getGoupedEvents(url_str);
		
		interval = setInterval( function () {
			getGoupedEvents(url_str);
		}, refresh );	
		

	});	
	
	function getGoupedEvents(url){
		$.ajax({
			type: 'GET',
			url: url+"?get=events&grouped",
			success: function(data) {
				if(data.status != 0){
					$('#p_grouped').html(data.blocks);		
				} else {
					$('#p_grouped').html('');	
				}						
			}
		});		
	}
	

	</script>