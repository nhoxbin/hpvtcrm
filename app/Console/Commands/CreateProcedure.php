<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateProcedure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-procedure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $procedure = "
            CREATE PROCEDURE `process_customers`(IN vnptTable varchar(64), IN userId INT)
            BEGIN
                IF vnptTable = \"onebss\" THEN
                    (SELECT (SELECT COUNT(*) FROM one_bss_customers WHERE user_id=userId) total, (SELECT COUNT(*) FROM one_bss_customers WHERE user_id=userId and is_request=1) processing);
                ELSEIF vnptTable = \"digishop\" THEN
                    (SELECT (SELECT COUNT(*) FROM digi_shop_customers WHERE user_id=userId) total, (SELECT COUNT(*) FROM digi_shop_customers WHERE user_id=userId and is_request=1) processing);
                END IF;
            END
        ";

        DB::unprepared("DROP procedure IF EXISTS process_customers");
        DB::unprepared($procedure);
        $this->info('DONE');
    }
}
