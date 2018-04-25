	<?php 		array_push($arr_css, '/css/plugins/dataTables/datatables_responsive.min.css');?>
	<div class="wrapper wrapper-content animated fadeInRight">
		<h2 class="m-b-xs"><i class="pe pe-7s-box2 text-warning m-r-xs"></i> <span data-i18n="[html]groups.title">Groups</span></h2>
		<div class="row">
			<div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5><span data-i18n="[html]groups.table.title">Groups</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<table class="table table-hover jambo_table bulk_action datatable" id="datatable" style="width:100%">
							<thead>
								<tr>
									<th align='left' data-i18n="[html]groups.table.th1">Group ID</th>
									<th align='left' data-i18n="[html]groups.table.th2">Group name</th>
									<th align='left' data-i18n="[html]groups.table.th3">Location count</th>
									<th align='left' data-i18n="[html]groups.table.th4">User count</th>
									<th align='left' data-i18n="[html]groups.table.th5">Action</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
			<div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5><span data-i18n="[html]groups.modify.title">Modify groups</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<div class="row">
							<div class="col-lg-6">
							<form id="edit_site_name" name="edit_site_name">
								
								<div class="col-md-12">
									<h5><span data-i18n="[html]groups.edit.title">Edit group</span> <small></small></h5>
									<div class="form-group">
										<label class="control-label" for="first-name"><span data-i18n="[html]groups.edit.input.1">Group name</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="edit_site_name" type="text">
									</div>
								</div>
								
								<input name="csrf" type="hidden" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>"> 
								<input name="site_id" type="hidden" value="">
								
								<div class="col-md-6">
									<div class="form-group">
										<button class="btn btn-primary" name="save_button"><i class='fa fa-save fa-fw'></i> <span data-i18n="[html]groups.edit.button">Update</span></button>
									</div>
								</div>
							</form>
							</div>
							<div class="col-lg-6">
								<form id="new_site" name="new_site">
									<div class="col-md-12">
										<h5><span data-i18n="[html]groups.new.title">New group</span> <small></small></h5>
										<div class="form-group">
											<label class="control-label" for="first-name"><span data-i18n="[html]groups.new.input.1">Group name</span><font color="red">*</font></label> 
											<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="new_site_name" type="text">
										</div>
									</div>

									<input name="csrf" type="hidden" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">
									<div class="col-md-6">
										<div class="form-group">
											<button class="btn btn-primary" name="save_button"><i class='fa fa-save fa-fw'></i> <span data-i18n="[html]groups.new.button">Save</span></button>
										</div>
									</div>
				
								</form>							
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<form class="wizard-big" id="setting_form" name="setting_form">
						<div class="form-group">
							<label class="control-label"><span data-i18n="[html]groups.add.title">Select users</span> <font color="red">*</font></label> 
							<select class="form-control dual_select" id="zones_select" multiple name="add_zones[]">
							</select>
						</div>
						
						<input name="csrf" type="hidden" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">
						
						<div class="form-group">
							<label class="control-label"><span data-i18n="[html]groups.add.input.1">Add users to group</span> <font color="red">*</font></label> 
							<select class="select2 form-control" id="mySelect" name="select_site">
							</select>
						</div>
						<div class="form-group">
							<button class="btn btn-primary" name="save_button"><i class='fa fa-save fa-fw'></i> <span data-i18n="[html]groups.add.button">Update</span></button>
						</div>
					</form>
				</div>
			</div>
		</div>

	</div>
	
	<input type="text" hidden id="url_string" value="<?= URL_ROOT.'/Src/controllers/group.controller.php';?>" />	
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
        $(".select2").select2({
            placeholder: 'Select...',
            allowClear: true
        });
		
    	var url_string = $('#url_string').val();
		
		getUsersSelect();
    	getGroupsSelect();
		
    	$('.datatable').on('click', '#delete', function() {
    		var id = $(this).attr('value');
    		var user_email = '<b>'+$(this).attr('rel')+'</b>';
    		var csrf_token = $('input[name="csrf"]').attr('value');
    		swal({
    			html: true,
    			title: i18n.t('swal.confirm.title'),
    			text: i18n.t('locations.swal.confirm.text', { placeholder: user_email}),
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
    					getSiteSelect();
    				}
    			});
    		});
    	});
		
    	$(".datatable").on('click', '#edit', function() {
    		var data = table_active.row($(this).parents('tr')).data();
    		$('input[name="site_id"]').val(data[0]);
    		$('input[name="edit_site_name"]').val(data[1]);
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
    		lengthMenu: [5, 10, 20, 25],
    		processing: true,
    		serverSide: true,
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
    			url: url_string + "?addusertogroup",
    			data: $('#setting_form').serialize(),
    			success: function(data) {
    				swal({
    					html: true,
    					title: data.title,
    					text: data.body,
    					type: data.type
    				});
    				table_active.ajax.reload(null, false);
    				getUsersSelect();
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
    				getGroupsSelect();
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
    				getGroupsSelect();
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
	
    function getGroupsSelect() {
    	$.ajax({
    		type: "GET",
    		url: url_str + "?get=groupselect",
    		success: function(data) {
    			if (data.status != 0) {
    				$('#mySelect').empty();
					$('#mySelect').append($("<option></option>"));
					$('#mySelect').append($("<option></option>").attr("value", 0).text('Remove from group'));
    				$.each(data.get_sites, function(key, value) {
    					$('#mySelect').append($("<option></option>").attr("value", key).text(value));
    				});
    			}
    		}
    	});
    }

    function getUsersSelect() {
    	$.ajax({
    		type: "GET",
    		url: url_str + "?get=usersselect",
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