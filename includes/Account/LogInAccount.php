<?php

namespace Includes\Account;

use Includes\Common\ReturnSmg;
use Includes\Common\UserInfo;
use Services\Controllers\SignInCntr;

class LogInAccount extends SignInCntr
{
    private $account;
    private string $password;
    private bool $remember;

    private $locations;
    private $details;
    
    public function __construct($param)
    {
        $this->account = (string) trim($param['account']);
        $this->password = (string) trim($param['password']);
        $this->remember = (bool) trim($param['remember']);   

        $this->locations = $param['locations'];

        $this->details = $param['device-info'];
    }
    
    public function userLogIn(): string
    {
        if(empty($this->account))
        {
            http_response_code(400);

            return ReturnSmg::return(1050, false, '707: Please enter your email, phone or username!');
        }

        if (empty($this->password))
        {
            http_response_code(400);

            return ReturnSmg::return(1050, false, '705: Password must be fild the box!');
        }

        return $this->singInProcess();
    }

    private function singInProcess(): string
    {
        $uid = (int) UserInfo::getUserId((string) $this->account, (string) $this->account, (int) $this->account);

        return $this->signInRequest($uid, md5(htmlspecialchars($this->password)), $this->remember, $this->logInRequestArray());
    }

    private function logInRequestArray(): array
    {
        $this->details['ip-address'] = $_SERVER['REMOTE_ADDR'] ?? null;

        return array(
            'locations' => $this->locations,
            'device-info' => $this->details
        );
    }
    
}