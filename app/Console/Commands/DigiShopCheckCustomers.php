<?php

namespace App\Console\Commands;

use App\Helpers\Facades\VNPTDigiShop;
use App\Http\Mixins\HttpMixin;
use App\Models\DigiShopAccount;
use App\Models\DigiShopCustomer;
use Generator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

class DigiShopCheckCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:digishop-check-customers';

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
        /* Http::mixin(new HttpMixin());
        Http::concurrent(
            $concurrent,
            function (Pool $pool) use ($customers, $token): Generator {
                foreach ($customers as $customer) {
                    yield $pool->async()->withToken($token)->withHeaders(['x-api-key' => config('digishop.apiKey')])->post(config('digishop.endpoint') . '/customer/get-info?' . http_build_query(['phone_number' => $phone_number]))->then(fn($response) => [$customer->phone, $response->json()]);
                }
            },
            function ($info) use (&$upsert, &$delete, $account) {
                if ($account->access_token == null) return;
                if ($info[1]['error_code'] == 'BSS-00000000') {
                    $this->info('Processing: ' . $info[0]);
                    $data = $info[1]['data'];
                    if (!empty($data['GOI_CUOC_TS'])) {
                        $upsert[$data['SO_TB']] = [
                            'phone' => $data['SO_TB'],
                            'tra_sau' => (string) $data['TRA_SAU'],
                            'goi_data' => json_encode($data['GOI_CUOC_TS']),
                            'core_balance' => 0,
                            'is_request' => 1,
                        ];
                    } else {
                        $delete[] = $info[0];
                    }
                } elseif ($info[1]['error_code'] == 'BSS-0000420') {
                    $delete[] = $info[0];
                } elseif ($info[1]['error_code'] == 'BSS-00000401') {
                    OneBssClearAuth::dispatch($account);
                }
            }
        ); */
        $usernameCheck = 'hpvt';
        $customers = DigiShopCustomer::whereRelation('user', 'username', $usernameCheck)->where('is_request', false)->limit(200)->get();
        $digishop = DigiShopAccount::whereRelation('user', 'username', $usernameCheck)->where('status', true)->firstOrFail();
        foreach ($customers as $customer) {
            $info = VNPTDigiShop::getInfo($customer->phone_number, $digishop->access_token);
            if (!empty($info) && $info['success'] && $info['statusCode'] == 200) { //  && now() <= now()->createFromFormat('Y-m-d', '2024-05-13')
                $data = $info['data'];
                if ($data['errorCode'] == 0) {
                    $top_5 = $data['items'][0] ?? []; // top 5
                    $integration = $data['items'][1] ?? []; // tích hợp
                    $long_period = $data['items'][2] ?? []; // chu kì dài
                    $insert = [
                        'tkc' => 0,
                        'first_product_name' => null,
                        'packages' => null,
                        'integration' => null,
                        'long_period' => null,
                        'is_request' => true,
                    ];
                    if (!empty($integration)) {
                        $insert['integration'] = array_column($integration['list_product'], 'name');
                    }
                    if (!empty($long_period)) {
                        $insert['long_period'] = array_column($long_period['list_product'], 'name');
                    }
                    if (!empty($data['items']) && isset($top_5['list_product'])) {
                        $insert['first_product_name'] = $top_5['list_product'][0]['name'];
                    }
                    if (!empty($data['detail'])) {
                        $insert['tkc'] = $data['detail']['tkc'];
                        if (!empty($data['detail']['packages'])) {
                            $insert['packages'] = $data['detail']['packages'][0];
                        }
                    }
                    DigiShopCustomer::updateOrCreate(['phone_number' => $customer->phone_number], $insert);
                    // return response()->success('Success', $insert);
                }
            } else {
                Log::info($info);
            }
        }
        $this->info('Success');
    }
}
