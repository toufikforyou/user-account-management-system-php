<?php

namespace Controllers\Request;

use Includes\Account\CheckAccount;
use Includes\Account\CreateAccount;
use Includes\Account\LogInAccount;
use Includes\Account\TokenVerify;
use Includes\Common\ReturnSmg;

class Account
{
    public static function tokenVerify($param): void
    {
        $uToken = (string) $_SERVER['HTTP_AUTHORIZATION'] ?? $param['api_auth_token'];
        $uid = (int) $_SERVER['HTTP_SOPNOLIKHI'] ?? $param['api_auth_uid'];

        if (empty($uToken) || empty($uid)) {
            http_response_code(400);

            echo ReturnSmg::return(1050, false, '708: User token is invalid!');

            exit();
        }

        $tokenVerify = new TokenVerify((int) $uid, (string) $uToken, $param);

        echo $tokenVerify->userTokenVerify();
    }

    public static function checkUser($param): void
    {
        // Retrieve the JSON data from the request body
        $json_data = file_get_contents('php://input');

        // Parse the JSON data into an associative array
        $data = json_decode($json_data, true);

        if (empty($data['account'])) {
            http_response_code(400);

            echo ReturnSmg::return(1050, false, '707: Please enter either an email or a mobile number and password!');
            exit();
        }

        $checkAccount = new CheckAccount($data);

        echo $checkAccount->checkAccountValid();
    }

    public static function createUser($param): void
    {
        // Retrieve the JSON data from the request body
        $json_data = file_get_contents('php://input');

        // Parse the JSON data into an associative array
        $data = json_decode($json_data, true);


        if (empty($data['account']) || empty($data['password'])) {
            http_response_code(400);

            echo ReturnSmg::return(1050, false, '707: Please enter either an email or a mobile number and password!');
            exit();
        }

        $create = new CreateAccount($data);

        echo $create->account();
    }

    public static function logInUser($param): void
    {
        // Retrieve the JSON data from the request body
        $json_data = file_get_contents('php://input');

        // Parse the JSON data into an associative array
        $data = json_decode($json_data, true);


        if (empty($data['account']) || empty($data['password'])) {
            http_response_code(400);

            echo ReturnSmg::return(1050, false, '708: Please enter either an email and password!');
            exit();
        }

        $logIn = new LogInAccount($data);

        echo $logIn->userLogIn();
    }
}
