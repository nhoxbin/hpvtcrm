<?php

namespace App\Helpers;

use App\Models\DigiShopAccount;
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

    public function getNotification(string $access_token)
    {
        return Curl::to(config('digishop.endpoint') . '/notification/count-notification')
            ->withHeaders(['x-api-key' => config('digishop.apiKey')])
            ->withBearer($access_token)
            ->asJson(true)
            ->post();
    }

    public function getInfo(string $phone_number, string $access_token)
    {
        return Curl::to(config('digishop.endpoint') . '/customer/get-info?phone_number=' . $phone_number)
            ->withHeaders(['x-api-key' => config('digishop.apiKey')])
            ->withBearer($access_token)
            ->asJson(true)
            ->post();
    }

    public function checkSession(string $access_token)
    {
        $notifications = Curl::to(config('digishop.endpoint') . '/notification/count-notification')
            ->withHeaders(['x-api-key' => config('digishop.apiKey')])
            ->withBearer($access_token)
            ->asJson(true)
            ->post();

        $is_login = false;
        if ($notifications && $notifications['success'] && $notifications['statusCode'] == 200) {
            $data = $notifications['data'];
            if ($data['errorCode'] == 0) {
                $is_login = true;
            }
        }
        return $is_login;
    }
}
