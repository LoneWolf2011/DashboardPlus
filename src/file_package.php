<?php
/*
================================================================================

	Name: Packages config file
	Functie: 
		- Bevat array met de absolute file locaties.	
			
	Version: 1.0.8
	Author:	Roelof Jan van Golen - <r.vangolen@asb.nl>

================================================================================
*/
define('ROOT_FILE', array(
	// Layout
	'menu_side' 		=> '/Mdb/View/Layout/sidenav.layout.php',
	'menu_top' 			=> '/Mdb/View/Layout/topnav.layout.php',
	'menu_footer' 		=> '/Mdb/View/Layout/footer.layout.php',
		
	// Src
	'common' 			=> '/Mdb/Src/Config/common.php',
	'modal' 			=> '/Mdb/Src/Templates/modal.tpl.php',

	// Libs
	'LIBS'			=> array (	
		'phpmailer' 		=> '/Mdb/Src/libs/PHPmailer/class.phpmailer.php',
		'tcpdf' 			=> '/Mdb/Src/libs/TCPDF/tcpdf.php',
	),
	
	// Helper functions
	'FUNC'			=> array (
		'error_handler' 	=> '/Mdb/Src/Config/error_handler.php',
		'functions' 		=> '/Mdb/Src/functions.php',
	),
	 
	// Classes
	'CLASS'			=> array (
		'safemysql' 		=> '/Mdb/Src/Classes/class.safemysql.php',
		'ssp_class' 		=> '/Mdb/Src/Classes/class.datatable.ssp.php',
		'google' 			=> '/Mdb/Src/Classes/class.googleHelper.php',
		'login' 			=> '/Mdb/Src/Classes/class.login.php',
		'location' 			=> '/Mdb/Src/Classes/class.location.php',
		'home' 				=> '/Mdb/Src/Classes/class.home.php',
		'tools' 			=> '/Mdb/Src/Classes/class.tools.php',	
	)		
));

define('ROOT_CSS', array(
	'/Mdb/css/bootstrap.min.css',
	'/Mdb/fonts/font-awesome/css/font-awesome.css',
	'/Mdb/css/animate.css',
	'/Mdb/css/style.css',
	'/Mdb/css/plugins/dataTables/datatables.min.css',
	'/Mdb/css/plugins/iCheck/custom.css',
	'/Mdb/css/plugins/formvalidation/dist/css/formValidation.min.css',
	'/Mdb/css/plugins/c3/c3.min.css',
	'/Mdb/css/plugins/sweetalert/sweetalert.css',
));

define('ROOT_JS', array(
	'/Mdb/js/jquery-3.1.1.min.js',
	'/Mdb/js/bootstrap.min.js',
	'/Mdb/js/plugins/metisMenu/jquery.metisMenu.js',
	'/Mdb/js/plugins/slimscroll/jquery.slimscroll.min.js',
	'/Mdb/js/main.js',
	'/Mdb/js/plugins/pace/pace.min.js',
	'/Mdb/js/plugins/markerclusterer/src/markerclusterer.js',
	'/Mdb/js/plugins/i18next/i18next.min.js',
	'/Mdb/js/plugins/iCheck/icheck.min.js',
	'/Mdb/js/plugins/formvalidation/dist/js/formValidation.min.js',
	'/Mdb/js/plugins/formvalidation/dist/js/framework/bootstrap.min.js',
	'/Mdb/js/plugins/formvalidation/dist/js/language/'.strtolower(APP_LANG).'_'.strtoupper(APP_LANG).'.js',
	'/Mdb/js/plugins/chartJs/Chart.min.js',
	'/Mdb/js/plugins/echarts/dist/echarts.min.js',
	'/Mdb/js/plugins/echarts/map/js/world.js',
	'/Mdb/js/plugins/peity/jquery.peity.min.js',
	'/Mdb/js/plugins/sweetalert/sweetalert.min.js',
));