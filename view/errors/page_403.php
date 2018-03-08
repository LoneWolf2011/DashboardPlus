<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel='shortcut icon' type='image/x-icon' href='<?= URL_ROOT_IMG; ?>leaf.ico' />

    <title><?= APP_TITLE;?> | 403 Error</title>
	
	<!-- Mainly CSS -->
	<?php
		foreach($arr_css as $css){
			echo '<link href="'.$css.'" rel="stylesheet">';
		}
	?>

</head>

<body class="gray-bg">


    <div class="middle-box text-center animated fadeInDown">
        <h1>403</h1>
        <h3 class="font-bold ">Access denied</h3>

        <div class="error-desc">
           <p>
			Admin rechten zijn verplicht voor toegang. Neem contact op met de admins.
            </p>
			<a href="<?= URL_ROOT; ?>Src/login/redirect.php" class='btn btn-primary' >Return to safety</a>
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
