<?php
		$wb_conn = new SafeMySQL();
		if(isset($_GET['id'])){
			$search = preg_replace("/[^0-9]/","", $_GET['id']);
			$row = $wb_conn->getRow("SELECT * FROM app_users WHERE user_id = ?s",$search);			
		}

	?>
	<div class="wrapper wrapper-content animated fadeInRight">

		<div class="row">
			<div class="col-lg-12">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5><span data-i18n="[html]users.title">Users</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<table class="table table-hover jambo_table bulk_action datatable" id="datatable" style="width:100%">
							<thead>
								<tr>
									<th align='left' data-i18n="[html]users.table.th1">Username</th>
									<th align='left' data-i18n="[html]users.table.th2">Email</th>
									<th align='left' data-i18n="[html]users.table.th3">Last access</th>
									<th align='left' data-i18n="[html]users.table.th4">Userrole</th>
									<th align='left' data-i18n="[html]users.table.th5">Status</th>
									<th align='left' data-i18n="[html]users.table.th6">Action</th>
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
						<h5><span data-i18n="[html]users.edit.title">Edit user</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<div class="row">
							<form id="update_user" name="update_user">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span data-i18n="[html]users.edit.input.1">User name</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="user_name" type="text" value="<?= @$row['user_name'];?>">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span data-i18n="[html]users.edit.input.2">User last name</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="user_last_name" type="text" value="<?= @$row['user_last_name'];?>">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span data-i18n="[html]users.edit.input.3">User email</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="user_email" type="text" value="<?= @$row['user_email']; ?>">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label"><span data-i18n="[html]users.edit.input.4">User role</span><font color="red">*</font></label> 
										<select class="select2 form-control" name="user_role">
												<option value="<?= @$row['user_role'];?>"><?= $db_conn->getOne("SELECT role_name FROM app_role WHERE id = ?s",@$row['user_role']);?></option>
												<option value="" data-i18n="[html]tickets.create.dropdown">Selecteer...</option>
												<?php
													$roles = $db_conn->getAll("SELECT id, role_name FROM app_role");
													foreach($roles as $role){
														echo '<option value="'.$role['id'].'">'.$role['role_name'].'</option>';
													}
												?>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label"><span data-i18n="[html]users.edit.input.5">User status</span><font color="red">*</font></label> 
										<select class="select2 form-control" name="user_status">
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
										<button class="btn btn-primary" name="save_button"><i class='fa fa-save fa-fw'></i> <span data-i18n="[html]users.edit.button">Update</span></button>
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
						<h5><span data-i18n="[html]users.new.title">New user</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<div class="row">
							<form id="NewWerkbonForm" name="new_wb">
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span data-i18n="[html]users.edit.input.1">User name</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="user_name" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span data-i18n="[html]users.edit.input.2">User last name</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="user_last_name" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span data-i18n="[html]users.edit.input.3">User email</span><font color="red">*</font></label> 
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="user_email" type="text" >
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label"><span data-i18n="[html]users.edit.input.4">User role</span><font color="red">*</font></label> 
										<select class="select2 form-control" name="user_role">
											<option data-i18n="[html]tickets.create.dropdown" value="">
												Selecteer...
											</option>
												<?php
													$roles = $db_conn->getAll("SELECT id, role_name FROM app_role");
													foreach($roles as $role){
														echo '<option value="'.$role['id'].'">'.$role['role_name'].'</option>';
													}
												?>
										</select>
									</div>
								</div>
                                <input name="csrf" type="hidden" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">
								<div class="col-md-12">
									<div class="form-group">
										<button class="btn btn-primary" name="save_button"><i class='fa fa-save fa-fw'></i> <span data-i18n="[html]users.new.button">Create</span></button>
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
    		var user_email = '<b>'+$(this).attr('rel')+'</b>';
    		var csrf_token = $('input[name="csrf"]').attr('value');
    		swal({
    			html: true,
    			title: i18n.t('swal.confirm.title'),
    			text: i18n.t('users.swal.confirm.text', { placeholder: user_email}),
    			type: "warning",
    			showCancelButton: true,
				cancelButtonText: i18n.t('swal.confirm.cancelbutton'),
    			confirmButtonColor: "#DD6B55",
    			confirmButtonText: i18n.t('swal.confirm.confirmbutton'),
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
    		responsive: true
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
    						message: 'Het user naam in'
    					}
    				}
    			},
    			user_last_name: {
    				validators: {
    					notEmpty: {
    						message: 'Vul user achternaam in'
    					}
    				}
    			},
    			user_email: {
    				validators: {
    					notEmpty: {
    						message: 'Vul user email in'
    					},
    					emailAddress: {
    						message: 'Dit is geen geldig email adres'
    					}
    				}
    			},
    			user_role: {
    				validators: {
    					notEmpty: {
    						message: 'Selecteer de user role'
    					}
    				}
    			},
    			user_status: {
    				validators: {
    					notEmpty: {
    						message: 'Selecteer de user status'
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
    						message: 'Het user naam in'
    					}
    				}
    			},
    			user_last_name: {
    				validators: {
    					notEmpty: {
    						message: 'Vul user achternaam'
    					}
    				}
    			},
    			user_email: {
    				validators: {
    					notEmpty: {
    						message: 'Vul user email in'
    					},
    					emailAddress: {
    						message: 'Dit is geen geldig email adres'
    					}
    				}
    			},
    			user_role: {
    				validators: {
    					notEmpty: {
    						message: 'Selecteer de user role'
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