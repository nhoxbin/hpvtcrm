<?php

namespace App\AsyncJobs;

use Spatie\Async\Task;

class DigiShopJob extends Task
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private string $job) {}

    public function configure() {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function run()
    {
        /* Artisan::call('queue:work', [
            '--queue' => $this->job,
            '--once' => true,
            '--tries' => 3,
            '--stop-when-empty' => true
        ]); */
        $artisanPath = '/home/hpvtnetb/public_html/artisan'; // base_path('artisan');
        $logPath = '/home/hpvtnetb/public_html/storage/logs/AsyncWorkers.log'; // storage_path('logs/AsyncWorkers.log');
        $commandString = "/usr/local/bin/ea-php81 $artisanPath queue:work --queue={$this->job} --once --tries=3 --stop-when-empty > $logPath 2>&1 &";
        exec($commandString);

        // return Artisan::output();
    }
}
