<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('success', function ($message, $data = []) {
            return Response::make(['msg' => $message, 'data' => $data]);
        });

        Response::macro('error', function ($message, $data = []) {
            return Response::make(['msg' => $message, 'data' => $data]);
        });
    }
}
