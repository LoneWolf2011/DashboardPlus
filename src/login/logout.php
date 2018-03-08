<!--Made by: Roelof Jan van Golen | RGO | 2016-->
<?php

	if (isset($_GET['csrf']) && hash_equals($_GET['csrf'],$_SESSION['token'])) 
	{
		// Verwijderd de cookie(s) aan de client side
		if(isset($_COOKIE['modal'])){
			unset($_COOKIE['modal']);
			setcookie('modal', '', time() -3600, "/"); // -3600 = 1 uur geleden.
		} 
	
		
		// If we want to keep some session information such as shopping cart contents,
		// we only remove the user's data from the session without unsetting remaining
		// session variables and without destroying the session.
		
		// Log to file
		$msg = "Logout success. User: ".$_SESSION['user']['user_email'];
		logToFile(__FILE__,0,$msg);
				
		unset($_SESSION['user']);
		unset($_SESSION['token']);
	
		// Otherwise, we unset all of the session variables.
		$_SESSION = array();
	
		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}
	
		// Finally, destroy the session.
		session_destroy();
		
		//$session->destroy($id);
		// Whether we destroy the session or not, we redirect them to the login page
		header("Location: ".URL_ROOT);
		die("Redirecting to: ".URL_ROOT);
	} else {
		// Log to file
		$msg = "CSRF token invalid during logout for user: ". $_SESSION['user']['user_email'];
		logToFile(__FILE__,0,$msg);
		header("Location: ".URL_ROOT);
		die("Redirecting to: ".URL_ROOT);		
	}
	