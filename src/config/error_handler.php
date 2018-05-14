<?php
/* 
==========================================================================================================

	Functie:
			- Writes errors too error.log if debug is FALSE in env.ini
			- Shows user friendly error message if debug is TRUE in env.ini
			- Log Fatal error too error.log file.

	Version: 1.2.0
	Author:	Roelof Jan van Golen - <r.vangolen@asb.nl>

==========================================================================================================
*/
error_reporting(E_ALL);

// Modify parse errors from php.ini through APP_DEBUG
// if APP_DEBUG = TRUE errors are shown in application
ini_set("display_errors", (APP_DEBUG === 1) ? 'on' : 'off');
define('ERROR_LOG_FILE', ROOT_PATH.'/Src/Logs/'.date("Y").'/Errors/'.date("Y-m-d").'_error.log');

/**
 * Custom error handler
 * @param integer $code
 * @param string $description
 * @param string $file
 * @param interger $line
 * @param mixed $context
 * @return boolean
 */
function handleError($code, $description, $file = null, $line = null, $context = null) {
    $displayErrors = ini_get("display_errors");
    $displayErrors = strtolower($displayErrors);
    if (error_reporting() === 0 || $displayErrors === 'on') {
        return false;
    }
    list($error, $log) = mapErrorCode($code);

    $datum 	= date("D Y-m-d H:i:s");
    $env 	= APP_ENV;
    $user 	= (isset($_SESSION[SES_NAME]['app_location_data'])) ? htmlentities($_SESSION[SES_NAME]['app_location_data'], ENT_QUOTES, 'UTF-8') : '---';

    $str 	= "[{$datum}] [{$error}] [{$user}] [{$env}] [{$file}, line {$line}] {$description}".PHP_EOL;
    fileLog($str);

    die( '<div class="text-center">
				<h3 class="font-bold text-danger">Oops!</h3>
				<div class="error-desc">
					<p>Something definitely went wrong here! Please try again, otherwise contact your administrators.</p>
				</div>
			</div>');
}

/**
 * This method is used to write data in file
 * @param mixed $logData
 * @param string $fileName
 * @return boolean
 */
function fileLog($logData, $fileName = ERROR_LOG_FILE) {

    // Open file
    $fileContent = @file_get_contents($fileName);
    $status = file_put_contents($fileName, $logData . $fileContent);
    /*
    $fh = fopen($fileName, 'a+');
        if (is_array($logData)) {
        $logData = print_r($logData, 1);
    }
    $status = fwrite($fh, $logData);
    fclose($fh);*/
    return ($status) ? true : false;
}

/**
 * Map an error code into an Error word, and log location.
 *
 * @param int $code Error code to map
 * @return array Array of error word, and log location.
 */
function mapErrorCode($code) {
    $error = $log = null;
    switch ($code) {
        case E_PARSE:
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
            $error = 'Fatal Error';
            $log = LOG_ERR;
            break;
        case E_WARNING:
        case E_USER_WARNING:
        case E_COMPILE_WARNING:
        case E_RECOVERABLE_ERROR:
            $error = 'Warning';
            $log = LOG_WARNING;
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $error = 'Notice';
            $log = LOG_NOTICE;
            break;
        case E_STRICT:
            $error = 'Strict';
            $log = LOG_NOTICE;
            break;
        case E_DEPRECATED:
        case E_USER_DEPRECATED:
            $error = 'Deprecated';
            $log = LOG_NOTICE;
            break;
        default :
            break;
    }
    return array($error, $log);
}

//calling custom error handler
set_error_handler("handleError");

/**
 * Catch fatal error and display nice error view to user.
 * Write fatal error to error log.
 *
 * @return Die statement with error message.
 */
function shutdown() {
    $isError = false;

    if ($error = error_get_last() ){
        switch($error['type']){
            case E_PARSE:
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                $datum 			= date("D Y-m-d H:i:s");
                $name 			= 'Fatal';
                $user 			= (isset($_SESSION[SES_NAME]['user_email'])) ? htmlentities($_SESSION[SES_NAME]['user_email'], ENT_QUOTES, 'UTF-8') : '---';
                $env 			= APP_ENV;
                $file 			= $error['file'];
                $line 			= $error['line'];
                $description 	= $error['message'];

                $str 	= "[{$datum}] [{$name}] [{$user}] [{$env}] [{$file}, line {$line}] {$description}".PHP_EOL;

                $isError = fileLog($str);
                break;
        }
    }

    if ($isError && APP_DEBUG !== 1){
        // If an ajax call is being proccesed
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $return = array();
            $return['title'] = "Fatal error";
            $return['msg']	 = '<b>URL</b><br>
						<small>'. htmlspecialchars($_SERVER['HTTP_REFERER']).'</small><br>
						<b>Error message</b><br>
						<pre>'.$error['message'].'</pre>
						<div style="margin-bottom: 20px;"></div>
						<b>Error on file</b><br>
						<small>'.$error['file'].'</small><br>
						<b>Error on line</b><br>
						<small>'.$error['line'].'</small><br>';

            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Content-type: application/json');
            die(json_encode($return));
        }

        die( '<div >
					<h3 class="font-bold text-danger">Oops!</h3>
					<div class="error-desc">
						<p>Something definitely went wrong here! Please try again, otherwise contact your administrators.</p>
					</div>
				</div>');
    }
}
register_shutdown_function('shutdown');

/* DIE MSG:
'<div class="container body">
            <div class="main_container">
                <div class="col-md-3 left_col" style="min-height: 0;">
                <div class="left_col scroll-view">

                    <div class="clearfix"></div>

                </div>
                </div>

                <!-- page content -->
                <div class="right_col" role="main" style="margin-left: 0;">
                <div class="">
                    <div class="kopBRAND">
                    <h1 class="text-danger">Fatal error</h1>
                    <b>URL</b><br>
                    <small> http://'.$_SERVER['HTTP_HOST'].htmlspecialchars($_SERVER['REQUEST_URI']).'</small><br>
                    <b>Error message</b><br>
                    <pre>'.$error['message'].'</pre>
                    </div>
                    <div style="margin-bottom: 20px;"></div>

                    <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                        <div class="x_title">

                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <table class="table table" width="100%">
                                <th>File</th><th>Error on file</th><th>Line</th>
                                <tr><td>'.htmlspecialchars($_SERVER['SCRIPT_FILENAME']).'</td><td>'.$error['file'].'</td><td>'.$error['line'].'</td></tr>
                            </table>
                        </div>
                        </div>
                    </div>
                    </div>

            </div>

            </div>
            </div>
        </div>'*/