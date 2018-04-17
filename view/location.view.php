	<?php 		array_push($arr_css, '/css/plugins/dataTables/datatables_responsive.min.css');?>
	<div class="wrapper wrapper-content animated fadeInRight">
		<h2 class="m-b-xs"><i class="pe pe-7s-map-marker text-warning m-r-xs"></i> Locations</h2>
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5><span>Locations</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<table class="table table-hover jambo_table bulk_action datatable" id="datatable" style="width:100%">
							<thead>
								<tr>
									<th align='left'>SiteID</th>
									<th align='left'>Site name</th>
									<th align='left'>Site address</th>
									<th align='left'>Site zipcode</th>
									<th align='left'>Site city</th>
									<th align='left'>Group</th>
									<th align='left'></th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5><span>Edit location</span> <small></small></h5>
					</div>
					<div class="ibox-content">

							<form id="edit_site_name" name="edit_site_name">
								<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>Site name</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="edit_site_name" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>Site address</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="edit_site_address" type="text">
									</div>
								</div>
								</div>
								<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>Site zipcode</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="edit_site_zipcode" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>Site city</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="edit_site_city" type="text">
									</div>
								</div>
								</div>
								<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>Select group</span></label> 
										<select class="form-control" name="edit_site_group">
											<option value="">Select...</option>
											<?php
												$group = new Group($db_conn);
												foreach($group->getSelectGroup() as $key => $val){
													echo '<option value="'.$key.'">'.$val.'</option>';
												}
											?>
										</select>
									</div>
								</div>								
								<div class="col-md-6">
									<div class="form-group">
										<div class="input-group">
											<input type="text" class="form-control" placeholder="Latitude" id="edit_lat" name="edit_site_latitude" aria-describedby="basic-addon2">
											<input type="text" class="form-control" placeholder="Longitude"  id="edit_lon" name="edit_site_longitude" aria-describedby="basic-addon2">
											<span class="btn btn-accent input-group-addon " id="basic-addon2" onClick="getEditCoord();"><i class="fa fa-map-marker"></i></span>
										</div>
									</div>
								</div>
								</div>
								<div class="row">
								<input name="csrf" type="hidden" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>"> 
								<input name="site_id" type="hidden" value="">
								
								<div class="col-md-6">
									<div class="form-group">
										<button class="btn btn-primary" name="save_button"><i class='fa fa-save fa-fw'></i> <span>Update</span></button>
									</div>
								</div>
								</div>
							</form>

					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5><span>New location</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						
							<form id="new_site" name="new_site">
								<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>Site name</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="new_site_name" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>Site address</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="new_site_address" type="text">
									</div>
								</div>
								</div>
								<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>Site zipcode</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="new_site_zipcode" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>Site city</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="new_site_city" type="text">
									</div>
								</div>
								</div>
								<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>Select group</span><font color="red">*</font></label> 
										<select class="form-control" name="new_site_group">
											<option value="">Select...</option>
											<?php
												$group = new Group($db_conn);
												foreach($group->getSelectGroup() as $key => $val){
													echo '<option value="'.$key.'">'.$val.'</option>';
												}
											?>
										</select>
									</div>
								</div>								
								<div class="col-md-6">
									<div class="form-group">
										<div class="input-group">
											<input type="text" class="form-control" placeholder="Latitude" id="lat" name="new_site_latitude" aria-describedby="basic-addon2">
											<input type="text" class="form-control" placeholder="Longitude"  id="lon" name="new_site_longitude" aria-describedby="basic-addon2">
											<span class="btn btn-accent input-group-addon " id="basic-addon2" onClick="getCoord();"><i class="fa fa-map-marker"></i></span>
										</div>
									</div>
								</div>
								</div>
								<div class="row">
								<input name="csrf" type="hidden" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">
								<div class="col-md-6">
									<div class="form-group">
										<button class="btn btn-primary" name="save_button"><i class='fa fa-save fa-fw'></i> <span>Save</span></button>
									</div>
								</div>
								</div>
							</form>

					</div>
				</div>
			</div>
		</div>

	</div>
	
	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'/Src/controllers/location.controller.php';?>" />	
	<?php
		// View specific scripts
		array_push($arr_js, '/js/plugins/dataTables/datatables.min.js');
		array_push($arr_js, '/js/plugins/dataTables/datatables_responsive.min.js');
		
	?>		
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>	

	<script>
    $(document).ready(function() {
    	var url_string = $('#url_string').val();
		
    	$('.datatable').on('click', '#delete', function() {
    		var id = $(this).attr('value');
    		var user_email = $(this).attr('rel');
    		var csrf_token = $('input[name="csrf"]').attr('value');
    		swal({
    			html: true,
    			title: "Weet je het zeker?",
    			text: "De site: <b>" + user_email + "</b> wordt permanent verwijderd!",
    			type: "warning",
    			showCancelButton: true,
    			confirmButtonColor: "#DD6B55",
    			confirmButtonText: "Yes, verwijder",
    			closeOnConfirm: false
    		}, function() {
    			$.ajax({
    				type: "post",
    				url: url_string + "?delete",
    				data: {
    					site_id: id,
    					csrf: csrf_token
    				},
    				success: function(data) {
    					swal({
    						html: true,
    						title: data.title,
    						text: data.body,
    						type: data.type
    					});
    					table_active.ajax.reload(null, false);
    					getSiteSelect();
    				}
    			});
    		});
    	});
		
    	$(".datatable").on('click', '#edit', function() {
    		var data = table_active.row($(this).parents('tr')).data();
    		$('input[name="site_id"]').val(data[0]);
    		$('input[name="edit_site_name"]').val(data[1]);
    		$('input[name="edit_site_address"]').val(data[2]);
    		$('input[name="edit_site_zipcode"]').val(data[3]);
    		$('input[name="edit_site_city"]').val(data[4]);
    	});
    	
		var lang_code = $('html').attr('lang').toLowerCase() + '_' + $('html').attr('lang').toUpperCase();
    	
		$.extend(true, $.fn.dataTable.defaults, {
    		language: {
    			url: <?= json_encode(URL_ROOT);?> + '/js/plugins/dataTables/' + $('html').attr('lang') + '.json'
    		},
    		iDisplayLength: 5,
    		deferRender: true,
    		order: [
    			[0, "desc"]
    		],
    		lengthMenu: [5,10, 20, 25],
    		processing: true,
    		serverSide: true,
    		responsive: true
    	});
    	
		var interval;
    	var table_active = $(".datatable").DataTable({
    		ajax: url_string + "?get=sitestable",
    		fnInitComplete: function(oSettings, json) {
    			$.i18n.init({
    				resGetPath: <?= json_encode(URL_ROOT);?> + '/src/lang/__lng__.json',
    				load: 'unspecific',
    				fallbackLng: false,
    				lng: $('html').attr('lang')
    			}, function(t) {
    				$('#i18container').i18n();
    			});
    		}
    	});
    	
		// Add new site
    	$('#new_site').formValidation({
    		framework: 'bootstrap',
    		icon: {
    			valid: 'glyphicon glyphicon-ok',
    			invalid: 'glyphicon glyphicon-remove',
    			validating: 'glyphicon glyphicon-refresh'
    		},
    		locale: lang_code,
    		fields: {
    			new_site_name: {
    				validators: {
    					notEmpty: {
    						message: 'Vul site naam in'
    					}
    				}
    			},
    			new_site_group: {
    				validators: {
    					notEmpty: {
    						message: 'Selecteer een groep'
    					}
    				}
    			},				
    			new_site_address: {
    				validators: {
    					notEmpty: {
    						message: 'Vul het site adres in'
    					}
    				}
    			},
    			new_site_zipcode: {
    				validators: {
    					notEmpty: {
    						message: 'Vul de site postcode in'
    					}
    				}
    			},
    			new_site_city: {
    				validators: {
    					notEmpty: {
    						message: 'Vul de site stad in'
    					}
    				}
    			}
    		}
    	}).on('success.field.fv', function(e, data) {
    		if (data.fv.getInvalidFields().length > 0) {
    			data.fv.disableSubmitButtons(true);
    		}
    	}).on('success.form.fv', function(e) {
    		// Voorkom form submission 
    		e.preventDefault();
    		var $form = $(e.target),
    			fv = $form.data('formValidation');
    		$.ajax({
    			type: "POST",
    			url: url_string + "?new",
    			data: $('#new_site').serialize(),
    			success: function(data) {
    				swal({
    					html: true,
    					title: data.title,
    					text: data.body,
    					type: data.type
    				});
    				table_active.ajax.reload(null, false);
    				getSiteSelect();
					$('#new_site').find("input[type=text], textarea").val("");
					fv.resetForm();
    			},
    			error: function(xhr, status, error) {
    				var json = $.parseJSON(xhr.responseText);
    				console.log(json);
    				swal({
    					html: true,
    					title: json.title,
    					text: json.msg,
    					type: "error"
    				});
    			}
    		});
    	});
    	// Update site name
    	$('#edit_site_name').formValidation({
    		framework: 'bootstrap',
    		icon: {
    			valid: 'glyphicon glyphicon-ok',
    			invalid: 'glyphicon glyphicon-remove',
    			validating: 'glyphicon glyphicon-refresh'
    		},
    		locale: lang_code,
    		fields: {
    			edit_site_name: {
    				validators: {
    					notEmpty: {
    						message: 'Vul site naam in'
    					}
    				}
    			},

    			edit_site_address: {
    				validators: {
    					notEmpty: {
    						message: 'Vul het site adres in'
    					}
    				}
    			},
    			edit_site_zipcode: {
    				validators: {
    					notEmpty: {
    						message: 'Vul de site postcode in'
    					}
    				}
    			},
    			edit_site_city: {
    				validators: {
    					notEmpty: {
    						message: 'Vul de site stad in'
    					}
    				}
    			}
    		}
    	}).on('success.field.fv', function(e, data) {
    		if (data.fv.getInvalidFields().length > 0) {
    			data.fv.disableSubmitButtons(true);
    		}
    	}).on('success.form.fv', function(e) {
    		// Voorkom form submission 
    		e.preventDefault();
    		var $form = $(e.target),
    			fv = $form.data('formValidation');
    		$.ajax({
    			type: "POST",
    			url: url_string + "?update",
    			data: $('#edit_site_name').serialize(),
    			success: function(data) {
    				swal({
    					html: true,
    					title: data.title,
    					text: data.body,
    					type: data.type
    				});
    				table_active.ajax.reload(null, false);
    				getSiteSelect();
					$('#edit_site_name').find("input[type=text], textarea").val("");
					fv.resetForm();
    			},
    			error: function(xhr, status, error) {
    				var json = $.parseJSON(xhr.responseText);
    				console.log(json);
    				swal({
    					html: true,
    					title: json.title,
    					text: json.msg,
    					type: "error"
    				});
    			}
    		});
    	});
    });
	
	var url_str = $('#url_string').val();
	var ajaxObj = {
		options: {
			url: null,
			dataType: 'json'
		},
		delay: function(refresh_time) {
			return refresh_time;
		},
		errorCount: 0,
		errorThreshold: 5,
		ticker: null,
		updatetime: null,
		get: function(function_name, refresh_time) {
			if (ajaxObj.errorCount < ajaxObj.errorThreshold) { // Gets triggered for all objects!?
				ajaxObj.ticker = setTimeout(function_name, ajaxObj.delay(refresh_time));
				swal.close();
			}
		},
		fail: function(jqXHR, textStatus, errorThrown) {
			console.log(errorThrown);
			
			swal({
				html: true,
				title: textStatus,
				text: errorThrown,
				type: "error"
			});
			ajaxObj.errorCount++;
		}
	};	

	function getEditCoord() {
		var _name = $('input[name=edit_site_name]').val();
		var _addr = $('input[name=edit_site_address]').val();
		var _zipc = $('input[name=edit_site_zipcode]').val();
		var _city = $('input[name=edit_site_city]').val();
		
		$.ajax({
			type: 'POST',
			url: url_str+"?get=coordinates",
			data: {name: _name, addr: _addr, zipc: _zipc, city: _city},
			success: function(data) {
				$('#edit_lat').val(data.lat);
				$('#edit_lon').val(data.lon);
			}
		});
	}	
	
	function getCoord() {
		var _name = $('input[name=new_site_name]').val();
		var _addr = $('input[name=new_site_address]').val();
		var _zipc = $('input[name=new_site_zipcode]').val();
		var _city = $('input[name=new_site_city]').val();
		
		$.ajax({
			type: 'POST',
			url: url_str+"?get=coordinates",
			data: {name: _name, addr: _addr, zipc: _zipc, city: _city},
			success: function(data) {
				$('#lat').val(data.lat);
				$('#lon').val(data.lon);
			}
		});
	}
	
	</script>	