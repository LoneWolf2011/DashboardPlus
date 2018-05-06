    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5 >Tools</h5>
					</div>
					<div class="ibox-content">
						<button class="btn btn-primary" onclick="popupWindow('graphs/', 'graphs', 1980, 1080 ); return false;" >Open responds tijden</button>								
						<button class="btn btn-primary" onclick="popupWindow('port/', 'port', 1980, 1080 ); return false;" >Open port monitor</button>								
						<button class="btn btn-primary" onclick="popupWindow('http://172.16.8.12/beheer/tools/plodash/', 'port', 1980, 1080 ); return false;" >PLO dashboard</button>								
					</div>
				</div>
            </div>		
            <div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5 >Events</h5>
					</div>
					<div class="ibox-content">
						<button class="btn btn-primary" onclick="popupWindow('events/', 'events', 1980, 1080 ); return false;" >Open pending events</button>
						<button class="btn btn-primary" onclick="popupWindow('events_group/', 'events_group', 1980, 1080 ); return false;" >Open grouped events</button>
					</div>
				</div>
            </div>			
        </div>
        <div class="row">
            <div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5 >Maps</h5>
					</div>
					<div class="ibox-content">
						<button class="btn btn-primary" onclick="popupWindow('maps/', 'maps', 1980, 1080 ); return false;" >Open map</button>														
					</div>
				</div>
            </div>		
            <div class="col-lg-6">

            </div>			
        </div>		
    </div>
	
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		

	?>	

	<script>


	</script>