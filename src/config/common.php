<?php
/*
==========================================================================================================
	
	Name: Common file
	Functie:
	
	Version: 1.0.8
	Author:	Roelof Jan van Golen - <r.vangolen@asb.nl>
	
==========================================================================================================
*/
	
	// define('ROOT_PATH', $_SERVER["DOCUMENT_ROOT"]);
	
	$content = file_get_contents($_SERVER["DOCUMENT_ROOT"] . '/sentdb/env.ini');
	$env = parse_ini_string($content, true);
	
	// $env = parse_ini_file($_SERVER["DOCUMENT_ROOT"].'/sentdb/env.ini', true);
	// DB connectie for mdb database
	
	define('DB_HOST', $env['LOCAL_DB']['HOST']);
	define('DB_USER', $env['LOCAL_DB']['USER']);
	define('DB_PASS', $env['LOCAL_DB']['PASS']);
	define('DB_NAME', $env['LOCAL_DB']['NAME']);
	define('DB_LOGS', $env['LOCAL_DB']['LOGS']);
	
	$options = array(
		PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
	);
	try {
		$db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS, $options);
	}
	catch(PDOException $ex) {
		die("Failed to connect to the database: " . $ex->getMessage());
	}
	
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			
	define('LOGO_NAME', $env['APP']['LOGO_NAME']);
	define('FAVICON_NAME', $env['APP']['FAVICON_NAME']);
	define('URL_ROOT', $env['APP']['URL_ROOT']);
	
	// define('URL_ROOT_OTAP', getSetting($db, 'URL_ROOT_OTAP'));

	define('URL_ROOT_IMG', $env['APP']['URL_ROOT_IMG']);
	define('ROOT_PATH', $env['APP']['ROOT_PATH']);
	define('GOOGLE_API', $env['APP']['GOOGLE_API']);
	define('WEB_API', $env['API']['WEB_API']);
	define('WEB_USER', $env['API']['WEB_USER']);
	define('WEB_PASS', $env['API']['WEB_PASS']);
	define('APP_NAME', getSetting($db, 'APP_NAME'));
	define('APP_TITLE', getSetting($db, 'APP_TITLE'));
	define('APP_EMAIL', getSetting($db, 'APP_EMAIL'));
	define('SES_NAME', $env['APP']['SES_NAME']);
	define('APP_ENV', $env['APP']['ENV']);
	define('APP_VER', $env['APP']['VER']);
	define('APP_LANG', getSetting($db, 'APP_LANG'));
	define('APP_LAT', (int)getSetting($db, 'APP_LAT'));
	define('APP_LNG', (int)getSetting($db, 'APP_LNG'));
	define('APP_DEBUG', (int)$env['APP']['DEBUG']);
	define('APP_INITIALIZE', (int)getSetting($db, 'APP_INITIALIZE'));
	
	// Define events threshold
	
	define('ENABLE_AUDIO', $env['EVENTS']['ENABLE_AUDIO']);
	define('ENABLE_GROUPED_EVENTS', $env['EVENTS']['ENABLE_GROUPED_EVENTS']);
	define('GROUPED_EVENTS', $env['EVENTS']['GROUPED_EVENTS']);
	define('GROUPED_EVENTS_WARNING', $env['EVENTS']['GROUPED_EVENTS_WARNING']);
	define('GROUPED_EVENTS_DANGER', $env['EVENTS']['GROUPED_EVENTS_DANGER']);
	
	// Define SMTP settings
	
	define('SMTP_HOST', $env['SMTP']['SMTP_HOST']);
	define('SMTP_PORT', (int)$env['SMTP']['SMTP_PORT']);
	
	// Define SCS conn
	
	define('SCS_DB_HOST', $env['SCS_DB']['HOST']);
	define('SCS_DB_USER', $env['SCS_DB']['USER']);
	define('SCS_DB_PASS', $env['SCS_DB']['PASS']);
	define('SCS_DB_NAME', $env['SCS_DB']['NAME']);
	define('SCS_DB_CONN', array(
		'host' => SCS_DB_HOST,
		'user' => SCS_DB_USER,
		'pass' => SCS_DB_PASS,
		'db' => SCS_DB_NAME
	));
	
	// Make language file available as variable
	$lang = json_decode(file_get_contents(URL_ROOT.'/Src/lang/'.APP_LANG.'.json'), true);
	define('LANG',$lang);
	
	// Define RMS conn
	// define('RMS_DB_HOST', $env['RMS_DB']['HOST']);
	// define('RMS_DB_USER', $env['RMS_DB']['USER']);
	// define('RMS_DB_PASS', $env['RMS_DB']['PASS']);
	// define('RMS_DB_NAME', $env['RMS_DB']['NAME']);
	// define('RMS_DB_CONN', array(
	//	'host' 	=> RMS_DB_HOST,
	//	'user' 	=> RMS_DB_USER,
	//	'pass' 	=> RMS_DB_PASS,
	//	'db' 	=> RMS_DB_NAME
	// ));
	
	// Include file router	
	require ROOT_PATH . '/Src/file_package.php';
	
	// Function files	
	foreach(ROOT_FILE['FUNC'] as $func) {
		require ROOT_PATH . $func;
	
	}
	
	// Libs
	
	foreach(ROOT_FILE['LIBS'] as $libs) {
		require ROOT_PATH . $libs;
	
	}
	
	// Class files
	
	foreach(ROOT_FILE['CLASS'] as $class) {
		require ROOT_PATH . $class;
	
	}
	
	// Define function to get settings
	
	function getSetting($db_conn, $setting_name)
	{
		$stmt = $db_conn->prepare("SELECT * FROM app_settings LIMIT 1");
		$stmt->execute();
		$row = $stmt->fetch();
		return $row[strtolower($setting_name) ];
	}
	
	function getClasses($path)
	{
		$class_arr = array();
		$scanned_directory = array_diff(scandir($path) , array(
			'..',
			'.'
		));
		foreach($scanned_directory as $file) {
			$file_name = substr($file, strpos($file, ".") + 1);
			$file_key = substr($file_name, 0, strpos($file_name, "."));
	
			// echo $variable.'<br />';
	
			$class_arr[strtolower($file_key) ] = '/src/classes/' . $file;
		}
	
		return $class_arr;
	}
	
	if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
		function undo_magic_quotes_gpc(&$array)
		{
			foreach($array as & $value) {
				if (is_array($value)) {
					undo_magic_quotes_gpc($value);
				}
				else {
					$value = stripslashes($value);
				}
			}
		}
	
		undo_magic_quotes_gpc($_POST);
		undo_magic_quotes_gpc($_GET);
		undo_magic_quotes_gpc($_COOKIE);
	}
	
	// Set header content
	
	header('Content-Type: text/html; charset=UTF-8');
	
	// Start session
	
	session_start();
	
	// DB connection with wrapper
	$db_conn = new SafeMySQL();
	
	// Set package const as variable
	$arr_css = ROOT_CSS;
	$arr_js = ROOT_JS;
	
	// Update user last access
	
	$id = @$_SESSION[SES_NAME]['user_id'];
	updateLastAccess($db, $id);
	
	// Get url and set view relative to view folder
	
	$url_arr = parse_url($_SERVER['REQUEST_URI']);
	
	// Prevent access to errors and layout folders
	
	if ($url_arr['path'] == '/view/errors/' || $url_arr['path'] == '/view/layout/') {
		http_response_code(404);
		include ROOT_PATH . '/view/errors/page_404.php';
	
		die();
	} else {
		$urlarr = explode('/', $url_arr['path']);
		if (count($urlarr) > 2) {
	
			// array_shift($urlarr);
			unset($urlarr[1]);
			$view_url = implode('/', $urlarr);
	
			// Set page title
	
			$title_arr = array_filter($urlarr);
			define('PAGE_TITLE', ucfirst(end($title_arr)));
			
		}
	
		$view_content = preg_replace('{/$}', '.view.php', $view_url);
		$view_basename = basename($view_url, '.view.php');
	}