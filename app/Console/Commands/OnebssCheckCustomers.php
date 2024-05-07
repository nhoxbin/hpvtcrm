<?php

namespace App\Console\Commands;

use App\Http\Mixins\HttpMixin;
use App\Models\OneBssAccount;
use App\Models\OneBssCustomer;
use Generator;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

class OnebssCheckCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:onebss-check-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $account = OneBssAccount::whereNotNull('access_token')->first();
        if ($account != null) {
            $token = $account->access_token;
            $concurrent = 20;
            $customers = OneBssCustomer::whereNull('goi_data')->where('is_request', 0)->get();
            $upsert = [];

            Http::mixin(new HttpMixin());
            $start = microtime(true);
            Http::concurrent(
                $concurrent,
                function (Pool $pool) use ($customers, $token): Generator {
                    foreach ($customers as $customer) {
                        yield $pool->async()->withHeader('app-secret', config('onebss.app_secret'))->withToken($token)->post(config('onebss.endpoint') . '/ccbs/oneBss/app_tb_tc_thongtin', ['so_tb' => $customer->phone, 'service' => 'SIM4G'])->then(fn($response) => $response->json());
                    }
                },
                function ($info) use (&$upsert) {
                    if ($info['error_code'] == 'BSS-00000000') {
                        $data = $info['data'];
                        $goi_data = null;
                        if ($data['TRA_SAU'] == 0) {
                            $goi_data = $data['GOI_CUOC'];
                        } else {
                            $goi_data = $data['GOI_DATA'];
                        }
                        $upsert[$data['SO_TB']] = [
                            'phone' => $data['SO_TB'],
                            'tra_sau' => (string) $data['TRA_SAU'],
                            'goi_data' => !empty($goi_data) ? json_encode($goi_data) : null,
                            'core_balance' => 0,
                            'is_request' => 1,
                        ];
                    }
                }
            );

            Http::concurrent(
                $concurrent,
                function (Pool $pool) use ($customers, $token): Generator {
                    foreach ($customers as $customer) {
                        yield $pool->async()->withHeaders(['app-secret' => config('onebss.app_secret')])->withToken($token)->post(config('onebss.endpoint') . '/ccbs/didong/taikhoan-tien', ['so_tb' => $customer->phone])->then(fn($response) => [$customer->phone, $response->json()]);
                    }
                },
                function ($balance) use (&$upsert) {
                    if ($balance[1]['error_code'] == 'BSS-00000000') {
                        $data = $balance[1]['data'];
                        $key = array_search('1', array_column($data, 'ID'));
                        $upsert[$balance[0]]['core_balance'] = $data[$key]['REMAIN'];
                    }
                }
            );
            $this->info(microtime(true) - $start);
            $upsert = array_values($upsert);
            OneBssCustomer::upsert($upsert, ['phone'], ['tra_sau', 'goi_data', 'core_balance', 'is_request']);
            $this->info('DONE');
        }
    }
}
