<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Async\Pool;

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
        DB::table('jobs')->where('queue', 'like', 'DigiShop%')->orderBy('id', 'desc')->chunk(30, function ($jobs) {
            $pool = Pool::create()->withBinary('/usr/local/bin/ea-php81');;
            foreach ($jobs as $job) {
                $pool[] = async(function () use ($job) {
                    Artisan::call('queue:work', [
                        '--queue' => $job->queue,
                        '--once' => true,
                        '--tries' => 3,
                        '--stop-when-empty' => true
                    ]);
                    return Artisan::output();
                })->then(function ($output) {
                    Log::info($output);
                })->catch(function ($exception) {
                    Log::info($exception);
                });
            }
            await($pool);
        });
        /* foreach ($jobs as $job) {
            $artisanPath = base_path('artisan');
            $logPath = storage_path('logs/AsyncWorkers.log');
            $commandString = "/usr/local/bin/ea-php81 $artisanPath queue:work  --once --tries=3 --stop-when-empty > $logPath 2>&1 &";
            exec($commandString);
            sleep(1);
        } */
    }
}
