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
            ], [
                'name' => 'Thuê bao',
            ], [
                'name' => 'Bận hẹn gọi lại',
            ], [
                'name' => 'Đã đăng ký',
            ], [
                'name' => 'Đã gọi',
            ]
        ]);
    }
}
