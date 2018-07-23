    <div class="wrapper animated fadeInRight">
        <div class="row">
			<div class="col-lg-12">
				<h2>Totaal statistiek <small><?= date('d-m-Y');?></small></h2>
			</div>
			<div class="col-lg-2">
                <div class="widget style1 dark-gray-bg">
                    <div class="row vertical-align">
                        <div class="col-xs-5" >
                            <h2 class="c-gray"><i class="fa fa-phone"></i> <small class="text-navy"><i class="fa fa-arrow-left"></i></small></h2>
                        </div>
                        <div class="col-xs-7 text-right">
                            <h2 class="font-bold" id="total_in"></h2>
                        </div>
						<div class="col-xs-12">
							<span>Totaal inkomende calls</span>
						</div>
                    </div>
                </div>
            </div>
			<div class="col-lg-2">
                <div class="widget style1 dark-gray-bg">
                    <div class="row vertical-align">
                        <div class="col-xs-5" >
                            <h2 class="c-gray"><i class="fa fa-phone"></i> <small class="text-navy"><i class="fa fa-arrow-right"></i></small></h2>
                        </div>
                        <div class="col-xs-7 text-right">
                            <h2 class="font-bold" id="total_out"></h2>
                        </div>
						<div class="col-xs-12">
							<span>Totaal uitgaande calls</span>
						</div>
                    </div>
                </div>
            </div>			
			<div class="col-lg-2">
                <div class="widget style1 dark-gray-bg">
                    <div class="row vertical-align">
                        <div class="col-xs-5" >
                            <h2 class="c-gray"><i class="fa fa-phone"></i> <small class="text-danger"><i class="fa fa-remove"></i></small></h2>
							
                        </div>
                        <div class="col-xs-7 text-right">
                            <h2 class="font-bold" id="missed"></h2>
                        </div>
						<div class="col-xs-12">
							<span>Totaal gemiste calls</span>
						</div>
                    </div>
                </div>
            </div>	
			<div class="col-lg-2">
                <div class="widget style1 dark-gray-bg">
                    <div class="row vertical-align">
                        <div class="col-xs-5" >
                            <h2 class="c-gray">SLA <small class="text-navy"><i class="fa fa-pie-chart"></i></small></h2>
                        </div>
                        <div class="col-xs-7 text-right">
                            <h2 class="font-bold"><span id="total_in_sla"></span><small>%</small></h2>
                        </div>
						<div class="col-xs-12">
							<span>Percentage inkomende calls binnen SLA </span>
						</div>
                    </div>
                </div>
            </div>
			<div class="col-lg-2">
                <div class="widget style1 dark-gray-bg">
                    <div class="row vertical-align">
                        <div class="col-xs-5" >
                            <h2 class="c-gray">SLA <small class="text-danger"><i class="fa fa-pie-chart"></i></small></h2>
                        </div>
                        <div class="col-xs-7 text-right">
                            <h2 class="font-bold"><span id="total_out_sla"></span><small>%</small></h2>
                        </div>
						<div class="col-xs-12">
							<span>Percentage inkomende calls buiten SLA </span>
						</div>
                    </div>
                </div>
            </div>			
        </div>		
        <div class="row">
			<div class="col-lg-12">
				<h2>Telefoon queues <small> </small></h2>
			</div>		
        </div>		
		
		<div id="queue"></div>
		
		<div class="row">
			<div class="col-lg-12">
				<h2>Queue agents <small></small></h2>
				<div class="widget style1 dark-gray-bg">
					<table class="table" style="color: white;" id="agents_table">
						<thead>
							<tr>
								<th>#</th>
								<th>Agent</th>
								<th>Status</th>
								<th><span class="text-navy"><i class="fa fa-phone"></i> <i class="fa fa-arrow-left"></i></span></th>
								<th><span class="text-navy"><i class="fa fa-phone"></i> <i class="fa fa-arrow-right"></i></span></th>
								<th><span class="text-danger"><i class="fa fa-phone"></i> <i class="fa fa-remove"></span></th>
								<th>Gem wachttijd</th>
								<th>Gem spreektijd</th>
							</tr>
						</thead>
						<tbody id="q_agents">
						</tbody>
					</table>
				</div>
			</div>
		</div>
    </div>
						
	<input type="text" hidden id="url_query" value="<?= $_SERVER['QUERY_STRING']; ?>" />	
	<input type="text" hidden id="mac_adres" />	
	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'/Src/controllers/tools.controller.php';?>" />	
	
	
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>	

	<script>
	$(document).ready(function () {	
		var refresh = 1500;
		var interval;
		
		getQueues();
		getQueueAgents();
		highLightTableValues();
		
		pollPending();
				
	});	

	function pollPending(){
		setTimeout(function(){
			getQueues(),
			getQueueAgents(),
			highLightTableValues()
			pollPending();
		}, 1500);
	}	
	
	function highLightTableValues(){
		var cols = []
		var trs = $('#agents_table tr')
		var data =$.each(trs , function(index, tr){
			$.each($(tr).find("td").not(":first"), function(index, td){
			cols[index] = cols[index] || [];
			cols[index].push($(td).text())
			})
		});
		cols.forEach(function(col, index){
			var max = Math.max.apply(null, col);
			var min = Math.min.apply(null, col)
			$('#agents_table tr').find('td:eq('+(index+1)+')').each(function(i, td){
			$(td).toggleClass('text-navy', +$(td).text() === max)
			$(td).toggleClass('text-danger', +$(td).text() === min)  
			})
		})
	};
	
	function getQueues(){
		$.ajaxq('telqueue', {
			type: 'GET',
			url: $('#url_string').val() + "?get=queue&row",
			success: function(data) {
				if(data.status != 0){
					$('#queue').html(data.row);		
					$('#total_in').html(data.total_in);
					$('#total_out').html(data.total_out);
					$('#missed').html(data.total_missed);
					$('#total_in_sla').html(data.total_in_sla);
					$('#total_out_sla').html(data.total_out_sla);
				} else {
					$('#queue').html('');
					$('#total_in').html('');
					$('#total_out').html('');
					$('#missed').html('');
					$('#total_in_sla').html('');
					$('#total_out_sla').html('');
				}						
			}
		});		
	}
	function getQueueAgents(){
		$.ajaxq('telqueue',{
			type: 'GET',
			url: $('#url_string').val() + "?get=queue&agents",
			success: function(data) {
				if(data.status != 0){
					$('#q_agents').html(data.rows);	
				} else {
					$('#q_agents').html('');
				}						
			}
		});		
	}	

	</script>