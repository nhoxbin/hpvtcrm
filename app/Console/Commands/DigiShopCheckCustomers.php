<?php

namespace App\Console\Commands;

use App\Events\DigiShopUnauth;
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
use Illuminate\Support\Facades\DB;
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
        $jobs = DB::table('jobs')->where('queue', 'like', 'DigiShop%')->limit(30)->get();
        foreach ($jobs as $job) {
            $artisanPath = base_path('artisan');
            $logPath = storage_path('logs/AsyncWorkers.log');
            $commandString = "/usr/local/bin/ea-php81 $artisanPath queue:work --queue={$job->queue} --memory=2048 --once --tries=3 --stop-when-empty > $logPath 2>&1 &";
            exec($commandString);
        }
    }
}
