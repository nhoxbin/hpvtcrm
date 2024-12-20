<?php

namespace App\Console\Commands;

use App\Events\OneBssUnauth;
use App\Http\Mixins\HttpMixin;
use App\Models\OneBssAccount;
use App\Models\OneBssCustomer;
use Generator;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        if (Cache::has('account')) {
            $account = Cache::get('account');
        } else {
            if ($account = OneBssAccount::whereNotNull('access_token')->latest()->first()) {
                $seconds = $account->expires_in - now()->subSeconds($account->expires_in)->diffInSeconds();
                if ($seconds > 0) {
                    Cache::remember('account', $seconds, function () use ($account) {
                        return $account;
                    });
                }
            }
        }
        if ($account) {
            $token = $account->access_token;
            $concurrent = 20;
            $customers = OneBssCustomer::withTrashed()->where('is_request', 0)->limit(6)->get();
            $upsert = [];
            $delete = [];

            Http::mixin(new HttpMixin());
            $start = microtime(true);
            Http::concurrent(
                $concurrent,
                function (Pool $pool) use ($customers, $token): Generator {
                    foreach ($customers as $customer) {
                        sleep(20);
                        yield $pool->async()->withToken($token)->post(config('onebss.endpoint') . '/ccbs/oneBss/app_tb_tc_thongtin', ['so_tb' => $customer->phone, 'service' => 'SIM4G'])->then(fn($response) => [$customer->phone, $response->json()]);
                    }
                },
                function ($info) use (&$upsert, &$delete, $account) {
                    if ($account->access_token == null) return;
                    if ($info[1]['error_code'] == 'BSS-00000000') {
                        $this->info('Processing: ' . $info[0]);
                        $data = $info[1]['data'];
                        if (!empty($data['GOI_CUOC_TS']) || !empty($data['GOI_CUOC']) || !empty($data['GOI_DATA'])) {
                            $upsert[$data['SO_TB']] = [
                                'phone' => $data['SO_TB'],
                                'tra_sau' => (string) $data['TRA_SAU'],
                                'goi_cuoc_ts' => json_encode($data['GOI_CUOC_TS']),
                                'goi_cuoc' => json_encode($data['GOI_CUOC']),
                                'goi_data' => json_encode($data['GOI_DATA']),
                                'core_balance' => 0,
                                'is_request' => 1,
                            ];
                            return;
                        }
                        $delete[] = $info[0];
                    } elseif ($info[1]['error_code'] == 'BSS-0000420') {
                        $delete[] = $info[0];
                    } elseif ($info[1]['error_code'] == 'BSS-00000401') {
                        event(new OneBssUnauth($account));
                    }
                }
            );
            OneBssCustomer::upsert($upsert, ['phone'], ['tra_sau', 'goi_cuoc_ts', 'goi_cuoc', 'goi_data', 'core_balance', 'is_request']);
            if (!empty($delete)) {
                OneBssCustomer::whereIn('phone', $delete)->forceDelete();
            }

            /* if ($account->access_token && !empty($upsert)) {
                Http::concurrent(
                    $concurrent,
                    function (Pool $pool) use ($customers, $token): Generator {
                        foreach ($customers as $customer) {
                            sleep(20);
                            yield $pool
                                ->async()
                                ->withToken($token)
                                ->post(config('onebss.endpoint') . '/ccbs/didong/taikhoan-tien', ['so_tb' => $customer->phone])
                                ->then(fn($response) => [$customer->phone, $response->json()]);
                        }
                    },
                    function ($balance) use (&$upsert) {
                        if ($balance[1]['error_code'] == 'BSS-00000000') {
                            $data = $balance[1]['data'];
                            $key = array_search('1', array_column($data, 'ID'));
                            if (isset($upsert[$balance[0]])) {
                                $upsert[$balance[0]]['core_balance'] = $data[$key]['REMAIN'];
                            }
                        } elseif ($balance[1]['error_code'] == 'BSS-00000500') {
                            if (isset($upsert[$balance[0]])) {
                                unset($upsert[$balance[0]]);
                            }
                        } else {
                            Log::info('OneBssCheckCustomers command');
                            Log::info($balance);
                        }
                    }
                );
                $this->info(microtime(true) - $start);
                OneBssCustomer::upsert($upsert, ['phone'], ['tra_sau', 'goi_data', 'core_balance', 'is_request']);
            } */

            $this->info('DONE');
        } else {
            $this->info('Access Token Expired!');
        }
    }
}
