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
            if(file_exists($toolPath)){
                include $toolPath;
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

    </body>

    </html>
