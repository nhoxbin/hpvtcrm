<?php

namespace App\Helpers;

use Ixudra\Curl\Facades\Curl;

class VNPTDigiShop
{
    public function login(array $credentials)
    {
        return Curl::to(config('digishop.endpoint') . '/user/login?' . http_build_query($credentials))
            ->withHeaders(['x-api-key' => config('digishop.apiKey')])
            // ->withContentType('application/json')
            ->asJson(true)
            ->post();
    }

    public function getInfo(string $phone_number, string $access_token)
    {
        return Curl::to(config('digishop.endpoint') . '/customer/get-info?' . http_build_query(['phone_number' => $phone_number]))
            ->withHeaders(['x-api-key' => config('digishop.apiKey')])
            ->withBearer($access_token)
            ->asJson(true)
            ->post();
    }
}
