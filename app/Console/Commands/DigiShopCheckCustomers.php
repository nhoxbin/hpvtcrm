<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
        $jobs = DB::table('jobs')->where('queue', 'like', 'DigiShop%')->limit(30)->get();
        foreach ($jobs as $job) {
            $artisanPath = base_path('artisan');
            $logPath = storage_path('logs/AsyncWorkers.log');
            $commandString = "/usr/local/bin/ea-php81 $artisanPath queue:work --queue={$job->queue} --once --tries=3 --stop-when-empty > $logPath 2>&1 &";
            exec($commandString, $output);
            Log::info($output);
            sleep(1);
        }
    }
}
