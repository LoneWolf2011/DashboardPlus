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
	'menu_side' 		=> '/View/Layout/sidenav.layout.php',
	'menu_top' 			=> '/View/Layout/topnav.layout.php',
	'menu_footer' 		=> '/View/Layout/footer.layout.php',
		
	// Src
	'common' 			=> '/Src/Config/common.php',
	'modal' 			=> '/Src/Templates/modal.tpl.php',

	// Libs
	'LIBS'			=> array (	
		'phpmailer' 		=> '/Src/libs/PHPMailer/PHPMailerAutoload.php',
		'purifier' 			=> '/Src/libs/HTMLpurifier/HTMLPurifier.auto.php',
		'tcpdf' 			=> '/Src/libs/TCPDF/tcpdf.php',
	),
	
	// Helper functions
	'FUNC'			=> array (
		'error_handler' 	=> '/Src/Config/error_handler.php',
		'functions' 		=> '/Src/functions.php',
	),
	 
	// Classes
	'CLASS'			=> 	getClasses(ROOT_PATH.'/src/classes/')
));

define('ROOT_CSS', array(
	'/css/pe-icons/pe-icon-7-stroke.css',
	'/css/pe-icons/helper.css',
	'/css/stroke-icons/stroke-style.css',
	'/css/bootstrap.min.css',
	'/fonts/font-awesome/css/font-awesome.css',
	'/css/animate.css',
	'/css/style-dark.css',
	'/css/dash_custom-dark.css',
	'/css/plugins/dataTables/datatables.min.css',
	'/css/plugins/dataTables/datatables_responsive.min.css',
	'/css/plugins/iCheck/custom.css',
	'/css/plugins/formvalidation/dist/css/formValidation.min.css',
	'/css/plugins/c3/c3.min.css',
	'/css/plugins/sweetalert/sweetalert.css',
	'/css/plugins/datepicker/datepicker3.css',
	'/css/plugins/dualListbox/bootstrap-duallistbox.min.css',
));

define('ROOT_JS', array(
	'/js/jquery-3.1.1.min.js',
	'/js/bootstrap.min.js',
	'/js/plugins/metisMenu/jquery.metisMenu.js',
	'/js/plugins/slimscroll/jquery.slimscroll.min.js',
	'/js/main.js',
	'/js/plugins/pace/pace.min.js',
	'/js/plugins/markerclusterer/src/markerclusterer.js',
	'/js/plugins/i18next/i18next.min.js',
	'/js/plugins/iCheck/icheck.min.js',
	'/js/plugins/formvalidation/dist/js/formValidation.min.js',
	'/js/plugins/formvalidation/dist/js/framework/bootstrap.min.js',
	'/js/plugins/formvalidation/dist/js/language/'.strtolower(APP_LANG).'_'.strtoupper(APP_LANG).'.js',
	'/js/plugins/chartJs/Chart.min.js',
	'/js/plugins/echarts/dist/echarts.min.js',
	'/js/plugins/echarts/map/js/world.js',
	'/js/plugins/peity/jquery.peity.min.js',
	'/js/plugins/sweetalert/sweetalert.min.js',
	'/js/plugins/zxcvbn/zxcvbn.js',
	'/js/plugins/autocomplete/dist/jquery.autocomplete.min.js',
	'/js/plugins/datepicker/bootstrap-datepicker.js',
	'/js/plugins/datepicker/locales/bootstrap-datepicker.nl.js',
	'/js/plugins/dualListbox/jquery.bootstrap-duallistbox.js',
));