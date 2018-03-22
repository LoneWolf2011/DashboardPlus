    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center m-t-lg">
                    <h1 data-i18n="[html]admin.h1">
                        Welcome ADMIN
                    </h1>
                    
					<p>
						<button class="btn btn-primary" onclick="popupWindow('graphs/', 'graphs', 1980, 1080 ); return false;" >Open responds tijden</button>
					</p>
					<small data-i18n="[html]admin.small"> Please standby...</small>
                </div>
            </div>
        </div>
    </div>
		
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>	

	<script>
	$(document).ready(function () {
		setTimeout(function() {
			window.location.href = <?= json_encode(URL_ROOT);?>+'/view/home/';
		}, 3000);
	});
	</script>	