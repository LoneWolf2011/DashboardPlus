	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="row">
			<!-- Logging vandaag -->
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Log naam <?= date("Y-m-d").".txt";?>
						 <small><b>Folder:</b> <?= date("Y");?>
						</small></h5>
						<div class="clearfix"></div>
					</div>
					<div class="ibox-content" >
						<div id="log">
						
						</div>
					
					</div>
				</div>
			</div><!-- /Logging vandaag -->
		</div>
		<div class="row">
			<!-- Logging vandaag -->
			<div class="col-md-6 col-sm-12 col-xs-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Log files<small></small></h5>
						<div class="clearfix"></div>
					</div>
					<div class="ibox-content">
						<table class='table' id='datatable-log'>
							<thead>
								<tr>
									<th align='left'>Datum:</th>
								</tr>
							</thead>
							<?php
							    $year = date("Y");  
							    if($handle=opendir('../src/logs/'.date("Y"))){
							        while(false !==($file = readdir($handle))) {
							            if(strpos($file, $year.'-' ) === 0) {
							                echo "<tr><td>Log: <a class='link' href='".URL_ROOT."/src/logs/".$year."/". $file ."'>".$file."</a></td></tr>"; 
							            }
							        }
							        closedir($handle);  
							    }
							?>
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-6 col-sm-12 col-xs-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5>Error log files<small></small></h5>
						<div class="clearfix"></div>
					</div>
					<div class="ibox-content">
						<table class='table' id='datatable-error'>
							<thead>
								<tr>
									<th align='left'>Datum:</th>
								</tr>
							</thead>
							<?php
							    if($handle=opendir('../src/logs/Errors')){
							    
							        while(false !==($file = readdir($handle))) {
							            if(strpos($file, $year.'-' ) === 0) {
							                echo "<tr><td>Log: <a class='link' href='".URL_ROOT."/src/logs/errors/". $file ."'>".$file."</a></td></tr>"; 
							            }
							        }
							        closedir($handle);  
							    } 
							?>
						</table>
					</div>
				</div>
			</div><!-- /Logging vandaag -->
		</div>
	</div>

	<?php
		// View specific scripts
		array_push($arr_js, '/js/plugins/dataTables/datatables.min.js');
		
	?>	
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>	

   <script>
	function loadUpdates() {
		$('#log').load('log.php', function() {});
	}
	$(document).ready(function() {
		loadUpdates();
		
		setInterval(function() {
			loadUpdates();
		}, 5000);
		
		$.extend(true, $.fn.dataTable.defaults, {
			language: {
				url: <?= json_encode(URL_ROOT);?> + '/js/plugins/dataTables/' + $('html').attr('lang') + '.json'
			},
			iDisplayLength: 5,
			deferRender: true,
			order: [
				[0, "desc"]
			],
			lengthMenu: [5, 10, 20, 25],
			dom: '<"html5buttons"B>lTfgitp',
			buttons: [{
				extend: 'copy'
			}, {
				extend: 'csv'
			}, {
				extend: 'excel',
				title: 'ExampleFile'
			}, {
				extend: 'pdf',
				title: 'ExampleFile'
			}, {
				extend: 'print',
				customize: function(win) {
					$(win.document.body).addClass('white-bg');
					$(win.document.body).css('font-size', '10px');
					$(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
				}
			}]
		});
		$("#datatable-log").DataTable();
		$("#datatable-error").DataTable();
	});
    </script>