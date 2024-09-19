<?php

namespace App\Console\Commands;

use App\AsyncJobs\DigiShopJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use VXM\Async\AsyncFacade;

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
        $collection = DB::table('jobs')->where('queue', 'like', 'DigiShop%')->limit(30);
        $jobs = $collection->get();
        // $pool = Pool::create()->withBinary('/usr/local/bin/ea-php81');
        $jobss = [];
        foreach ($jobs as $job) {
            $data = unserialize(json_decode($job->payload, true)['data']['command']);
            $account = $data->account;
            $customers = $data->customers;
            /* Async::run(function () use ($job) {
                Artisan::call('queue:work', [
                    '--queue' => $job->queue,
                    '--once' => true,
                    '--tries' => 3,
                    '--stop-when-empty' => true
                ]);
                return Artisan::output();
            }, [
                'success' => function ($output) {
                    Log::info($output);
                },
                'error' => function (\Throwable $exception) {
                    Log::info($exception->getMessage());
                },
            ]); */
            $jobss[] = new DigiShopJob($account, $customers);
        }
        AsyncFacade::batchRun(...$jobss);
        // $results = AsyncFacade::wait();
        // Log::info($results);
        // $jobs->delete();


        /* foreach ($jobs as $job) {
            $artisanPath = base_path('artisan');
            $logPath = storage_path('logs/AsyncWorkers.log');
            $commandString = "/usr/local/bin/ea-php81 $artisanPath queue:work --queue={$job->queue} --once --tries=3 --stop-when-empty > $logPath 2>&1 &";
            exec($commandString);
            sleep(1);
        } */
    }
}
