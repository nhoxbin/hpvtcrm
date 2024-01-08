<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalesStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sales_states')->delete();

		DB::table('sales_states')->insert([
            [
                'name' => 'Không nghe máy',
                'code' => 'not_answer',
            ], [
                'name' => 'Thuê bao',
                'code' => 'cannot_contacted',
            ], [
                'name' => 'Bận hẹn gọi lại',
                'code' => 'busy',
            ], [
                'name' => 'Đã đăng ký',
                'code' => 'registered',
            ], [
                'name' => 'Đã gọi',
                'code' => 'called',
            ]
        ]);
    }
}
