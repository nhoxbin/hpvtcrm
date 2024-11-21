<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use VXM\Async\AsyncFacade as Async;

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
        DB::table('jobs')->where('queue', 'like', 'DigiShop%')->orWhere('queue', 'like', 'OneBss%')->orderBy('id', 'asc')->chunk(config('async.concurrency'), function ($jobs) {
            foreach ($jobs as $job) {
                Artisan::call('queue:work', [
                    '--queue' => $job->queue,
                    '--once' => true,
                    '--tries' => 5,
                    '--timeout' => 3600,
                    '--stop-when-empty' => true,
                ]);
                /* Async::run(function () use ($job) {
                    return Artisan::output();
                }, [
                    'success' => function ($output) {
                        // Log::info($output);
                    },
                    'error' => function (\Throwable $exception) {
                        // Log::info($exception->getMessage());
                    },
                ]); */
            }
            // Async::wait();
        });
    }
}
