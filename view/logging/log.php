<?php
	$year 		= date("Y");
	$today		= date("Y-m-d");
	$filename 	= URL_ROOT.'/src/logs/'.$year.'/'.$today.".log"; 
	
	$file = file($filename);
	
	echo '<pre style="height: 520px; overflow-x: hidden; overflow-y: auto;">';
		foreach($file as $text) {
			echo $text;
		}  
	echo "</pre>";	
