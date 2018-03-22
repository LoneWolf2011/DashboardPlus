<?php
    // Create CSRF token
    $_SESSION['db_token'] =  bin2hex(random_bytes(32));  
	
    if(APP_INITIALIZE === 0) 
    { 
		header("Location: install.php"); 
    }    
	
    // At the top of the page we check to see whether the user is logged in or not 
    if(!empty($_SESSION[SES_NAME])) 
    { 
		$obj 	= new Login( new SafeMySQL());
		$obj->redirectLogin();
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
	
    <title><?= APP_TITLE; ?> | Login</title>

	<?php
		foreach($arr_css as $css){
			echo '<link href="'.URL_ROOT.$css.'" rel="stylesheet">';
		}
	?>

</head>

<body class="dark-bg" id="i18container">

    <div class="middle-box text-center loginscreen animated fadeInDown ">
        <div class="wrapper wrapper-content">
            <h1 class="logo-name text-center"><img src="<?= URL_ROOT_IMG.'/'.LOGO_NAME;?>" width="70%"></img></h1>

			<div class="view-header">

                <div class="header-icon">
                    <i class="pe page-header-icon pe-7s-unlock"></i>
                </div>
                <div class="header-title">
                    <h2><?= APP_TITLE;?> login</h2>
                    <h5 data-i18n="[html]loginscreen.text"></h5>
                </div>
            </div>		

		<?php 
			if(isset($_GET['fail'])){
					$search = '<div class="alert alert-danger" >
									<b data-i18n="[html]error_msg.die.label">Account locked</b><br><span data-i18n="[html]error_msg.die.msg"> for 2 hours.</span>
								</div>';					
			} elseif(isset($_GET['lck'])){
					$search = '<div class="alert alert-danger" >
									<b data-i18n="[html]loginmsg.lck.label">Account locked</b><br><span data-i18n="[html]loginmsg.lck.msg"> for 2 hours.</span>
								</div>';		
			} elseif(isset($_GET['dev'])){
					$search = '<div class="alert alert-warning" >
									<font color="orange"><b data-i18n="[html]loginmsg.dev.label">Login failed</b><br><span data-i18n="[html]loginmsg.dev.msg"> Geen DEV account.<br> Indien je toegang nodig hebt meldt dit bij de admins.</span>
								</div>';			
			} elseif(isset($_GET['blc'])){
					$search = '<div class="alert alert-danger" >
									<b data-i18n="[html]loginmsg.blc.label">Login failed</b><br><span data-i18n="[html]loginmsg.blc.msg"> User account is block. Contact your admins.</span>
								</div>';			
			} elseif(isset($_GET['id'])){
					$search = '<div class="alert alert-danger" >
									<b data-i18n="[html]loginmsg.id.label">Login failed</b><br><span data-i18n="[html]loginmsg.id.msg"> Check your initials or password.</span>
								</div>';
			} elseif(isset($_GET['err'])){
					$search = '<div class="alert alert-danger" >
									<b data-i18n="[html]loginmsg.err.label"><br><span data-i18n="[html]loginmsg.err.msg">  due to inactivity.</span>
								</div>';
			} elseif(isset($_GET['uknw'])){
					$search = '<div class="alert alert-danger" >
									<b data-i18n="[html]loginmsg.uknw.label"><br><br><span data-i18n="[html]loginmsg.uknw.msg"> User: '. $_GET['uknw'].' does not exist.</span> .
								</div>';
			} elseif(isset($_GET['tok'])){
				if($_GET['tok'] == "suc"){
					$search = '<div class="alert alert-success" >
									<b data-i18n="[html]loginmsg.tok.suc.label">Successful</b><br><span data-i18n="[html]loginmsg.tok.suc.msg"> Reset token requested.</span>
								</div>';		
				} elseif($_GET['tok'] == "err") {
					$search = '<div class="alert alert-danger">
									<b data-i18n="[html]loginmsg.tok.err.label">Reset token aanvraag mislukt</b><br><span data-i18n="[html]loginmsg.tok.err.msg"></span>
								</div>';	
				} elseif($_GET['tok'] == "inv") {
					$search = '<div class="alert alert-danger" >
									<b data-i18n="[html]loginmsg.tok.inv.label">Reset token aanvraag mislukt</b><br><span data-i18n="[html]loginmsg.tok.inv.msg"></span>
								</div>';		
				} elseif($_GET['tok'] == "uknw") {
					$search = '<div class="alert alert-danger" >
									<b data-i18n="[html]loginmsg.tok.uknw.label">Reset token aanvraag mislukt</b><br><span data-i18n="[html]loginmsg.tok.uknw.msg"></span>
								</div>';
				} elseif($_GET['tok'] == "exp") {
					$search = '<div class="alert alert-danger" >
									<b data-i18n="[html]loginmsg.tok.exp.label">Reset token aanvraag mislukt</b><br><span data-i18n="[html]loginmsg.tok.exp.msg"></span>
								</div>';								
				} else {
					$search = "";
				}		
			} elseif(isset($_GET['res'])){
				if($_GET['res'] == "suc"){
					$search = '<div class="alert alert-success" data-i18n="[html]login.res.suc">
									<b>Successful</b><br> Wachtwoord gereset. Mail verstuurd met nieuw wachtwoord naar: '.$_GET['ini'].'.
								</div>';	
				} elseif($_GET['res'] == "err") {
					$search = '<div class="alert alert-danger" data-i18n="[html]login.res.err">
									<b>Wachtwoord reset niet gelukt</b><br> Mail niet correct verzonden<br>Vraag een nieuwe token aan en meldt dit bij een admin.
								</div>';	
				} else {
					$search = "";
				}
			} else {
				$search = "";
			}
			
		echo $search; 
		
		?>			
			
            <form class="m-t" id="signinForm" action="Src/controllers/login.controller.php?login" method="post">
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="Email" name="email" required="" value="<?php 
				if(isset($_GET['id'])){ 
					echo strtolower($_GET['id']); 
				} elseif(isset($_GET['ini'])) { 
					echo strtolower($_GET['ini']); 
				}?>">
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" placeholder="Password" name="password" required="" style="color:#f6a821;">
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b" name="login" value="Login" data-i18n="[html]loginscreen.login">Login</button>

                <a class="link" id="password"><small data-i18n="[html]loginscreen.forget">Forgot password?</small></a>
				
				<input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">
            </form>
		
			<div id="show_form" style="display: none;">
				<form class="login-form" action="Src/controllers/login.controller.php?gentoken" method="post">
					<div class="form-group">
						<input type="email" class="form-control" placeholder="Email" name="email" required="">
					</div>			
					<button type="submit" name="request" value="request" class="btn btn-primary block full-width m-b" data-i18n="[html]loginscreen.request">Request </button>

					<a class="link" id="password" data-i18n="[html]loginscreen.login">Login</a>
					<input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">
				</form>
			</div>	

            <p class="m-t"> <small><?= date("D d-m-Y"). "<font color='#0092D0'> | </font>". date("H:i:s")."<font color='#0092D0'> | </font> ".APP_ENV." " . APP_VER; ?></small> </p>
			
        </div>
    </div>

    <!-- Mainly scripts -->
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>
	<script>
	$('.link').click(function() {
		$('#signinForm').animate({
			height: "toggle",
			opacity: "toggle"
		}, "slow");
		$("#show_form").animate({
			height: "toggle",
			opacity: "toggle"
		}, "slow");
	}); 
	</script>

</body>

</html>
