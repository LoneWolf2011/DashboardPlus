    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5 >Settings</h5>
					</div>
					<div class="ibox-content">
						<button class="btn btn-primary" onclick="popupWindow('/mdb/view/tools/graphs/', 'graphs', 1980, 1080 ); return false;" >Open responds tijden</button>								
					</div>
				</div>
            </div>		
            <div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5 >Settings</h5>
					</div>
					<div class="ibox-content">
						<button class="btn btn-primary" onclick="popupWindow('/mdb/view/tools/events/', 'events', 1980, 1080 ); return false;" >Open pending events</button>
					</div>
				</div>
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

	</script>