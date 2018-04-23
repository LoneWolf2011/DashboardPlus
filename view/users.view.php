<?php
		$wb_conn = new SafeMySQL();
		if(isset($_GET['id'])){
			$search = preg_replace("/[^0-9]/","", $_GET['id']);
			$row = $wb_conn->getRow("SELECT * FROM app_users WHERE user_id = ?s",$search);			
		}

	?>
	<div class="wrapper wrapper-content animated fadeInRight">
		<h2 class="m-b-xs"><i class="pe pe-7s-users text-warning m-r-xs"></i> Users</h2>
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
									<th align='left'>Username</th>
									<th align='left'>Email</th>
									<th align='left'>Last access</th>
									<th align='left'>Userrole</th>
									<th align='left'>Status</th>
									<th align='left'>Action</th>
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
						<h5><span>Edit user</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<div class="row">
							<form id="update_user" name="update_user">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>User name</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="user_name" type="text" value="<?= @$row['user_name'];?>">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>User last name</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="user_last_name" type="text" value="<?= @$row['user_last_name'];?>">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>User email</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="user_email" type="text" value="<?= @$row['user_email']; ?>">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label"><span>User role</span><font color="red">*</font></label> 
										<select class="form-control" name="user_role">
											<option value="<?= @$row['user_role'];?>">
												<?= @$row['user_role'];?>
											</option>
											<option data-i18n="[html]tickets.create.dropdown" value="">
												Selecteer...
											</option>
											<option value="2">
												User (2)
											</option>
											<option value="1">
												Admin (1)
											</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label"><span>User status</span><font color="red">*</font></label> 
										<select class="form-control" name="user_status">
											<option value="<?= @$row['user_status'];?>">
												<?= @$row['user_status'];?>
											</option>
											<option data-i18n="[html]tickets.create.dropdown" value="">
												Selecteer...
											</option>
											<option value="Active">
												Active
											</option>
											<option value="Blocked">
												Blocked
											</option>
										</select>
									</div>
								</div>
								
								<input class="form-control hidden" name="user_id" type="text" value="<?= @$row['user_id'];?>"> 
								<input name="csrf" type="hidden" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">
								
								<div class="col-md-12">
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
						<h5><span>New user</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<div class="row">
							<form id="NewWerkbonForm" name="new_wb">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>User name</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="user_name" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>User last name</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="user_last_name" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>User email</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]tickets.create.placeholder" name="user_email" type="text" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label"><span>User role</span><font color="red">*</font></label> 
										<select class="form-control" name="user_role">
											<option data-i18n="[html]tickets.create.dropdown" value="">
												Selecteer...
											</option>
											<option value="2">
												User
											</option>
											<option value="1">
												Admin
											</option>
										</select>
									</div>
								</div><input name="csrf" type="hidden" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">
								<div class="col-md-12">
									<div class="form-group">
										<button class="btn btn-primary" name="save_button"><i class='fa fa-save fa-fw'></i> <span>Create</span></button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	
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
    	$('#datatable').on('click', '#delete', function() {
    		var id = $(this).attr('value');
    		var user_email = $(this).attr('rel');
    		var csrf_token = $('input[name="csrf"]').attr('value');
    		swal({
    			html: true,
    			title: "Weet je het zeker?",
    			text: "De gebruiker: <b>" + user_email + "</b> wordt permanent verwijderd!",
    			type: "warning",
    			showCancelButton: true,
    			confirmButtonColor: "#DD6B55",
    			confirmButtonText: "Yes, verwijder user",
    			closeOnConfirm: false
    		}, function() {
    			$.ajax({
    				type: "post",
    				url: <?= json_encode(URL_ROOT);?> + "/Src/controllers/user.controller.php?delete",
    				data: {
    					user_id: id,
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
    				}
    			});
    		});
    	});
    	var lang_code = $('html').attr('lang');
    	$.extend(true, $.fn.dataTable.defaults, {
    		language: {
    			url: <?= json_encode(URL_ROOT);?> + '/js/plugins/dataTables/' + $('html').attr('lang') + '.json'
    		},
    		iDisplayLength: 10,
    		deferRender: true,
    		order: [
    			[3, "desc"]
    		],
    		lengthMenu: [10, 20, 25],
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
    		ajax: <?= json_encode(URL_ROOT);?> + "/Src/controllers/user.controller.php?get=users",
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
    	$('#update_user').formValidation({
    		framework: 'bootstrap',
    		icon: {
    			valid: 'glyphicon glyphicon-ok',
    			invalid: 'glyphicon glyphicon-remove',
    			validating: 'glyphicon glyphicon-refresh'
    		},
    		locale: lang_code,
    		fields: {
    			user_name: {
    				validators: {
    					notEmpty: {
    					}
    				}
    			},
    			user_last_name: {
    				validators: {
    					notEmpty: {
    					}
    				}
    			},
    			user_email: {
    				validators: {
    					notEmpty: {
    					},
    					emailAddress: {
    					}
    				}
    			},
    			user_role: {
    				validators: {
    					notEmpty: {
    					}
    				}
    			},
    			user_status: {
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
    			url: <?= json_encode(URL_ROOT);?> + "/Src/controllers/user.controller.php?updateuser",
    			data: $('form[name="update_user"]').serialize(),
    			success: function(data) {
    				swal({
    					html: true,
    					title: data.title,
    					text: data.body,
    					type: data.type
    				});
    				table_active.ajax.reload(null, false);
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
    	$('#NewWerkbonForm').formValidation({
    		framework: 'bootstrap',
    		icon: {
    			valid: 'glyphicon glyphicon-ok',
    			invalid: 'glyphicon glyphicon-remove',
    			validating: 'glyphicon glyphicon-refresh'
    		},
    		locale: lang_code,
    		fields: {
    			user_name: {
    				validators: {
    					notEmpty: {
    					}
    				}
    			},
    			user_last_name: {
    				validators: {
    					notEmpty: {
    					}
    				}
    			},
    			user_email: {
    				validators: {
    					notEmpty: {
    					},
    					emailAddress: {
    					}
    				}
    			},
    			user_role: {
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
    			url: <?= json_encode(URL_ROOT);?> + "/Src/controllers/user.controller.php?new",
    			data: $('form[name="new_wb"]').serialize(),
    			success: function(data) {
    				swal({
    					html: true,
    					title: data.title,
    					text: data.body,
    					type: data.type
    				});
    				table_active.ajax.reload(null, false);
    			},
    			error: function(xhr, status, error) {
    				var json = $.parseJSON(xhr.responseText);
    				//console.log(json);
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
	</script>	