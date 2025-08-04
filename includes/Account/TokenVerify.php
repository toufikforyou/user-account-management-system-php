<?php

namespace Includes\Account;

use Includes\Common\ReturnSmg;
use Includes\Common\Validation;
use Services\Controllers\TokenVerifyCntr;

class TokenVerify extends TokenVerifyCntr
{
    private int $uid;
    private string $token;

    public function __construct(int $uid, string $token, $param)
    {
        $this->uid = $uid;
        $this->token = $token;
    }

    public function userTokenVerify(): string
    {
        if (!Validation::validateUid($this->uid) && !is_string($this->token)) {
            http_response_code(400);

            return ReturnSmg::return(1050, false, '700: User Token is invalid!');
        }

        return $this->tokenVerifyProcess($this->uid, $this->token);
    }
}
