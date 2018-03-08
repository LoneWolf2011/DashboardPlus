<?php
    // Check om te zien of de user ingelogged is of niet
    if(empty($_SESSION['user'])) 
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
	<link rel='shortcut icon' type='image/x-icon' href='<?= URL_ROOT_IMG; ?>leaf.ico' />
	
    <title><?= APP_TITLE; ?> | Home</title>
	
	<!-- Mainly CSS -->
	<?php
		foreach($arr_css as $css){
			echo '<link href="'.$css.'" rel="stylesheet">';
		}
	?>
	
</head>

<body class="mini-navbar" id="i18container">

	<div id="wrapper">
	
		<?php include ROOT_PATH.ROOT_FILE['menu_side'];?>
	
		<div id="page-wrapper" class="gray-bg">
		
			<?php include ROOT_PATH.ROOT_FILE['menu_top'];?>
			<?php include ROOT_PATH . $view_content; ?>
			<?php include ROOT_PATH.ROOT_FILE['menu_footer'];?>
	
		</div>
		
	</div>
	
	<!-- Mainly scripts -->

</body>

</html>
