<?php
    $year 		= date("Y");
    $today		= date("Y-m-d");

    // Check if GET is defined and not empty
    if(isset($_GET['file']) && $_GET['file'] != '')
    {
        // parse file, prevent access to other files
        $parse_file  = strtolower(preg_replace("/[^a-z0-9-_.]/", "", $_GET['file']));
        // if filename contains 'error' add errors directory
        if(strpos($parse_file, 'error') !== false){
            $parse_file = 'errors/'.$parse_file;
        }

        $filename = $year.'/'.$parse_file;
    } else {
        $filename = $year.'/'.$today.'.log';
    }

    // file location
    $filelocation 	= '../../src/logs/'.$filename;

    // show content of file
    $file = file($filelocation);
    echo '<pre style="height: 520px; overflow: auto;">';
    foreach($file as $text) {
        echo $text;
    }
    echo "</pre>";
