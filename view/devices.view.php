	<?php 		
		//array_push($arr_css, '/css/plugins/dataTables/datatables_responsive.min.css');
		array_push($arr_css, '/css/plugins/select2/dist/css/select2.min.css');
	?>
	<div class="wrapper wrapper-content animated fadeInRight">
		<h2 class="m-b-xs"><i class="pe pe-7s-map-marker text-warning m-r-xs"></i> <span data-i18n="[html]devices.title">Devices</span></h2>
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5><span data-i18n="[html]devices.table.title">Devices</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<table class="table table-hover jambo_table bulk_action datatable" id="datatable" style="width:100%">
							<thead>
								<tr>
									<th align='left' data-i18n="[html]devices.table.th1">Device ID</th>
									<th align='left' data-i18n="[html]devices.table.th2">Device IP</th>
									<th align='left' data-i18n="[html]devices.table.th3">Device MAC</th>
									<th align='left' data-i18n="[html]devices.table.th4">Device Name</th>
									<th align='left' data-i18n="[html]devices.table.th5">Device Location</th>
									<th align='left' data-i18n="[html]devices.table.th6">Device Status</th>
									<th align='left' data-i18n="[html]devices.table.th7">Last Signal</th>
									<th align='left' data-i18n="[html]devices.table.th8">Action</th>
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
						<h5><span data-i18n="[html]devices.new.title">New location</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						
						<form id="new_site" name="new_site">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span data-i18n="[html]devices.new.input.1">Device IP</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="device_ip" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span data-i18n="[html]devices.new.input.2">Device port nr</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="device_ip_port" type="text">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span data-i18n="[html]devices.new.input.3">Device MAC address</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="device_mac" type="text" data-inputmask="'mask': '**:**:**:**:**:**'" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label"><span data-i18n="[html]devices.new.input.4">Add new device to location</span> </label> 
										<select class="select2 form-control mySelect"  name="select_site">
											<option>
										</select>
									</div>								
								</div>
							</div>

							<div class="row">
								<input name="csrf" type="hidden" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">
								<div class="col-md-6">
									<div class="form-group">
										<button class="btn btn-primary" name="save_button"><i class='fa fa-save fa-fw'></i> <span data-i18n="[html]devices.new.button">Save</span></button>
									</div>
								</div>
							</div>
						</form>

					</div>
				</div>
			</div>		
			<div class="col-md-6">
				<form class="wizard-big" id="setting_form" name="setting_form">
					<div class="form-group">
						<label class="control-label"><span data-i18n="[html]devices.add.title">Select device(s)</span> </label> 
						<select class="form-control dual_select" id="zones_select" multiple name="add_zones[]">
						</select>
					</div>
					
					<input name="csrf" type="hidden" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">
					
					<div class="form-group">
						<label class="control-label"><span data-i18n="[html]devices.add.input.1">Add device to location</span> <font color="red">*</font></label> 
						<select class="select2 form-control mySelect" name="select_site">
						</select>
					</div>
					<div class="form-group">
						<button class="btn btn-primary" name="save_button"><i class='fa fa-save fa-fw'></i> <span data-i18n="[html]devices.add.button">Update</span></button>
					</div>
				</form>
			</div>
		
		</div>


	</div>
	
	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'/Src/controllers/devices.controller.php';?>" />	
	<?php
		// View specific scripts
		array_push($arr_js, '/js/plugins/dataTables/datatables.min.js');
		array_push($arr_js, '/js/plugins/dataTables/datatables_responsive.min.js');
		array_push($arr_js, '/js/plugins/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js');

	?>		
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>	

	<script>
    $(document).ready(function() {
        $(".select2").select2({
            placeholder: 'Select...',
            allowClear: true
        });		
		
		$(":input").inputmask();

		$('input[name=device_mac]').keyup(function(){
			this.value = this.value.toUpperCase();
		});		
		
		getDevicesSelect();		
		getLocationSelect();	
		
    	var url_string = $('#url_string').val();
		
    	$('.datatable').on('click', '#delete', function() {
    		var id = $(this).attr('value');
    		var user_email = '<b>'+$(this).attr('rel')+'</b>';
    		var csrf_token = $('input[name="csrf"]').attr('value');
    		swal({
    			html: true,
    			title: i18n.t('swal.confirm.title'),
    			text: i18n.t('devices.swal.confirm.text', { placeholder: user_email}),
    			type: "warning",
    			showCancelButton: true,
				cancelButtonText: i18n.t('swal.confirm.cancelbutton'),
    			confirmButtonColor: "#DD6B55",
    			confirmButtonText: i18n.t('swal.confirm.confirmbutton'),
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
    					getDevicesSelect();
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
    	
		// Format ex: nl_NL
		var lang_code = $('html').attr('lang').toLowerCase() + '_' + $('html').attr('lang').toUpperCase();

		$.extend(true, $.fn.dataTable.defaults, {
    		language: {
    			url: <?= json_encode(URL_ROOT);?> + '/js/plugins/dataTables/' + $('html').attr('lang') + '.json'
    		},
    		iDisplayLength: 5,
    		deferRender: true,
    		order: [
    			[6, "desc"]
    		],
    		lengthMenu: [5,10, 20, 25],
			responsive: {
				details: {
					renderer: function ( api, rowIdx, columns ) {
						var data = $.map( columns, function ( col, i ) {
							return col.hidden ?
								'<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
									'<td>'+col.title+':'+'</td> '+
									'<td>'+col.data+'</td>'+
								'</tr>' :
								'';
						} ).join('');
	
						return data ?
							$('<table/ width="100%" class="sub_responsive">').append( data ) :
							false;
					}
				}
			}
    	});
    	
		var interval;
    	var table_active = $(".datatable").DataTable({
    		//ajax: url_string + "?get=sitestable",
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
    					}
    				}
    			},
    			add_zones: {
    				validators: {
    					notEmpty: {
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
    			url: url_string + "?adddevicetolocation",
    			data: $('#setting_form').serialize(),
    			success: function(data) {
    				swal({
    					html: true,
    					title: data.title,
    					text: data.body,
    					type: data.type
    				});
    				table_active.ajax.reload(null, false);
    				getDevicesSelect();
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
    			device_ip: {
    				validators: {
    					notEmpty: {
    					},
    					ip: {
    					}						
    				}
    			},
    			device_ip_port: {
    				validators: {
    					notEmpty: {
    					},
    					digits: {
    					}
    				}
    			},				
    			device_mac: {
    				validators: {
    					notEmpty: {
    					},
    					mac: {
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
    				getDevicesSelect();
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
    					}
    				}
    			},

    			edit_site_address: {
    				validators: {
    					notEmpty: {
    					}
    				}
    			},
    			edit_site_zipcode: {
    				validators: {
    					notEmpty: {
    					}
    				}
    			},
    			edit_site_city: {
    				validators: {
    					notEmpty: {
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

    var dual = $('.dual_select').bootstrapDualListbox({
    	selectorMinimalHeight: 160
    });	
	
	var url_str = $('#url_string').val();

    function getLocationSelect() {
    	$.ajax({
    		type: "GET",
    		url: url_str + "?get=locationselect",
    		success: function(data) {
    			if (data.status != 0) {
    				$('.mySelect').empty();
					$('.mySelect').append($("<option></option>"));
					$('.mySelect').append($("<option></option>").attr("value", 0).text('Remove from location'));
    				$.each(data.get_sites, function(key, value) {
    					$('.mySelect').append($("<option></option>").attr("value", key).text(value));
    				});
    			}
    		}
    	});
    }

    function getDevicesSelect() {
    	$.ajax({
    		type: "GET",
    		url: url_str + "?get=devicesselect",
    		success: function(data) {
    			if (data.status != 0) {
    				dual.empty();
    				$.each(data.get_zones, function(key, value) {
						if(value['in_site'] == 0){
							var add_class = 'text-navy';
						}
    					dual.append($('<option class="'+add_class+'"></option>').attr("value", key).text(value['text']));
    				});
    				dual.bootstrapDualListbox('refresh', true);
    			}
    		}
    	});
    }		
	
	</script>	