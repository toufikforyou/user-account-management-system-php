<?php

namespace Services\Apis;

use Services\Models\Common\ApiHandlerModel;

class ApiController extends ApiHandlerModel
{
    public function sendEmailCallApi(string $url, array $data): string
    {
        return $this->apiResponseModel('POST', $url, $data);
    }
}