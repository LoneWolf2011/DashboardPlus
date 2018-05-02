<?php
	$year 		= date("Y");
	$today		= date("Y-m-d");
	$filename 	= '../../src/logs/'.$year.'/'.$today.".log"; 
	


	$file = file($filename);
	echo '<pre style="height: 520px; overflow: auto;">';
		foreach($file as $text) {
			echo $text;
		}  
	echo "</pre>";	
