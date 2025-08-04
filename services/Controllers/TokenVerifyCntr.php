<?php

namespace Services\Controllers;

use Includes\Common\ReturnSmg;
use Services\Models\TokenVerifyModel;

class TokenVerifyCntr extends TokenVerifyModel
{
    protected function tokenVerifyProcess(int $uid, string $token): string
    {
        $result = $this->tokenVerify($uid, $token);

        if ($result['success'] !== null && $result['success'] === 1) {
            // This result for success response
            // Response success message
            http_response_code(200);

            return ReturnSmg::return(1000, true, $result['result']);
        }

        // All error handler

        switch ($result['error']) {
            case 1:
                http_response_code(400);

                $errorResponse = ReturnSmg::return(1052, false, '702: ' . $result['result']);
                break;
            case -1:
                http_response_code(500);

                $errorResponse = ReturnSmg::return(1053, false, '701: ' . $result['result']);
                break;
            default:
                http_response_code(500);

                $errorResponse = ReturnSmg::return(1053, false, '702: ' . $result['result']);
        }

        return $errorResponse;
    }
}
