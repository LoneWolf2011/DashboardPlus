<?php
    // Create CSRF token
    $_SESSION['db_token'] =  bin2hex(random_bytes(32));  
	
    if(APP_INITIALIZE === 0) 
    { 
		header("Location: install.php"); 
    }    
	
    // At the top of the page we check to see whether the user is logged in or not 
    if(!empty($_SESSION['db_user'])) 
    { 
		header("Location: ".URL_ROOT); 
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
	<link rel='shortcut icon' type='image/x-icon' href='<?= URL_ROOT_IMG; ?>leaf.ico' />
	
    <title><?= APP_TITLE; ?> | Login</title>

	<?php
		foreach($arr_css as $css){
			echo '<link href="'.$css.'" rel="stylesheet">';
		}
	?>

</head>

<body class="gray-bg" id="i18container">

    <div class="middle-box text-center loginscreen animated fadeInDown ">
        <div class="wrapper wrapper-content">
            <div>
                <!--<h1 class="logo-name">DB+</h1>-->
                <h1 class="logo-name"><img src="<?= URL_ROOT_IMG.'DB+.png';?>" width="70%"></img></h1>
            </div>
				<h3 ><span data-i18n="[html]loginscreen.welcome">Welcome to</span> <?= APP_NAME;?> </h3>
				<p data-i18n="[html]loginscreen.text">An improved experience for managing RMS and SCS.</p>
				<p data-i18n="[html]loginscreen.subtext">Login in. To see it in action.</p>

		<?php 
			if(isset($_GET['lck'])){
					$search = '<div class="alert alert-danger" >
									<font color="red"><b data-i18n="[html]loginmsg.blocked">Account locked</b></font><br><span data-i18n="[html]loginmsg.lck"> for 2 hours.</span>
								</div>';		
			} elseif(isset($_GET['dev'])){
					$search = '<div class="alert alert-warning" >
									<font color="orange"><b data-i18n="[html]loginmsg.failed">Login failed</b></font><br><span data-i18n="[html]loginmsg.lck"> Geen DEV account.<br> Indien je toegang nodig hebt meldt dit bij de admins.</span>
								</div>';			
			} elseif(isset($_GET['blc'])){
					$search = '<div class="alert alert-danger" >
									<font color="red"><b data-i18n="[html]loginmsg.failed">Login failed</b></font><br><span data-i18n="[html]loginmsg.blc"> User account is block. Contact your admins.</span>
								</div>';			
			} elseif(isset($_GET['id'])){
					$search = '<div class="alert alert-danger" >
									<font color="red"><b data-i18n="[html]loginmsg.failed">Login failed</b></font><br><span data-i18n="[html]loginmsg.id"> Check your initials or password.</span>
								</div>';
			} elseif(isset($_GET['err'])){
					$search = '<div class="alert alert-danger" data-i18n="[html]loginmsg.err">
									<font color="red"><b>Logged out</b></font><br> due to inactivity
								</div>';
			} elseif(isset($_GET['uknw'])){
					$search = '<div class="alert alert-danger" data-i18n="[html]loginmsg.uknw">
									<font color="red"><b>Error</b></font><br> User: '. $_GET['uknw'].' does not exist.
								</div>';
			} elseif(isset($_GET['tok'])){
				if($_GET['tok'] == "suc"){
					$search = '<div class="alert alert-success" >
									<font color="green"><b data-i18n="[html]loginmsg.tok.suc.label">Successful</b></font><br><span data-i18n="[html]loginmsg.tok.suc.msg"> Reset token requested.</span>
								</div>';		
				} elseif($_GET['tok'] == "err") {
					$search = '<div class="alert alert-danger">
									<font color="red"><b data-i18n="[html]loginmsg.tok.err.label">Reset token aanvraag mislukt</b></font><br><span data-i18n="[html]loginmsg.tok.err.msg"></span>
								</div>';	
				} elseif($_GET['tok'] == "inv") {
					$search = '<div class="alert alert-danger" >
									<font color="red"><b data-i18n="[html]loginmsg.tok.inv.label">Reset token aanvraag mislukt</b></font><br><span data-i18n="[html]loginmsg.tok.inv.msg"></span>
								</div>';		
				} elseif($_GET['tok'] == "uknw") {
					$search = '<div class="alert alert-danger" >
									<font color="red"><b data-i18n="[html]loginmsg.tok.uknw.label">Reset token aanvraag mislukt</b></font><br><span data-i18n="[html]loginmsg.tok.uknw.msg"></span>
								</div>';
				} elseif($_GET['tok'] == "exp") {
					$search = '<div class="alert alert-danger" >
									<font color="red"><b data-i18n="[html]loginmsg.tok.exp.label">Reset token aanvraag mislukt</b></font><br><span data-i18n="[html]loginmsg.tok.exp.msg"></span>
								</div>';								
				} else {
					$search = "";
				}		
			} elseif(isset($_GET['res'])){
				if($_GET['res'] == "suc"){
					$search = '<div class="alert alert-success" data-i18n="[html]login.res.suc">
									<font color="green"><b>Successful</b></font><br> Wachtwoord gereset. Mail verstuurd met nieuw wachtwoord naar: '.$_GET['ini'].'.
								</div>';	
				} elseif($_GET['res'] == "err") {
					$search = '<div class="alert alert-danger" data-i18n="[html]login.res.err">
									<font color="red"><b>Wachtwoord reset niet gelukt</b></font><br> Mail niet correct verzonden<br>Vraag een nieuwe token aan en meldt dit bij een admin.
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
                    <input type="password" class="form-control" placeholder="Password" name="password" required="">
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
			echo '<script src="'.$js.'"></script>';
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
