<?php
namespace Services\Controllers\Common;

use Includes\Common\ReturnSmg;
use Services\Models\Common\AccountCheckModel;

class AccountInfoCntr extends AccountCheckModel
{
    protected function userInfoRequest(int $uid): string
    {
        // TODO:: Result for model class
        $result = $this->getUserInfo($uid);

        if($result['success'] !== null && $result['success'] === 1)
        {
            $user = $result['result']['0'];
            $gender = "Not Set";

            switch($user['GENDER'])
            {
                case 1: 
                    $gender = "Male";
                break;
                case 2: 
                    $gender = "Female";
                break;
                default:
                    $gender = "Not set";
            }
            
            $response = [
                'uid' => $user['UID'],
                'account' => $user['ACTIVE'],
                'full-name' => $user['FULLNAME'],
                'profile' => $user['PROFILE'],
                'mobile' => $user['MOBILE'],
                'email' => $user['EMAIL'],
                'username' => $user['USERNAME'],
                'gender' => $gender,
                'date-of-birth' => $user['DOB'],
                'date' => $user['DATE']  
            ];

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
}