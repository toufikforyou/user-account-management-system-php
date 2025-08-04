<?php
namespace Includes\Account;

use Includes\Common\ReturnSmg;
use Includes\Common\Validation;
use Services\Controllers\OtpCntr;

class SendOtp extends OtpCntr
{
    private string $fullName;
    private string $account;

    private $locations;
    private $details;

    public function __construct($param)
    {
        $this->fullName = (string) trim($param['full-name']);
        $this->account = (string) trim($param['account']);

        $this->locations = $param['locations'];
        $this->details = $param['device-info'];
    }

    public function sendOtpProcess(): string
    {
        if(Validation::validateEmail($this->account))
        {
            
        }elseif(Validation::validateMobile($this->account))
        {
            $this->account = Validation::formatPhoneNumber($this->account);
        }else{
            http_response_code(400);
            return ReturnSmg::return(1050, false, '701: Account details is not correct!');
        }
        
        return $this->sendOtpRequest();
    }
    
    private function sendOtpRequest(): string
    {
        return $this->sendOtpProcessing($this->fullName, $this->account, $this->sendOtpArray());
    }

    private function sendOtpArray(): array
    {
        $this->details['ip-address'] = $_SERVER['REMOTE_ADDR'] ?? null;

        return array(
            'locations' => $this->locations,
            'device-info' => $this->details
        );
    }
}