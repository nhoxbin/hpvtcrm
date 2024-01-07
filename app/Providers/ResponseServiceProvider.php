<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Response::macro('success', function (string $message, array $data = []) {
            return Response::make(['msg' => $message, 'data' => $data]);
        });

        Response::macro('error', function (string $message, int $errorCode = 500, array $data = []) {
            return Response::make(['msg' => $message, 'data' => $data])->setStatusCode($errorCode);
        });
    }
}
