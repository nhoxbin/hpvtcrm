<?php

namespace App\Providers;

use App\AsyncJobs\DigiShopJob;
use App\Helpers\OneSell;
use App\Helpers\VNPTDigiShop;
use App\Helpers\VNPTOneBss;
use Illuminate\Support\ServiceProvider;
use VXM\Async\Async;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('OneSell', function () {
            return new OneSell();
        });
        $this->app->singleton('VNPTDigiShop', function () {
            return new VNPTDigiShop();
        });
        $this->app->singleton('VNPTOneBss', function () {
            return new VNPTOneBss();
        });
        $this->app->singleton('VNPTOneBss', function () {
            return new VNPTOneBss();
        });
        $this->app->bindMethod(DigiShopJob::class . '@handle', function ($job, $app) {
            return $job->handle($app->make());
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
