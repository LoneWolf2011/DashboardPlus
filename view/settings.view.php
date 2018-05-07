	<?php 		array_push($arr_css, '/css/plugins/dataTables/datatables_responsive.min.css');?>
	<div class="wrapper wrapper-content animated fadeInRight">

		<div class="row">
			<div class="col-lg-6">
				<div class="ibox float-e-margins">
					<div class="ibox-title">
						<h5><span>Edit settings</span> <small></small></h5>
					</div>
					<div class="ibox-content">
						<div class="row">
							<form id="edit_site_name" name="edit_site_name">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>App name</span></label> 
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="app_name" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>App title</span></label>
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="app_title" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>App email</span></label> 
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="app_email" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>App latitude</span></label> 
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="app_lat" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>App longitude</span></label> 
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="app_lng" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>App language</span></label> 
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="app_lang" type="text">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label" for="first-name"><span>App initialized</span></label> 
										<input class="form-control" data-i18n="[placeholder]placeholders.input" name="app_initialize" type="number" min="0" max="1">
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
		</div>

	</div>
	
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>	

	<script>
    $(document).ready(function() {
		getLocation();
		
    	var url_string = $('#url_string').val();
		var lang_code = $('html').attr('lang').toLowerCase() + '_' + $('html').attr('lang').toUpperCase();
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
    			app_name: {
    				validators: {
    					notEmpty: {
    						message: 'Vul site naam in'
    					}
    				}
    			},
    			app_title: {
    				validators: {
    					notEmpty: {
    						message: 'Vul site locatie in'
    					}
    				}
    			},
    			app_email: {
    				validators: {
    					notEmpty: {
    						message: 'Vul het site adres in'
    					}
    				}
    			},
    			app_lat: {
    				validators: {
    					notEmpty: {
    						message: 'Vul de site postcode in'
    					}
    				}
    			},
    			app_lng: {
    				validators: {
    					notEmpty: {
    						message: 'Vul de site stad in'
    					}
    				}
    			},
    			app_lang: {
    				validators: {
    					notEmpty: {
    						message: 'Vul de site stad in'
    					}
    				}
    			},
    			app_initialize: {
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

					$('#edit_site_name').find("input[type=text], textarea").val("");
					fv.resetForm();
					getLocation();
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
	
	
	function getLocation(){
		$.ajax({
			type: 'GET',
			url: url_str+"?get=settings",
			success: function(data) {

				$('input[name=app_name]').val(data.app_name);
				$('input[name=app_title]').val(data.app_title);
				$('input[name=app_email]').val(data.app_email);
				$('input[name=app_lat]').val(data.app_lat);
				$('input[name=app_lng]').val(data.app_lng);
				$('input[name=app_lang]').val(data.app_lang);
				$('input[name=app_initialize]').val(data.app_initialize);

			}
		});			
	}		
	</script>	