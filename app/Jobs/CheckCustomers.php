<?php

namespace App\Jobs;

use App\Events\DigiShopUnauth;
use App\Helpers\Facades\VNPTDigiShop;
use App\Http\Mixins\HttpMixin;
use App\Models\DigiShopAccount;
use App\Models\DigiShopCustomer;
use App\Models\OneBssAccount;
use Exception;
use Generator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\Pool;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckCustomers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private DigiShopAccount|OneBssAccount $account, private $customers)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->account instanceof DigiShopAccount) {
            $this->digishop();
        }
    }

    private function digishop()
    {
        $account = $this->account;
        $customers = $this->customers;
        $upsert = [];
        $delete = [];
        Http::mixin(new HttpMixin());
        Http::concurrent(
            count($this->customers),
            function (Pool $pool) use ($customers, $account): Generator {
                foreach ($customers as $customer) {
                    yield $pool
                        ->async()
                        ->withToken($account->access_token)
                        ->withHeaders(['x-api-key' => config('digishop.apiKey')])
                        ->post(config('digishop.endpoint') . '/customer/get-info?phone_number=' . $customer['phone_number'])
                        ->then(fn($response) => [$customer['phone_number'], $response->json()]);
                }
            },
            function ($resp) use (&$upsert, &$delete, &$account) {
                $phone_number = $resp[0];
                $info = $resp[1];
                if (!empty($info) && $info['success'] && $info['statusCode'] == 200) { //  && now() <= now()->createFromFormat('Y-m-d', '2024-05-13')
                    $data = $info['data'];
                    if ($data['errorCode'] == 0) {
                        if ($data['errorCode'] == 401) {
                            event(new DigiShopUnauth($account));
                            $account = DigiShopAccount::find($account->id);
                            Log::info("Unauth");
                        } else {
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
                            if (empty($data['items'])) {
                                // $delete[] = $phone_number;
                                Log::info($phone_number);
                            } else {
                                $top_5 = $data['items'][0] ?? []; // top 5
                                $integration = $data['items'][1] ?? []; // tích hợp
                                $long_period = $data['items'][2] ?? []; // chu kì dài
                                if (!empty($integration)) {
                                    $insert['integration'] = json_encode(array_column($integration['list_product'], 'name'));
                                }
                                if (!empty($long_period)) {
                                    $insert['long_period'] = json_encode(array_column($long_period['list_product'], 'name'));
                                }
                                if (isset($top_5['list_product'])) {
                                    $insert['first_product_name'] = $top_5['list_product'][0]['name'];
                                }
                                if (!empty($data['detail'])) {
                                    $insert['tkc'] = $data['detail']['tkc'];
                                    if (!empty($data['detail']['packages'])) {
                                        $insert['packages'] = json_encode($data['detail']['packages']);
                                    }
                                }
                            }
                            $upsert[$phone_number] = $insert;
                        }
                    } else {
                        if ($data['errorCode'] == 3) {
                            // 'errorMessage' => 'Bạn đã vượt quá số lần tra cứu gói cước của thuê bao trong ngày. Vui lòng thực hiện tra cứu vào ngày mai. Xin cảm ơn.',
                            Log::info($data['errorMessage']);
                        } else {
                            Log::info('CheckCustomers Job: data');
                            Log::info($data);
                        }
                    }
                } else {
                    Log::info('CheckCustomers Job: info');
                    Log::info($phone_number);
                    Log::info($info);
                }
            }
        );

        $numberOfAttempts = 5;
        DB::transaction(function () use ($upsert) {
            DigiShopCustomer::upsert($upsert, ['phone_number', 'user_id'], ['tkc', 'first_product_name', 'packages', 'integration', 'long_period', 'is_request']);
        }, $numberOfAttempts);

        DB::transaction(function () use ($delete, $account) {
            if (!empty($delete)) {
                $account->customers()->whereIn('phone_number', $delete)->delete();
            }
        }, $numberOfAttempts);
    }
}
