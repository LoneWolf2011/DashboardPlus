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

    if($requestedAction == '' ){
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
	
    <title><?= APP_TITLE; ?> | <?= $requestedController; ?></title>
	
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
    <?php
    foreach($arr_js as $js){
        echo '<script src="'.URL_ROOT.$js.'"></script>';
    }
    ?>
</body>

</html>

	<script>
	$(document).ready(function() {
		var lang_code = $('html').attr('lang').toLowerCase()+'_'+$('html').attr('lang').toUpperCase();

        $('.autocomplete-append').autocomplete({
            serviceUrl: <?= json_encode(URL_ROOT);?>+'/Src/helpers/scs_naw_hint.json.php',
            max: 10,
            onSelect: function (suggestion) {
                //alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
                location.href = <?= json_encode(URL_ROOT);?>+'/location/?'+suggestion.value.replace(/[^0-9\.]+/g, "");
            }
        });

	});	
	</script>
	<?php 
	if(htmlentities($_SESSION[SES_NAME]['user_new'], ENT_QUOTES, 'UTF-8') == 1) {
	    include 'user_model.view.php';
		echo "<script>$('#myModal').modal('show');</script>";
	}
	?>
    <?php
    } else {

        $toolPath = ROOT_PATH . '/view/' . $requestedController .'/'. $requestedAction.'.view.php';
        //echo $toolPath;

        if (file_exists($toolPath)) {
            include ROOT_PATH . '/view/' . $requestedController .'/template.view.php';
        } else {
            http_response_code(404);
            include ROOT_PATH . '/view/errors/page_404.php';
            die();
        }
    };?>