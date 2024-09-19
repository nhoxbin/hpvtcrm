<?php

namespace App\AsyncJobs;

use Illuminate\Support\Facades\Artisan;
use Spatie\Async\Task;
use Illuminate\Contracts\Console\Kernel;

class DigiShopJob extends Task
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private string $job) {}

    public function configure()
    {
        require __DIR__ . '/../../vendor/autoload.php';
        $app = require_once __DIR__ . '/../../bootstrap/app.php';
        $kernel = $app->make(Kernel::class);
        $kernel->handle(
            new \Symfony\Component\Console\Input\ArgvInput,
            new \Symfony\Component\Console\Output\NullOutput,
        );
    }

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
