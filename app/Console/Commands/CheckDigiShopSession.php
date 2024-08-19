<?php

namespace App\Console\Commands;

use App\Helpers\Facades\VNPTDigiShop;
use App\Models\DigiShopAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
        $accounts = DigiShopAccount::where('status', true)->get();
        foreach ($accounts as $account) {
            if (!empty($account) && $account->status) {
                $is_login = VNPTDigiShop::checkSession($account->access_token);
                if (!$is_login) {
                    $credentials = ['username' => $account->username, 'password' => $account->password];
                    $login = VNPTDigiShop::login($credentials);
                    // Log::error($login);
                    if ($login['success'] && $login['statusCode'] == 200) {
                        $data = $login['data'];
                        if ($data['errorCode'] == 0) {
                            $item = $data['item'];
                            if (!empty($item) && $item['access_token']) {
                                $account->status = true;
                                $account->access_token = $item['access_token'];
                                $account->save();
                                return;
                            }
                        }
                    }
                    $account->status = false;
                    $account->access_token = NULL;
                    $account->save();
                    $this->info('Account ' . $account->username . ' has been updated.');
                }
            }
        }
    }
}
