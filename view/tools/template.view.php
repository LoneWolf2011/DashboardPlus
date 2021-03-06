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
    <link rel='shortcut icon' type='image/x-icon' href='<?= URL_ROOT_IMG; ?>/leaf.ico' />

    <title><?= APP_TITLE; ?> | <?= $requestedAction;?></title>

    <!-- Mainly CSS -->
    <?php
    // View specific CSS
    array_push($arr_css, '/css/dash_custom.css');
    ?>
    <?php
    foreach($arr_css as $css){
        echo '<link href="'.URL_ROOT.$css.'" rel="stylesheet">';
    }
    ?>
    <style>
        #chart .c3-circles-Trend {
            display: none;
        }
        .map-div {
            background-color: #414141;
            border-radius: 6px;
            padding:15px;
            margin-bottom: 10px;
            border: 1px solid  #232323;
        }
        .map-div-table {
            display:none;
            font-size: 15px;
            padding:4px;
            margin:6px;
            width:350px;
            color: white;"
        }
        .map-div-grouped {
            font-size: 15px;
            padding:4px;
            margin:6px;
            width:250px;
        }
        html, body {
            height:100%;
            width:100%;
        }
        #map {
            height:100%;
            width:100%;
        }
    </style>
</head>

<body class="mini-navbar" id="i18container" style="background-color: #282828;" >

<div id="wrapper" style="height:100%">

    <?php
    include $toolPath;
    ?>

</div>

</body>

</html>
