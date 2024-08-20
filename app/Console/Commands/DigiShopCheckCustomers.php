<?php

namespace App\Console\Commands;

use App\Helpers\Facades\VNPTDigiShop;
use App\Models\DigiShopAccount;
use App\Models\DigiShopCustomer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
