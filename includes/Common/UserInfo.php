<?php
namespace Includes\Common;

use Services\Controllers\Common\AccountCheckCntr;

class UserInfo
{
    public static function getUserId(?string $username, ?string $email, ?int $mobile): int
    {
        $userCheck = new AccountCheckCntr();

        return $userCheck->getUserId($username, $email, $mobile);
    }
   

    
}