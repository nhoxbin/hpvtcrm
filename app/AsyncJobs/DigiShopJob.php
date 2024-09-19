<?php

namespace App\AsyncJobs;

use Illuminate\Support\Facades\Artisan;

class DigiShopJob
{
    public function __invoke($job)
    {
        Artisan::call('queue:work', [
            '--queue' => $job,
            '--once' => true,
            '--tries' => 3,
            '--stop-when-empty' => true
        ]);
        return Artisan::output();
    }
}
