<?php
namespace Includes\Common;

use Services\Controllers\Common\AccountCheckCntr;

class UserCheck
{

    public static function checkUser(?string $username, ?string $email, ?int $mobile):bool
    {
        $userCheck = new AccountCheckCntr();

        return $userCheck->checkUserExits($username, $email, $mobile);
    }
}