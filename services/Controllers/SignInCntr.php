<?php
namespace Services\Controllers;

use Includes\Common\ReturnSmg;
use Includes\Common\SendEmail;
use Includes\Common\Validation;
use Services\Controllers\Common\NotifyFinderCntr;
use Services\Models\SignInModel;

class SignInCntr extends SignInModel
{
    protected function signInRequest(int $uid, string $password, bool $remember, array $others): string
    {
        // TODO:: Result for model class
        $result = $this->signInAccount($uid, $password, $remember, $others);

        if($result['success'] !== null && $result['success'] === 1)
        {
            // This result for success response
            $response = $result['result'];

            $email = NotifyFinderCntr::getEmailId((int) $response['uid']);

            // Send Email and sms for the user account successfully login
            if(Validation::validateEmail($email))
            {
                // Send Email and sms for the user account successfully created
                $this->sendEmail($response['full-name'], $email);
            }



            // Response success message
            http_response_code(200);
            return ReturnSmg::return(1000, true, $response);
        }

        // All error handler

        switch($result['error'])
        {
            case 1:
                http_response_code(404);
                
                $errorResponse = ReturnSmg::return(1054, false, '702: '.$result['result']);
                break;
            case 2:
                http_response_code(400);
                
                $errorResponse = ReturnSmg::return(1052, false, '704: '.$result['result']);
                break;
            case 3:
                http_response_code(400);
                
                $errorResponse = ReturnSmg::return(1052, false, '703: '.$result['result']);
                break;
            case 4:
            case -1:
                http_response_code(500);
                
                $errorResponse = ReturnSmg::return(1053, false, '701: '.$result['result']);
                break;
            default:
                http_response_code(500);

                $errorResponse = ReturnSmg::return(1053, false, '702: '.$result['result']);
        }

        return $errorResponse;
    }

    

    private function sendSms(): void
    {
        
    }

    private function sendEmail(string $name, string $email): void
    {
        $fullName = $name ? $name : 'User';

        $time = date("d-m-Y h:i A", time());

        $html = "<!DOCTYPE html><html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Account Login Successful</title>
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
                        <h1 style='color: #007bff;'>Login Successful</h1>
                        <p>Dear $fullName,</p>
                        <p>Your login to your account at SOPNO LIKHI LLC was successful.</p>
                        <p>If you did not initiate this login, please change your password immediately and contact us.</p>
                        <p>If you have any questions or need assistance, please don't hesitate to contact us at support@sopnolikhi.com.</p>
                        <p>Thank you for using SOPNO LIKHI LLC.</p>
                        <br/>
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
            'subject' => "Successful Login to Your SOPNO LIKHI LLC Account",
            'from' => [
                'name' => 'SL Account login',
                'email' => 'account@sopnolikhi.com'
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

    private function sendNotification(): void
    {
        
    }
}