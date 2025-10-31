<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->delete();

        $users = [
            ['name' => 'Nhoxbin', 'username' => 'nhoxbin',],
            ['name' => 'Tiến Tý', 'username' => 'tymcrm',],
            ['name' => 'Hùng', 'username' => 'hpvt',],
        ];
        foreach ($users as $user) {
            User::factory()->create([
                'name' => $user['name'],
                'username' => $user['username'],
            ]);
        }
    }
}
