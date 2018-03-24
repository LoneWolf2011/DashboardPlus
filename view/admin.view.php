    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center m-t-lg">
                    <h1 data-i18n="[html]admin.h1">
                        Welcome ADMIN
                    </h1>
                    
					<p>
                        <div class="sk-spinner sk-spinner-wave">
                            <div class="sk-rect1"></div>
                            <div class="sk-rect2"></div>
                            <div class="sk-rect3"></div>
                            <div class="sk-rect4"></div>
                            <div class="sk-rect5"></div>
                        </div>
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