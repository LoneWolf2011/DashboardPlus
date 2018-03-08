<!--Made by: Roelof Jan van Golen | RGO | 2016-->

<?php

    // At the top of the page we check to see whether the user is logged in or not 
    if(empty($_SESSION['user'])) 
    { 
        // If they are not, we redirect them to the login page. 
        header("Location: ../../"); 
         
        // Remember that this die statement is absolutely critical.  Without it, 
        // people can view your members-only content without logging in. 
        die("Redirecting to login.php"); 
    } 

	//Update Lastaccess kolom in users database
	$id 		= $_SESSION['user']['user_id'];
	$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

	if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
	}
	
    $sql = "UPDATE app_users SET user_last_access = now() WHERE user_id = '".$id."'";  
	
    if($conn->query($sql) === TRUE) {
	} else {
		echo $conn->error;
	}

	//Redirect naar juiste index pagina op basis van Userrole	
	$user_role 		= $_SESSION['user']['user_role'];
	
	if(APP_INITIALIZE === 0){
		header("Location: ".URL_ROOT."view/install.php"); 
	} elseif($user_role == 1){
		header("location: ".URL_ROOT."view/admin/");
	} elseif($user_role == 2) {
		header("location: ".URL_ROOT."view/home/");
	} else {
		header("location: ".URL_ROOT);
	}
 
