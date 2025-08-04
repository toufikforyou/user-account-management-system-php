<?php

namespace Services\Controllers;

use Includes\Common\ReturnSmg;
use Includes\Common\SendEmail;
use Includes\Common\Validation;
use Services\Models\OtpModel;

class OtpCntr extends OtpModel
{
    protected function sendOtpProcessing(string $fullName, string $account, ?array $details): string
    {
        // TODO:: Result for model class
        $otp = mt_rand(10000, 99999);
        $result = $this->sendingOtp($account, $otp, $details);

        if ($result['success'] !== null && $result['success'] === 1) {
            // This result for success response
            $response = $result['result'];

            // Send Email and sms for the otp send successfully
            if (Validation::validateEmail((string) $response['account'])) {
                // Send Email and sms for the 
                $this->sendEmail($fullName, $response['account'], $otp);
            }

            if (Validation::validateMobile($response['account'])) {
                // Send otp sms for the account
                $this->sendOtpMobileQuckly($fullName, Validation::formatPhoneNumber($response['account']), $otp);
            }


            // Response success message
            http_response_code(201);
            return ReturnSmg::return(1001, true, $response);
        }

        // All error handler

        switch ($result['error']) {
            case -1:
                http_response_code(500);

                $errorResponse = ReturnSmg::return(1053, false, '701: ' . $result['result']);
                break;
            default:
                http_response_code(500);

                $errorResponse = ReturnSmg::return(1053, false, '702: ' . $result['result']);
        }

        return $errorResponse;
    }



    protected function verifyOtpProcessing(string $account, int $otp, ?array $details): string
    {
        // TODO:: Result for model class
        $result = $this->verifyOtp($account, $otp, $details);

        if ($result['success'] !== null && $result['success'] === 1) {
            // This result for success response
            // Response success message
            http_response_code(200);
            return ReturnSmg::return(1000, true, $result['result']);
        }

        // All error handler

        switch ($result['error']) {
            case 1:
                http_response_code(400);

                $errorResponse = ReturnSmg::return(1052, false, '702: ' . $result['result']);
                break;
            case -1:
                http_response_code(500);

                $errorResponse = ReturnSmg::return(1053, false, '701: ' . $result['result']);
                break;
            default:
                http_response_code(500);

                $errorResponse = ReturnSmg::return(1053, false, '702: ' . $result['result']);
        }

        return $errorResponse;
    }



    private function sendEmail(string $name, string $email, int $otp): void
    {
        $fullName = $name ? $name : 'User';

        $time = date("d-m-Y h:i A", time());

        $html = "<!DOCTYPE html><html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>OTP Verification Code</title>
        </head>
        <body style='font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: white;'>
            <table style='max-width: 600px; margin: 0 auto; background-color: #f2f2f2;'>
                <tr>
                    <td style='border-bottom: 1px solid #007bff; padding: 10px;'>
                        <a href='https://sopnolikhi.com/?ref=account_verification_email' style='display: block; max-width: 100%;'>
                            <img src='https://sopnolikhi.com/images/icons/logo.png' alt='Logo' style='max-width: 100%; width: 50px;'>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 20px;'>
                        <h1 style='color: #007bff;'>OTP Verification Code</h1>
                        <p>Welcome User,</p>
                        <p>Thank you for choosing our service. To complete your account setup, we require you to verify your email address using the OTP (One Time Password) code provided below:</p>
                        <p style='background-color: white; padding: 10px; font-size: 24px; font-weight: bold; display: inline-block;'>{$otp}</p>
                        <p>Please enter this code in the verification field to confirm your email address. The OTP code is valid for only 5 minutes.</p>
                        <p>If you did not request this OTP, please ignore this email.</p>
                        <p>Best regards, <br/><strong>Account team</strong></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width='100%' style='padding: 10px 0; text-align: center;'>
                            <tr>
                                <td>
                                    <p><span style='font-weight: bold;'>&copy; 2024 SOPNO LIKHI LLC</span><br/>
                                        <span>$time</span>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>";





        $data = [
            'subject' => "Hi $fullName, Your OTP Code is - $otp",
            'from' => [
                'name' => 'SL OTP Verification',
                'email' => 'verification@sopnolikhi.com'
            ],
            'reply' => [
                'name' => 'no reply',
                'email' => 'no-reply@sopnolikhi.com'
            ],
            'to' => [
                [
                    'name' => $fullName,
                    'email' => $email
                ]
            ],
            'body' => $html
        ];

        SendEmail::SendEmailToApi($data);
    }

    private function sendOtpMobile(string $name, string $mobile, int $otp): void
    {
        $fullName = $name ? $name : 'User';

        // Set the API endpoint URL
        $url = 'https://mimsms.com.bd/smsAPI?sendsms';

        // Set your unique API Key and API Token
        $apiKey = 'gjODWTJMNBNaCGMvOfOxAlMcecfEiXuB';
        $apiToken = 'wavT1685613654';

        // Set the message details
        $type = 'sms';
        $from = '8809601004976';
        $to = $mobile;
        $text = 'Welcome to "Books My Friend"! Your otp code is S-' . $otp . '. Enter it in the app to complete registration. Discover books, connect with readers. Happy reading!';

        // Build the query parameters
        $data = array(
            'apikey' => $apiKey,
            'apitoken' => $apiToken,
            'type' => $type,
            'from' => $from,
            'to' => $to,
            'text' => $text
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        // not require
        // return $response;
    }

    private function sendOtpMobileQuckly(string $name, string $mobile, int $otp): void
    {
        $to = $mobile;
        $token = "9800102407169051824763d8b1a1fd74d56b50749d247473e7d1";

        $message = 'Welcome to "Books My Friend"! Your otp code is S-' . $otp . '. Enter it in the app to complete registration. Discover books, connect with readers. Happy reading!';

        $url = "http://api.greenweb.com.bd/api.php?json";


        $data = array(
            'to' => "$to",
            'message' => "$message",
            'token' => "$token"
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $smsresult = curl_exec($ch);

        // //Result
        // $smsresult;

        // //Error Display
        // curl_error($ch);
    }
}