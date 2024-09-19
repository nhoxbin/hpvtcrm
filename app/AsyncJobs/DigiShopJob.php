<?php

namespace App\AsyncJobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Spatie\Async\Task;
use VXM\Async\Invocation;

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
        Artisan::call('queue:work', [
            '--queue' => $this->job,
            '--once' => true,
            '--tries' => 3,
            '--stop-when-empty' => true
        ]);
        return Artisan::output();
    }
}
