    <div class="wrapper wrapper-content animated fadeInRight">
        <h2 class="m-b-xs"><i class="pe pe-7s-signal text-warning m-r-xs"></i>  Zone <?= $_GET['id'];?> </h2>
		<hr>		
        <div class="row">
            <div class="col-lg-12">
                <div class="text-center m-t-lg">
                    <h1 >
                       
                       
                    </h1>
                    
					<p>

					</p>
					<small > <?= URL_ROOT.$view_url.'?id=1';?></small>
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

	</script>	