<?php

namespace App\Console\Commands;

use App\Helpers\Facades\VNPTDigiShop;
use App\Http\Mixins\HttpMixin;
use App\Models\DigiShopAccount;
use App\Models\DigiShopCustomer;
use App\Models\OneBssCustomer;
use App\Models\User;
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

        $concurrent = 5000;
        $usernameCheck = 'hpvt';
        $user = User::where('username', $usernameCheck)->with([
            'digishop_accounts' => function ($q) {
                $q->where('status', true);
            },
            'digishop_customers' => function ($q) use ($concurrent) {
                $q->where('is_request', false)->limit($concurrent);
            }
        ])->firstOrFail();
        $customers = $user->digishop_customers;
        $account = $user->digishop_accounts[0];
        $upsert = [];
        $delete = [];
        Http::mixin(new HttpMixin());
        Http::concurrent(
            $concurrent,
            function (Pool $pool) use ($customers, $account): Generator {
                foreach ($customers as $customer) {
                    yield $pool
                        ->async()
                        ->withToken($account->access_token)
                        ->withHeaders(['x-api-key' => config('digishop.apiKey')])
                        ->post(config('digishop.endpoint') . '/customer/get-info?phone_number=' . $customer->phone_number)
                        ->then(fn($response) => [$customer->phone_number, $response->json()]);
                }
            },
            function ($resp) use (&$upsert, &$delete, $account) {
                $this->info('hihi');
                $phone_number = $resp[0];
                $info = $resp[1];
                if (!empty($info) && $info['success'] && $info['statusCode'] == 200) { //  && now() <= now()->createFromFormat('Y-m-d', '2024-05-13')
                    $data = $info['data'];
                    if ($data['errorCode'] == 0) {
                        $top_5 = $data['items'][0] ?? []; // top 5
                        $integration = $data['items'][1] ?? []; // tích hợp
                        $long_period = $data['items'][2] ?? []; // chu kì dài
                        $insert = [
                            'phone_number' => $phone_number,
                            'tkc' => 0,
                            'user_id' => $account->user_id,
                            'first_product_name' => null,
                            'packages' => null,
                            'integration' => null,
                            'long_period' => null,
                            'is_request' => true,
                        ];
                        if (!empty($integration)) {
                            $insert['integration'] = json_encode(array_column($integration['list_product'], 'name'));
                        }
                        if (!empty($long_period)) {
                            $insert['long_period'] = json_encode(array_column($long_period['list_product'], 'name'));
                        }
                        if (!empty($data['items']) && isset($top_5['list_product'])) {
                            $insert['first_product_name'] = $top_5['list_product'][0]['name'];
                        }
                        if (!empty($data['detail'])) {
                            $insert['tkc'] = $data['detail']['tkc'];
                            if (!empty($data['detail']['packages'])) {
                                $insert['packages'] = json_encode($data['detail']['packages']);
                            }
                        }
                        $upsert[$phone_number] = $insert;
                    } else {
                        if ($data['errorCode'] == 3) {
                            /* array (
                                'errorCode' => 3,
                                'errorMessage' => 'Bạn đã vượt quá số lần tra cứu gói cước của thuê bao trong ngày. Vui lòng thực hiện tra cứu vào ngày mai. Xin cảm ơn.',
                            ), */
                        } else {
                            Log::info($info);
                        }
                    }
                } else {
                    Log::info($info);
                }
                // $this->call('app:check-digishop-session');
            }
        );
        DigiShopCustomer::upsert($upsert, ['phone_number'], ['tkc', 'first_product_name', 'packages', 'integration', 'long_period', 'is_request']);
        $this->info('Success');
    }
}
