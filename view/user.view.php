<?php
		$wb_conn = new SafeMySQL();
		$row = $wb_conn->getRow("SELECT * FROM app_users WHERE user_email = ?s",htmlentities($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8') );			
		

	?>
	<div class="wrapper wrapper-content animated fadeInRight">
        <h2 class="m-b-xs"><i class="pe pe-7s-user text-warning m-r-xs"></i> <span data-i18n="[html]user.title">User</span></h2>	

        <div class="row">
            <div class="col-lg-6">
					<div class="ibox float-e-margins">
						<div class="ibox-title">
							<h5><span data-i18n="[html]user.edit.title"> Edit user</span> <small></small></h5>
						</div>
						<div class="ibox-content">
							<div class="row">
								<form name="update_user" id="update_user" >
		
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label" for="first-name"><span data-i18n="[html]user.edit.input.1">User name</span><font color="red">*</font></label>
											<input type="text" class="form-control" name="user_name" value="<?= @$row['user_name'];?>" data-i18n="[placeholder]tickets.create.placeholder" >
										</div>									
									</div>									
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label" for="first-name"><span data-i18n="[html]user.edit.input.2">User last name</span><font color="red">*</font></label>
											<input type="text" class="form-control" name="user_last_name" value="<?= @$row['user_last_name'];?>" data-i18n="[placeholder]tickets.create.placeholder"   >
										</div>								
									</div>								
			
									<div class="col-md-6">
										<div class="form-group">
											<label class="control-label" for="first-name"><span data-i18n="[html]user.edit.input.3">User email</span><font color="red">*</font></label>
											<input type="text" class="form-control" name="user_email" value="<?= @$row['user_email'];?>" data-i18n="[placeholder]tickets.create.placeholder" value="<?= @$row['SCS_Account_Address_Name']; ?>">
										</div>									
									</div>									

									<input type="text" class="form-control hidden"  name="user_id" value="<?= @$row['user_id'];?>" >
									<input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">
									
									<div class="col-md-12">
										<div class="form-group">
											<button class="btn btn-primary" name="save_button" ><i class='fa fa-save fa-fw'></i> <span data-i18n="[html]user.edit.button">Update</span></button>	
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
		
	?>		
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>	
    <script>
    $(document).ready(function() {
		

		$('#datatable').on('click', '#delete', function(){
			var id =$(this).attr('value');
			var user_email =$(this).attr('rel');
			var csrf_token =$('input[name="csrf"]').attr('value');
			
			swal({
				html:true,
				title: "Weet je het zeker?",
				text: "De gebruiker: <b>"+user_email+"</b> wordt permanent verwijderd!",
				type: "warning",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Yes, verwijder user",
				closeOnConfirm: false
			}, function () {
				$.ajax({
                    type: "post",
                    url: <?= json_encode(URL_ROOT);?>+"/Src/controllers/user.controller.php?delete",
                    data: { user_id : id, csrf : csrf_token },
                    success: function(data){
						swal({
							html:true, 
							title: data.title,
							text: data.body,
							type: data.type
						});
						table_active.ajax.reload( null, false ); 
                    }
                });				
			});	
		});
		
		var lang_code = $('html').attr('lang');
		$.extend( true, $.fn.dataTable.defaults, {
			language: {
				url: <?= json_encode(URL_ROOT);?>+'/js/plugins/dataTables/'+$('html').attr('lang')+'.json'
			},
			iDisplayLength: 10,
			deferRender: true,
			order: [[ 3, "desc"]],
			lengthMenu: [ 10, 20, 25 ],
			processing: true,
			serverSide: true,
					
		} );		
		
		var interval;
		var table_active = $(".datatable").DataTable({
			responsive: true,
			ajax: <?= json_encode(URL_ROOT);?>+"/Src/controllers/user.controller.php?get=users",
			fnInitComplete: function(oSettings, json) {			
				$.i18n.init({
					resGetPath: <?= json_encode(URL_ROOT);?>+'/src/lang/__lng__.json',
					load: 'unspecific',
					fallbackLng: false,
					lng: $('html').attr('lang')
				}, function (t){
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
		})		
		.on('success.field.fv', function(e, data) {
			if(data.fv.getInvalidFields().length > 0) {
				data.fv.disableSubmitButtons(true);
			}
		}) 
		.on('success.form.fv', function(e) { 
			// Voorkom form submission 
			e.preventDefault(); 
			var $form = $(e.target), 
			fv = $form.data('formValidation'); 


			$.ajax({
				type: "POST",
				url: <?= json_encode(URL_ROOT);?>+"/Src/controllers/user.controller.php?updateuser",
				data: $('form[name="update_user"]').serialize(),
				success: function(data){
					swal({
						html:true, 
						title: data.title,
						text: data.body,
						type: data.type
					});
					table_active.ajax.reload( null, false ); 
				},
				error: function(xhr, status, error){
					var json = $.parseJSON(xhr.responseText);
					console.log(json);
					swal({
						html:true, 
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
		})		
		.on('success.field.fv', function(e, data) {
			if(data.fv.getInvalidFields().length > 0) {
				data.fv.disableSubmitButtons(true);
			}
		}) 
		.on('success.form.fv', function(e) { 
			// Voorkom form submission 
			e.preventDefault(); 
			var $form = $(e.target), 
			fv = $form.data('formValidation'); 


			$.ajax({
				type: "POST",
				url: <?= json_encode(URL_ROOT);?>+"/Src/controllers/user.controller.php?new",
				data: $('form[name="new_wb"]').serialize(),
				success: function(data){
					swal({
						html:true, 
						title: data.title,
						text: data.body,
						type: data.type
					});
					table_active.ajax.reload( null, false ); 
				},
				error: function(xhr, status, error){
					var json = $.parseJSON(xhr.responseText);
					console.log(json);
					swal({
						html:true, 
						title: json.title,
						text: json.msg,
						type: "error"
					});
				}
			});
		});		
	});
	</script>	