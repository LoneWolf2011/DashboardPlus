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
	<link rel='shortcut icon' type='image/x-icon' href='<?= URL_ROOT_IMG; ?>/<?= FAVICON_NAME; ?>' />
	
    <title><?= APP_TITLE; ?> | Recover</title>

	<?php
		foreach($arr_css as $css){
			echo '<link href="'.URL_ROOT.$css.'" rel="stylesheet">';
		}
	?>

</head>

<body class="" id="i18container">

    <div class="middle-box text-center loginscreen animated fadeInDown ">
        <div class="wrapper wrapper-content">
			<h1 class="logo-name text-center"><img src="<?= URL_ROOT_IMG.'/'.LOGO_NAME;?>" width="70%"></img></h1>
			<div class="view-header">

                <div class="header-icon">
                    <i class="pe page-header-icon pe-7s-key"></i>
                </div>
                <div class="header-title">
                    <h2 data-i18n="[html]tokenmsg.welcome"><?= APP_TITLE;?> login</h2>
                    <h5 data-i18n="[html]tokenmsg.text"></h5>
                </div>
            </div>		

            <form class="m-t" id="signinForm" action="Src/controllers/login.controller.php?recover" method="post">
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="Email" name="email" required="" value="<?php if(isset($_GET['id'])){ echo strtolower($_GET['id']); }?>">
                </div>
				<?php if(isset($_GET['rec'])){ ?>
				 <div class="form-group">
					<input type="text" class="form-control" name="token" placeholder="Token" value="<?php if(isset($_GET['rec'])){ echo $_GET['rec']; }?>" />
				</div>
				<?php };?>
				
                <button type="submit" class="btn btn-primary block full-width m-b" name="recover" value="Recover" data-i18n="[html]tokenmsg.button">Recover</button>
				
				<input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['db_token'], ENT_QUOTES, 'UTF-8');?>">
            </form>
		
            <p class="m-t"> <small><?= date("D d-m-Y"). "<font color='#0092D0'> | </font>". date("H:i:s")."<font color='#0092D0'> | </font> ".APP_ENV." " . APP_VER; ?></small> </p>
			
        </div>
    </div>

    <!-- Mainly scripts -->
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.URL_ROOT.$js.'"></script>';
		}		
	?>

</body>

</html>
