	<?php 		array_push($arr_css, '/css/plugins/dataTables/datatables_responsive.min.css');?>
	<div class="wrapper wrapper-content animated fadeInRight">
		<h2 class="m-b-xs"><i class="pe pe-7s-settings text-warning m-r-xs"></i> Settings</h2>
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5><span>Users</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<table class="table table-hover jambo_table bulk_action datatable" id="datatable" style="width:100%">
							<thead>
								<tr>
									<th align='left'>SiteID</th>
									<th align='left'>Site name</th>
									<th align='left'>Site location</th>
									<th align='left'>Site address</th>
									<th align='left'>Site zipcode</th>
									<th align='left'>Site city</th>
									<th align='left'>Zone count</th>
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
						<h5><span>Edit site</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<div class="row">
							<form id="edit_site_name" name="edit_site_name">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>Site name</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="edit_site_name" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>Site location</span><font color="red">*</font></label>
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="edit_site_location" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>Site address</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="edit_site_address" type="text">
									</div>
								</div>
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
								
								<input name="csrf" type="hidden" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>"> 
								<input name="site_id" type="hidden" value="">
								
								<div class="col-md-6">
									<div class="form-group">
										<button class="btn btn-primary" name="save_button"><i class='fa fa-save fa-fw'></i> <span>Update</span></button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5><span>New site</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<div class="row">
							<form id="new_site" name="new_site">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>Site name</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="new_site_name" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>Site location</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="new_site_location" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>Site address</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="new_site_address" type="text">
									</div>
								</div>
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
								
								<input name="csrf" type="hidden" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">
								
								<div class="col-md-6">
									<div class="form-group">
										<button class="btn btn-primary" name="save_button"><i class='fa fa-save fa-fw'></i> <span>Save</span></button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<form class="wizard-big" id="setting_form" name="setting_form">
			<div class="form-group">
				<label class="control-label"><span>Select zones</span> <font color="red">*</font></label> 
				<select class="form-control dual_select" id="zones_select" multiple name="add_zones[]">
				</select>
			</div>
			
			<input name="csrf" type="hidden" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">
			
			<div class="form-group">
				<label class="control-label"><span>Add zones to site</span> <font color="red">*</font></label> 
				<select class="form-control" id="mySelect" name="select_site">
				</select>
			</div>
			<div class="form-group">
				<button class="btn btn-primary" name="save_button"><i class='fa fa-save fa-fw'></i> <span>Update</span></button>
			</div>
		</form>
	</div>
	
	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'/Src/controllers/settings.controller.php';?>" />	
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
    		$('input[name="edit_site_location"]').val(data[2]);
    		$('input[name="edit_site_address"]').val(data[3]);
    		$('input[name="edit_site_zipcode"]').val(data[4]);
    		$('input[name="edit_site_city"]').val(data[5]);
    	});
    	var lang_code = $('html').attr('lang').toLowerCase() + '_' + $('html').attr('lang').toUpperCase();
    	$.extend(true, $.fn.dataTable.defaults, {
    		language: {
    			url: <?= json_encode(URL_ROOT);?> + '/js/plugins/dataTables/' + $('html').attr('lang') + '.json'
    		},
    		iDisplayLength: 3,
    		deferRender: true,
    		order: [
    			[0, "desc"]
    		],
    		lengthMenu: [10, 20, 25],
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
    	getSiteSelect();
    	getZonesSelect();
    	// Add zones to site
    	$('#setting_form').formValidation({
    		framework: 'bootstrap',
    		icon: {
    			valid: 'glyphicon glyphicon-ok',
    			invalid: 'glyphicon glyphicon-remove',
    			validating: 'glyphicon glyphicon-refresh'
    		},
    		locale: lang_code,
    		fields: {
    			select_site: {
    				validators: {
    					notEmpty: {
    						message: 'Het user naam in'
    					}
    				}
    			},
    			add_zones: {
    				validators: {
    					notEmpty: {
    						message: 'Vul user achternaam in'
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
    			url: url_string + "?updatezone",
    			data: $('#setting_form').serialize(),
    			success: function(data) {
    				swal({
    					html: true,
    					title: data.title,
    					text: data.body,
    					type: data.type
    				});
    				table_active.ajax.reload(null, false);
    				getZonesSelect();
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
    			new_site_location: {
    				validators: {
    					notEmpty: {
    						message: 'Vul site locatie in'
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
    			edit_site_location: {
    				validators: {
    					notEmpty: {
    						message: 'Vul site locatie in'
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
    var dual = $('.dual_select').bootstrapDualListbox({
    	selectorMinimalHeight: 160
    });
    var url_str = $('#url_string').val();

    function getSiteSelect() {
    	$.ajax({
    		type: "GET",
    		url: url_str + "?get=sitesselect",
    		success: function(data) {
    			if (data.status != 0) {
    				$('#mySelect').empty();
    				$.each(data.get_sites, function(key, value) {
    					$('#mySelect').append($("<option></option>").attr("value", key).text(value));
    				});
    			}
    		}
    	});
    }

    function getZonesSelect() {
    	$.ajax({
    		type: "GET",
    		url: url_str + "?get=zonesselect",
    		success: function(data) {
    			if (data.status != 0) {
    				dual.empty();
    				$.each(data.get_zones, function(key, value) {
    					dual.append($("<option></option>").attr("value", key).text(value));
    				});
    				dual.bootstrapDualListbox('refresh', true);
    			}
    		}
    	});
    }

	</script>	