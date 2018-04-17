	<?php 		array_push($arr_css, '/css/plugins/dataTables/datatables_responsive.min.css');?>
	<div class="wrapper wrapper-content animated fadeInRight">
		<h2 class="m-b-xs"><i class="pe pe-7s-map-marker text-warning m-r-xs"></i> Devices</h2>
		<div class="row">
			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5><span>Devices</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<table class="table table-hover jambo_table bulk_action datatable" id="datatable" style="width:100%">
							<thead>
								<tr>
									<th align='left'>Device ID</th>
									<th align='left'>Device IP</th>
									<th align='left'>Device MAC</th>
									<th align='left'>Device Name</th>
									<th align='left'>Device Location</th>
									<th align='left'>Device Status</th>
									<th align='left'>Last Signal</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<form class="wizard-big" id="setting_form" name="setting_form">
					<div class="form-group">
						<label class="control-label"><span>Select device(s)</span> <font color="red">*</font></label> 
						<select class="form-control dual_select" id="zones_select" multiple name="add_zones[]">
						</select>
					</div>
					
					<input name="csrf" type="hidden" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">
					
					<div class="form-group">
						<label class="control-label"><span>Add device to location</span> <font color="red">*</font></label> 
						<select class="form-control" id="mySelect" name="select_site">
						</select>
					</div>
					<div class="form-group">
						<button class="btn btn-primary" name="save_button"><i class='fa fa-save fa-fw'></i> <span>Update</span></button>
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
		
	?>		
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>	

	<script>
    $(document).ready(function() {
		
		getDevicesSelect();		
		getLocationSelect();	
		
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
    		responsive: true
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
    				$('#mySelect').empty();
					$('#mySelect').append($("<option></option>").attr("value", 0).text('Remove from group'))
    				$.each(data.get_sites, function(key, value) {
    					$('#mySelect').append($("<option></option>").attr("value", key).text(value));
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