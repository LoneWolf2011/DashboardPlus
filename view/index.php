<?php
    // Check om te zien of de user ingelogged is of niet
    if(empty($_SESSION[SES_NAME])) 
    { 
        // Indien dit niet het geval is, redirect naar inlog pagina. 
        header("Location: ".URL_ROOT); 
         
		// Die statement is essentieel. Zonder dit kunnen users de 
		// privé content zien zonder ingelogd te zijn.
        die("Redirecting to ".URL_ROOT."index.php"); 
    } 	

?>
<!DOCTYPE html>
<html lang="<?= APP_LANG;?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel='shortcut icon' type='image/x-icon' href='<?= URL_ROOT_IMG; ?>/<?= FAVICON_NAME; ?>' />
	
    <title><?= APP_TITLE; ?> | Home</title>
	
	<!-- Mainly CSS -->
	<?php
		foreach($arr_css as $css){
			echo '<link href="'.URL_ROOT.$css.'" rel="stylesheet">';
		}
	?>
	
</head>

<body class="mini-navbar" id="i18container">

	<div id="wrapper">
	
		<?php include ROOT_PATH.ROOT_FILE['menu_side'];?>
	
		<div id="page-wrapper" class="gray-bg">
			<input type="text" hidden id="url_string" value="<?= URL_ROOT.'/Src/controllers/'.$view_basename.'.controller.php';?>" />
	
			<?php 
			// Top menu bar
			include ROOT_PATH.ROOT_FILE['menu_top'];
			
			// View content
			if(file_exists(ROOT_PATH . $view_content)){
				include ROOT_PATH . $view_content;
			} else {
				http_response_code(404);
				include ROOT_PATH.'/view/errors/page_404.php';
				die();
			}
			
			// Footer
			include ROOT_PATH.ROOT_FILE['menu_footer'];
			?>
	
		</div>

	</div>
	
	<!-- Mainly scripts -->

</body>

    <div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content animated bounceInRight">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <!--<i class="fa fa-laptop modal-icon"></i>-->
					<img src="<?= URL_ROOT_IMG.'/app_logo.png';?>" width="20%"/>
                    <h4 class="modal-title">Welkom bij DB+</h4>
					<?php 	if(htmlentities($_SESSION[SES_NAME]['user_new'], ENT_QUOTES, 'UTF-8') == 1) { ?>
						<p class="font-bold">Dit is de eerste keer dat u inlogd.</p>
						<p>	U dient uw wachtwoord te wijzigen naar een zelf gekozen wachtwoord. Dit om de veiligheid van uw account te kunnen waarborgen.</p>
					<?php };?>
                </div>
                <div class="modal-body">
				  <form name="form_update_acc" id="loginForm" class="form-horizontal form-label-left"> 

						<div class="form-group">
							<label class="control-label col-md-4 col-sm-4 col-xs-12">Wijzig E-Mail adres:</label>
							<div class="col-md-8 col-sm-8 col-xs-12">
								<input type="text" class="form-control" name="email" value="<?= htmlentities($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8'); ?>" placeholder="Email"/> </li>
							</div>
						</div>	
								
						<div class="form-group">
						<label class="control-label col-md-4 col-sm-4 col-xs-12">Wijzig wachtwoord:<font color="red">*</font></label>
						<div class="col-md-8 col-sm-8 col-xs-12">
								<input type="password" class="form-control" name="password" value="" placeholder="Wachtwoord"/>	
							</div>
						</div>	

						<div class="form-group">
						<label class="control-label col-md-4 col-sm-4 col-xs-12"></label>
							<div class="col-md-8 col-sm-8 col-xs-12">
								<div class="progress password-progress">
									<div id="strengthBar" class="progress-bar" role="progressbar" style="width: 0;"></div>
								</div>
							</div>
						</div>							
												
						<div class="form-group">
						<label class="control-label col-md-4 col-sm-4 col-xs-12">Bevestig wachtwoord:<font color="red">*</font></label>
						<div class="col-md-8 col-sm-8 col-xs-12">
								<input type="password" class="form-control" name="confirmPassword" value="" placeholder="Wachtwoord"/>	
							</div>
						</div>		
						
						<div class="form-group">
							<label class="control-label col-md-12 col-sm-12 col-xs-12" style="font-size: 12px;">(Vul je huidige wachtwoord in om de wijziging van email door te voeren)</label>
						</div>
						
						<input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">

						<div class="form-group" style="float: right;">
							<button type="button" class="btn btn-primary" data-dismiss="modal">Sluiten</button>
							<button type="submit" class="btn btn-success" name="update_account" value="Update Account" >Update</button>
						</div>
					</form>						
				</div>		
				<div class="modal-footer">

				</div>             

            </div>
        </div>
    </div>
	<input type="text" hidden id="index_url_string" value="<?= URL_ROOT.'/Src/controllers/user.controller.php';?>" />
</html>

	<script>
	$(document).ready(function() {
		var lang_code = $('html').attr('lang').toLowerCase()+'_'+$('html').attr('lang').toUpperCase();
	
		$('#loginForm').formValidation({
			framework: 'bootstrap',
			icon: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			locale: lang_code,
			fields: {
				email: {
					validators: {
						emailAddress: {
						}
					}
				},
				password: {
					validators: {
						notEmpty: {
						},
						// Password meter
						callback: {
							callback: function(value, validator, $field) {
								var password = $field.val();
								if (password == '') {
									return true;
								}
	
								var result  = zxcvbn(password),
									score   = result.score,
									message = result.feedback.warning || 'Het wachtwoord is te zwak';
	
								// Update the progress bar width and add alert class
								var $bar = $('#strengthBar');
								switch (score) {
									case 0:
										$bar.attr('class', 'progress-bar progress-bar-danger')
											.css('width', '1%');
										break;
									case 1:
										$bar.attr('class', 'progress-bar progress-bar-danger')
											.css('width', '25%');
										break;
									case 2:
										$bar.attr('class', 'progress-bar progress-bar-danger')
											.css('width', '50%');
										break;
									case 3:
										$bar.attr('class', 'progress-bar progress-bar-warning')
											.css('width', '75%');
										break;
									case 4:
										$bar.attr('class', 'progress-bar progress-bar-primary')
											.css('width', '100%');
										break;
								}
	
								// We will treat the password as an invalid one if the score is less than 3
								if (score < 3) {
									return {
										valid: false,
										message: message
									}
								}
								return true;
							}
						}
					}
				},								
				confirmPassword: {
					validators: {
						identical: {
							field: 'password'
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
			
			// Ajax om de data te posten naar de db
			$.ajax({
				type: "POST",
				url: $('#index_url_string').val()+ "?update",
				data: $('form[name="form_update_acc"]').serialize(),
				success: function(data){
					$('#myModal').modal('hide');
					swal({
						title: data.label,
						text: data.text,
						type: data.type
					});
				},
				error: function(data){
					$('#myModal').modal('hide');
					swal({
						title: data.label,
						text: data.text,
						type: data.type
					});
				}
			});
		});		
	});	
	</script>
	<?php 
	if(htmlentities($_SESSION[SES_NAME]['user_new'], ENT_QUOTES, 'UTF-8') == 1) {
		echo "<script>$('#myModal').modal('show');</script>";
	}
	?>
