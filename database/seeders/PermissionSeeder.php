<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('model_has_permissions')->truncate();
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    	DB::table('permissions')->insert([
            [
                'name' => 'Read DigiShop',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'name' => 'Write DigiShop',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'name' => 'Edit DigiShop',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'name' => 'Delete DigiShop',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'name' => 'Read OneBss',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'name' => 'Write OneBss',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'name' => 'Edit OneBss',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ], [
                'name' => 'Delete OneBss',
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        User::firstWhere('username', 'tymcrm')->givePermissionTo(['Read DigiShop', 'Write DigiShop', 'Edit DigiShop', 'Delete DigiShop']);
        User::firstWhere('username', 'hpvt')->givePermissionTo(['Read OneBss', 'Write OneBss', 'Edit OneBss', 'Delete OneBss']);
        User::firstWhere('username', 'nhoxbin')->givePermissionTo(['Read DigiShop', 'Write DigiShop', 'Edit DigiShop', 'Delete DigiShop', 'Read OneBss', 'Write OneBss', 'Edit OneBss', 'Delete OneBss']);
    }
}
