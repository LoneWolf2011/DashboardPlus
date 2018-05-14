<?php

    // Define admin only views
    $admin_only = array(
        'admin',
        'logging',
        'users',
        'settings'
    );
    // Main routing law
    $url = isset($_SERVER['PATH_INFO']) ? explode('/', ltrim($_SERVER['PATH_INFO'],'/')) : '/';

    if ($url == '/')
    {
        // This is the home page
        // Initiate the home controller
        // and render the home view
    } else {

        // This is not home page
        // Initiate the appropriate controller
        // and render the required view

        //The first element should be a controller
        $requestedController = $url[0];

        // If a second part is added in the URI,
        // it should be a method
        $requestedAction = isset($url[1]) ? $url[1] : '';

        // The remain parts are considered as
        // arguments of the method
        $requestedParams = array_slice($url, 2);

        // Check if controller exists. NB:
        // You have to do that for the model and the view too
        $ctrlPath = ROOT_PATH . '/view/' . $requestedController . '.view.php';
	
        if(!checkUserIsAdmin() && in_array($requestedController, $admin_only)) {

            http_response_code(403);
            include ROOT_PATH . '/view/errors/page_403.php';
            die();

        } elseif (file_exists($ctrlPath)) {

            $view_content = '/view/' . $requestedController . '.view.php';
            $view_basename = $requestedController;

        } else {

            http_response_code(404);
            include ROOT_PATH . '/view/errors/page_404.php';
            die();
            //require the 404 controller and initiate it
            //Display its view
        }
    }