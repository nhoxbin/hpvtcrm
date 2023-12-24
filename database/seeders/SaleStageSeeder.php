<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sale_stages')->delete();

		DB::table('sale_stages')->insert([
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
