<?php

declare(strict_types=1);

use Includes\Common\ReturnSmg;

date_default_timezone_set('Asia/Dhaka');

header("Content-type: application/json; charset=UTF-8");

header('Access-Control-Allow-Origin: *');

header('X-XSS-Protection: 1; mode=block');

header('X-Content-Type-Options: nosniff');

header('X-Frame-Options: DENY');

header('Access-Control-Allow-Headers: X-Auth-Token, Authorization');

$allowedMethods = ['GET', 'POST'];

// Check if the request method is allowed
if (!in_array($_SERVER['REQUEST_METHOD'], $allowedMethods)) {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: ' . implode(', ', $allowedMethods));

    echo json_encode([
        'code' => 1049,
        'success' => false,
        'result' =>  '703: Method not allowed',
        'language' => $_SERVER['HTTP_X_LANG'] ?? 'bn',
        'timestamp' => date("Y-m-d H:i:s", time())
    ]);

    exit();
}

// Retrieve the API key and API token from the request headers or query parameters
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? $_POST['api_key'];
$apiToken = $_SERVER['HTTP_X_API_TOKEN'] ?? $_POST['api_token'];

require 'vendor/autoload.php';

set_exception_handler("Controllers\ErrorHandler::handlerException");

$authentication = new Controllers\Authentication($apiKey, $apiToken);

// Check if the API key and API token are valid

if ($authentication->verifyToken() === false) {
    header('HTTP/1.0 401 Unauthorized');
    http_response_code(401);

    echo ReturnSmg::return(1049, false, '701: Unauthorized api');

    exit();
}


/*
    Route for the router
*/

$router = new Sources\Router();

// Define routes
$router->get('/', function () {
    http_response_code(200);

    echo ReturnSmg::return(1000, true, 'Api Home Page for version 1!');
});

// TODO:: Create account and login user route
$router->post('/account/check', 'Controllers\Request\Account::checkUser');
$router->post('/account/create', 'Controllers\Request\Account::createUser');
$router->post('/account/login', 'Controllers\Request\Account::logInUser');

// TODO: Check user token valid or not
$router->post('/account/login/verify', 'Controllers\Request\Account::tokenVerify');

//TODO:: Otp send and verify
$router->post('/account/otp/send', 'Controllers\Request\Otp::sendOtp');
$router->post('/account/otp/verify', 'Controllers\Request\Otp::verifyOtp');

// Set not found handler
$router->addNotFoundHandler(function () {
    http_response_code(404);

    echo ReturnSmg::return(1054, false, '701: 404 page not found!');

    exit();
});

// Run the router
$router->run();
