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
	
    <title><?= APP_TITLE; ?> | Graphs</title>
	
	<!-- Mainly CSS -->
	<?php
		foreach($arr_css as $css){
			echo '<link href="'.$css.'" rel="stylesheet">';
		}
	?>
	<style>
		.c3-axis-y text {
			fill: white;
			font-size:12px;
		}
		.c3-axis-x text {
			font-size:12px;
			fill:white;
		}
		.c3-legend-item {
			font-size:20px;
			fill: white;
		}	
		h2 {
			color: white;
		}
	</style>
</head>

<body class="mini-navbar" id="i18container" style="background-color: #282828;" >

	<div id="wrapper">
	
		<div >
	
			<?php include ROOT_PATH . $view_content; ?>

	
		</div>
		
	</div>
	
	<!-- Mainly scripts -->

</body>

</html>
