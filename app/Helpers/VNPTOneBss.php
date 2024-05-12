<?php

namespace App\Helpers;

use App\Models\OneBssAccount;
use Ixudra\Curl\Facades\Curl;

class VNPTOneBss
{
    public function login(array $credentials)
    {
        $credentials['os_type'] = 1;
        return Curl::to(config('onebss.endpoint') . '/quantri/user/login')
            ->withData($credentials)
            ->asJson(true)
            ->post();
    }

    public function oauth(array $credentials)
    {
        $credentials["grant_type"] = "password";
        $credentials["client_id"] = "clientapp";
        $credentials["client_secret"] = "password";
        return Curl::to(config('onebss.endpoint') . '/quantri/oauth/token')
            ->withData($credentials)
            ->asJson(true)
            ->post();
    }

    public function getInfo(string $phone_number, string $access_token)
    {
        return Curl::to(config('onebss.endpoint') . '/customer/get-info?' . http_build_query(['phone_number' => $phone_number]))
            ->withHeaders(['x-api-key' => config('onebss.apiKey')])
            ->withBearer($access_token)
            ->asJson(true)
            ->post();
    }

    public function getBalance(string $phone_number, string $access_token)
    {
        return Curl::to(config('onebss.endpoint') . '/ccbs/didong/taikhoan-tien')
            ->withHeaders(['x-api-key' => config('onebss.apiKey')])
            ->withData(['so_tb' => $phone_number])
            ->withBearer($access_token)
            ->asJson(true)
            ->post();
    }
}
