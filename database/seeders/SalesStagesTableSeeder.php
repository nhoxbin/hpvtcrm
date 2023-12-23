<?php

use Illuminate\Database\Seeder;

class SalesStagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sales_stages')->delete();

        $sales_stages = [
    		'Không nghe máy',
    		'Thuê bao',
    		'Bận hẹn gọi lại',
    		'Đã đăng ký',
    		'Đã gọi',
    	];
		$db = [];
    	foreach ($sales_stages as $sales_stage) {
			array_push($db, ['name' => $sales_stage]);
		}
		DB::table('sales_stages')->insert($db);
    }
}
