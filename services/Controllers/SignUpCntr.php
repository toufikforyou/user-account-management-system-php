<?php

namespace Services\Controllers;

use Includes\Common\ReturnSmg;
use Includes\Common\SendEmail;
use Includes\Common\Validation;
use Services\Controllers\Common\NotifyFinderCntr;
use Services\Models\SignUpModel;

class SignUpCntr extends SignUpModel
{
    protected function createAccount(array $user): string
    {
        // TODO:: Result for model class
        $result = $this->createUserAccount($user);

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
            http_response_code(201);
            return ReturnSmg::return(1001, true, $response);
        }

        // All error handler

        switch($result['error'])
        {
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
            <title>Account Created Successfully</title>
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
                        <h1 style='color: #007bff;'>Account Created</h1>
                        <p>Dear $fullName,</p>
                        <p>Congratulations! Your account with SOPNO LIKHI LLC has been successfully created.</p>
                        <p>You can now access your account and start enjoying our services.</p>
                        <p>If you have any questions or need assistance, please don't hesitate to contact us at support@sopnolikhi.com.</p>
                        <p>Thank you for choosing SOPNO LIKHI LLC.</p>
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
            'subject' => "Congratulations $name - Your account has been successfully created",
            'from' => [
                'name' => 'SOPNO LIKHI Account',
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