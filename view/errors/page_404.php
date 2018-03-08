<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel='shortcut icon' type='image/x-icon' href='<?= URL_ROOT_IMG; ?>leaf.ico' />

    <title><?= APP_TITLE;?> | 404 Error</title>
	
	<!-- Mainly CSS -->
	<?php
		foreach($arr_css as $css){
			echo '<link href="'.$css.'" rel="stylesheet">';
		}
	?>

</head>

<body class="gray-bg">


    <div class="middle-box text-center animated fadeInDown">
        <h1>404</h1>
        <h3 class="font-bold">Page Not Found</h3>

        <div class="error-desc">
			<p>
            Sorry, but the page you are looking for has note been found. Try checking the URL for error, then hit the refresh button on your browser or try found something else in our app.
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
