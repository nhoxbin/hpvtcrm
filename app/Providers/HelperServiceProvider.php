<?php

namespace App\Providers;

use App\Helpers\OneSell;
use App\Helpers\VNPTDigiShop;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('OneSell',function(){
            return new OneSell();
        });
        $this->app->singleton('VNPTDigiShop',function(){
            return new VNPTDigiShop();
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
