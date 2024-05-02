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

    public function checkSession(string $access_token) {
        $notifications = Curl::to(config('onebss.endpoint') . '/app-thicong/common/kiemTraTacNghiep')
            ->withHeaders(['app-secret' => config('onebss.app_secret')])
            ->withBearer($access_token)
            ->asJson(true)
            ->get();

        $is_login = false;
        if ($notifications && $notifications['error'] == "BSS-00000000" && $notifications['error_code'] == "BSS-00000000") {
            $data = $notifications['data'];
            if ($data['errorCode'] == 0) {
                $is_login = true;
            }
        }
        return $is_login;
    }
}
