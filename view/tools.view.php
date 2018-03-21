    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5 >Settings</h5>
					</div>
					<div class="ibox-content">
						<button class="btn btn-primary" onclick="popupWindow('graphs/', 'graphs', 1980, 1080 ); return false;" >Open responds tijden</button>								
						<button class="btn btn-primary" onclick="popupWindow('port/', 'port', 1980, 1080 ); return false;" >Open port monitor</button>								
					</div>
				</div>
            </div>		
            <div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5 >Settings</h5>
					</div>
					<div class="ibox-content">
						<button class="btn btn-primary" onclick="popupWindow('events/', 'events', 1980, 1080 ); return false;" >Open pending events</button>
						<button class="btn btn-primary" onclick="popupWindow('events_group/', 'events_group', 1980, 1080 ); return false;" >Open grouped events</button>
					</div>
				</div>
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


	</script>