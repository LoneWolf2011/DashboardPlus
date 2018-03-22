<?php
    if(APP_INITIALIZE === 1) 
    { 
		header("Location: /mdb/view"); 
    }   
	
    // At the top of the page we check to see whether the user is logged in or not 
    if(!empty($_SESSION[SES_NAME])) 
    { 
		header("Location: Src/Login/redirect.php"); 
    } 
     
    // Everything below this point in the file is secured by the login system 

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
	
    <title><?= APP_TITLE; ?> | Install</title>

	<?php
		foreach($arr_css as $css){
			echo '<link href="'.URL_ROOT.$css.'" rel="stylesheet">';
		}
	?>

</head>

<body class="dark-bg" id="i18container">

    <div class="middle-box loginscreen animated fadeInDown ">
        <div class="wrapper wrapper-content">
            <div class="text-center">
                <!--<h1 class="logo-name">DB+</h1>-->
				<h1 class="logo-name text-center"><img src="<?= URL_ROOT_IMG.'/'.LOGO_NAME;?>" width="70%"></img></h1>    
				<h3 ><span data-i18n="[html]installscreen.welcome">Welcome to</span> <?= APP_NAME;?> </h3>
				<p data-i18n="[html]installscreen.text">This is your first time.</p>
				<p data-i18n="[html]installscreen.subtext">Please take the time to fill in the below details.</p>
				<?php 
				if(version_compare(phpversion(),'7.0.0','<')){
					echo '<div class="alert alert-danger" >
									<font color="red"><b data-i18n="[html]installscreen.phpversion.err">PHP version to low</b></font><br> <span data-i18n="[html]installscreen.phpversion.msg">Version 7.0.0 is a minimum requirement.</span>
								</div>';
				} else {
				?>
				<div id="res_msg"></div>
		
			</div>
            <form class="m-t" id="form">	
				<h2 data-i18n="[html]installscreen.admin.txt">Admin configuration</h2>
                <div class="form-group">
					<label data-i18n="[html]installscreen.admin.email">Enter admin email</label>
                    <input type="email" class="form-control" placeholder="Email" name="admin_email" required="" value="">
                </div>
                <div class="form-group">
					<label data-i18n="[html]installscreen.admin.local">Enter location</label>
                    <select class="form-control"  name="default_local" required="">
						<option value="52.032633,5.191266">Nederland</option>
						<option value="52.255518,-1.074320">England</option>
						<option value="-23.359708,-45.196270">Brazil</option>
					</select>
                </div>					
                <div class="form-group">
					<label data-i18n="[html]installscreen.admin.lang">Select default language</label>
                    <select class="form-control"  name="default_lang" required="">
						<option value="nl">Nederlands</option>
						<option value="en">English</option>
						<option value="pt">Portugues</option>
					</select>
                </div>
				<?php if(empty($env['APP']['URL_ROOT'])){ ?>
				<h2 data-i18n="[html]installscreen.app.txt">APP settings</h2>
                <div class="form-group">
					<label data-i18n="[html]installscreen.app.root">URL root</label>
                    <input type="text" class="form-control" placeholder="URL root" name="app_url_root" value="<?= pathUrl();?>">
                </div>
                <div class="form-group">
					<label data-i18n="[html]installscreen.app.docu">Document root</label>
                    <input type="text" class="form-control" placeholder="Document root" name="app_document_root" value="<?= $_SERVER['DOCUMENT_ROOT'];?>">
                </div>
                <div class="form-group">
					<label data-i18n="[html]installscreen.app.api">Google API key</label>
                    <input type="text" class="form-control" placeholder="Google API" name="app_google_key" value="">
                </div>
				<?php }; ?>				
				<?php if(empty($env['SCS_DB']['HOST'])){ ?>
				<h2 data-i18n="[html]installscreen.scs.txt">SCS connection</h2>
                <div class="form-group">
					<label >Host IP</label>
                    <input type="text" class="form-control" placeholder="IP address" name="scs_host" value="">
                </div>
                <div class="form-group">
					<label data-i18n="[html]installscreen.scs.user">Username</label>
                    <input type="text" class="form-control" placeholder="username" name="scs_user" value="">
                </div>
                <div class="form-group">
					<label data-i18n="[html]installscreen.scs.pass">Password</label>
                    <input type="password" class="form-control" placeholder="Password" name="scs_pass" value="">
                </div>
				<?php }; ?>

                <button type="submit" class="btn btn-primary block full-width m-b" name="install" value="Install" data-i18n="[html]installscreen.login">Install</button>
				<input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">
            </form>
				<?php }; ?>
			<div class="text-center">
				<p class="m-t"> <small><?= date("D d-m-Y"). "<font color='#0092D0'> | </font>". date("H:i:s")."<font color='#0092D0'> | </font> ".APP_ENV." " . APP_VER; ?></small> </p>
			</div>
        </div>
    </div>

    <!-- Mainly scripts -->
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>
	<script>
	$(document).ready(function() {
		var lang_code = $('html').attr('lang').toLowerCase()+'_'+$('html').attr('lang').toUpperCase();

		$('#form').formValidation({
			framework: 'bootstrap',
			locale: lang_code,
			icon: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				email: {
					validators: {
						emailAddress: {
							message: 'The value is not a valid email address'
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
				url: "Src/controllers/login.controller.php?install",
				data: $('#form').serialize(),
				success: function(res){
					$('#res_msg').html(res.body);
					//alert(data);
				},
				error: function(xhr, status, error){
					var json = $.parseJSON(xhr.responseText);
					$('#ModalTpl').modal('show');
					$('#ModalHeader').addClass('modal-header-danger');						
					$('#ModalTpl .modal-title').html(json.title);
					$('#ModalTpl .modal-body').html(json.msg);
					$('#ModalTpl .modal-footer a').attr('href',window.location.href);	
				}
			});
		});		
	});	
	</script>
</body>

</html>
