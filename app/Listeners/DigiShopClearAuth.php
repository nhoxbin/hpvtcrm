<?php

namespace App\Listeners;

use App\Events\DigiShopUnauth;
use App\Helpers\Facades\VNPTDigiShop;
use App\Models\DigiShopAccount;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DigiShopReAuth
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DigiShopUnauth $event): void
    {
        $digishop = VNPTDigiShop::login([
            'username' => $event->account->username,
            'password' => $event->account->password
        ]);
        if ($digishop['success'] && $digishop['statusCode'] == 200) {
            $data = $digishop['data'];
            if ($data['errorCode'] == 0) {
                $item = $data['item'];
                if (!empty($item) && $item['access_token']) {
                    $event->account->status = true;
                    $event->account->access_token = $item['access_token'];
                    $event->account->save();
                    return;
                }
            }
        }
        $event->account->status = false;
        $event->account->access_token = NULL;
        $event->account->save();
    }
}
