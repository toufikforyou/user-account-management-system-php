<?php
namespace Controllers\Request;

use Includes\Account\SendOtp;
use Includes\Account\VerifyOtp;
use Includes\Common\ReturnSmg;

class Otp
{
    public static function sendOtp($param): void
    {
        // Retrieve the JSON data from the request body
        $json_data = file_get_contents('php://input');

        // Parse the JSON data into an associative array
        $data = json_decode($json_data, true);

        if (empty($data['account']))
        {
            http_response_code(400);

            echo ReturnSmg::return(1050, false, '707: Please enter either an email or a mobile number!');
            exit();
        }

        $sendOtp = new SendOtp($data);

        echo $sendOtp->sendOtpProcess();
    }

    public static function verifyOtp($param): void
    {
        // Retrieve the JSON data from the request body
        $json_data = file_get_contents('php://input');

        // Parse the JSON data into an associative array
        $data = json_decode($json_data, true);

        if (empty($data['account']) || empty($data['otp']))
        { 
            http_response_code(400);

            echo ReturnSmg::return(1050, false, '708: Please enter either an account and otp!');
            exit();
        }

        $verifyOtp = new VerifyOtp($data);

        echo $verifyOtp->verifyOtpProcess();
    }
}