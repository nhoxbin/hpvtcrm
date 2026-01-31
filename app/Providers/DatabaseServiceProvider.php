<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Level;
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
        if (config('database.log')) {
            // 1. Định nghĩa đường dẫn thư mục và file
            $logPath = storage_path('logs/db');
            $logFile = $logPath . '/laravel.log';

            // 2. Kiểm tra và tạo thư mục nếu chưa có
            if (!file_exists($logPath)) {
                // 0777 để đảm bảo cả root và www-data đều ghi được (trong môi trường dev/docker)
                mkdir($logPath, 0777, true);
            }

            $custom_log = new Logger('log');

            // 3. Cấu hình Handler với quyền file mở rộng (0666 hoặc 0777)
            // Tham số thứ 5 của RotatingFileHandler là $filePermission
            $handler = new RotatingFileHandler($logFile, 0, Level::fromName('DEBUG'), true, 0666);

            $getDefaultFormatter = function () {
                return new LineFormatter(null, 'H:i d/m/Y', true, true);
            };
            $handler->setFormatter($getDefaultFormatter());

            $custom_log->pushHandler($handler);

            // 4. Bọc request() trong try-catch để tránh lỗi khi chạy CLI
            try {
                $url = app()->runningInConsole() ? 'CLI Command' : request()->fullUrl();
                $custom_log->info($url);
            } catch (\Exception $e) {
                // Bỏ qua nếu không lấy được URL
            }

            DB::listen(function ($query) use ($custom_log) {
                $queries = str_replace('?', "'?'", $query->sql);
                if (!empty($query->bindings)) {
                    $queries = vsprintf(str_replace('?', '%s', $queries), $query->bindings);
                }
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
