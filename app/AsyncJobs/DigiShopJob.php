<?php

namespace App\AsyncJobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use VXM\Async\Invocation;

class DigiShopJob
{

    use Invocation;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private string $job) {}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
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
