<?php

namespace App\Console\Commands;

use App\Helpers\Facades\VNPTDigiShop;
use App\Models\DigiShopAccount;
use Illuminate\Console\Command;

class CheckDigiShopSession extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-digishop-session';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Digishop Session';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $digishop = DigiShopAccount::where('status', 1)->latest()->first();
        if (!empty($digishop) && $digishop->status) {
            $is_login = VNPTDigiShop::checkSession($digishop->access_token);
            if (!$is_login) {
                $credentials = ['username' => $digishop->username, 'password' => $digishop->password];
                $login = VNPTDigiShop::login($credentials);
                if ($login['success'] && $login['statusCode'] == 200) {
                    $data = $login['data'];
                    if ($data['errorCode'] == 0) {
                        $item = $data['item'];
                        if (!empty($item) && $item['access_token']) {
                            $digishop->status = true;
                            $digishop->access_token = $item['access_token'];
                            $digishop->save();
                            return;
                        }
                    }
                }
                $digishop->status = false;
                $digishop->access_token = NULL;
                $digishop->save();
            }
        }
    }
}
