<?php
namespace Includes\Account;

use Includes\Common\ReturnSmg;
use Includes\Common\Validation;
use Services\Controllers\OtpCntr;

class VerifyOtp extends OtpCntr
{
    private string $account;
    private int $otp;

    private $locations;
    private $details;

    public function __construct($param)
    {
        $this->account = (string) trim($param['account']);
        $this->otp = (int) trim($param['otp']);
    }

    public function verifyOtpProcess(): string
    {
        if(Validation::validateEmail($this->account))
        {
            
        }elseif(Validation::validateMobile($this->account))
        {
            $this->account = Validation::formatPhoneNumber($this->account);
        }else
        {
            http_response_code(400);
    
            return ReturnSmg::return(1050, false, '701: Account details is invalid!');
        }

        return $this->verifyOtpRequest();
    }
    
    private function verifyOtpRequest(): string
    {
        return $this->verifyOtpProcessing($this->account, $this->otp, $this->sendOtpArray());
    }

    private function sendOtpArray(): array
    {
        return array(
            'locations' => $this->locations,
            'details' => [
                'device-info' => $this->details,
                'ip-address' => $_SERVER['REMOTE_ADDR'] ?? null
            ]
        );
    }
}