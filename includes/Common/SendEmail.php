<?php
namespace Includes\Common;

use Services\Apis\ApiController;

class SendEmail
{
    public static function SendEmailToApi(array $data): void
    {
        $obj = new ApiController;

        $result = $obj->sendEmailCallApi('https://notify.sopnolikhi.com/v1/send/email', $data);

        $apiRes = json_decode($result, true);

        if ($apiRes['code'] === 1000) {
            http_response_code(200);
        } else {
            http_response_code(400);
        }
    }
}