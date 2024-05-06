<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // log db queries
        if (!config('app.debug')) {
            $logFile = storage_path('logs/db/laravel.log');
            $custom_log = new Logger('log');
            $custom_log->pushHandler(
                $handler = new RotatingFileHandler($logFile)
            );
            $getDefaultFormatter = function () {
                return new LineFormatter(null, 'H:i d/m/Y', true, true);
            };
            $handler->setFormatter($getDefaultFormatter());
            $custom_log->info(request()->fullUrl());
            DB::listen(function($query) use ($custom_log) {
                $addSlashes = str_replace('?', "'?'", $query->sql);
                $queries = vsprintf(str_replace('?', '%s', $addSlashes), $query->bindings);
                $custom_log->info('[SQL EXEC]', [
                    "sql"  => $queries,
                    "time" => $query->time,
                ]);
            });
        }

        // notify when the query took too long DB::whenQueryingForLongerThan(miliseconds, callback)
        /* DB::whenQueryingForLongerThan(500, function (Connection $connection, QueryExecuted $event) {
            // Notify development team...
        }); */
    }
}
