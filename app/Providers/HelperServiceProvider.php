<?php

namespace App\Providers;

use App\Helpers\OneSell;
use App\Helpers\VNPTDigiShop;
use App\Helpers\VNPTOneBss;
use Illuminate\Support\ServiceProvider;
use VXM\Async\Async;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use VXM\Async\Pool;

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
        $this->app->singleton('Async', function () {
            return new Async(new Pool, new EventDispatcher);
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
