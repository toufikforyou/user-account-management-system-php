<?php
namespace Services\Controllers\Common;

use Includes\Common\Validation;
use Services\Models\Common\AccountCheckModel;

class AccountCheckCntr extends AccountCheckModel
{

    public function checkUserExits(?string $username, ?string $email, ?int $mobile): bool
    {
        if(
            is_numeric($this->getUserByPhone((int) Validation::formatPhoneNumber($mobile))) === false &&
            is_numeric($this->getUserByEmail($email)) === false &&
            is_numeric($this->getUserByUsername($username)) === false
        )
        {
            return true;        
        }
        return false;
    }

    public function getUserId(?string $username, ?string $email, ?int $mobile): int
    {
        if($this->getUserByPhone((int) Validation::formatPhoneNumber($mobile)) !== null)
        {
            return $this->getUserByPhone((int) Validation::formatPhoneNumber($mobile));
        }elseif($this->getUserByEmail($email) !== null)
        {
            return $this->getUserByEmail($email);
        }elseif($this->getUserByUsername($username) !== null)
        {
            return $this->getUserByUsername($username);
        }else
        {
            return 0;
        }
    }


}