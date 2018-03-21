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
	
    <title><?= APP_TITLE; ?> | Recover</title>

	<?php
		foreach($arr_css as $css){
			echo '<link href="'.$css.'" rel="stylesheet">';
		}
	?>

</head>

<body class="gray-bg" id="i18container">

    <div class="middle-box text-center loginscreen animated fadeInDown ">
        <div class="wrapper wrapper-content">
		
                <!--<h1 class="logo-name">DB+</h1>-->
                <h1 class="logo-name"><img src="<?= URL_ROOT_IMG.'DB+.png';?>" width="70%"></img></h1>
				<h3 data-i18n="[html]tokenmsg.welcome">Welcome to DB+</h3>
				<p data-i18n="[html]tokenmsg.text">An improved experience for managing RMS and SCS.</p>

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
		
            <p class="m-t"> <small><?= date("D d-m-Y"). "<font color='#0092D0'> | </font>". date("H:i:s")."<font color='#0092D0'> | </font> ".APP_ENV." " . appVersionCode(APP_ENV); ?></small> </p>
			
        </div>
    </div>

    <!-- Mainly scripts -->
	<?php
		foreach($arr_js as $js){
			echo '<script src="'.$js.'"></script>';
		}		
	?>

</body>

</html>
