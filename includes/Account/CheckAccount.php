<?php
namespace Includes\Account;

use Includes\Common\ReturnSmg;
use Includes\Common\UserCheck;
use Includes\Common\UserInfo;
use Includes\Common\Validation;
use Services\Controllers\Common\AccountInfoCntr;

class CheckAccount extends AccountInfoCntr
{
    private string $account;

    public function __construct($param)
    {
        $this->account = trim($param['account']);
    }

    public function checkAccountValid(): string
    {
        if(!Validation::validateEmail($this->account) && !Validation::validateMobile($this->account))
        {
            http_response_code(400);
            return ReturnSmg::return(1050, false, '701: Email address or mobile is invalid!');
        }

        if (UserCheck::checkUser($this->account, $this->account, Validation::formatPhoneNumber($this->account)))
        {
            http_response_code(400);

            return ReturnSmg::return(1054, false, '702: User not found!');
        }

        return $this->getUserDetails((int) UserInfo::getUserId((string) $this->account, (string) $this->account, (int) $this->account));
    }

    private function getUserDetails(int $uid): string
    {
        return $this->userInfoRequest($uid);       
    }
}