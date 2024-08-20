<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('model_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('permissions')->truncate();
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $permissions = [
            // digishop
            'Read DigiShop',
            'Write DigiShop',
            'Edit DigiShop',
            'Delete DigiShop',
            // onebss
            'Login OneBss',
            'Import Excel OneBss',
            'Export Excel OneBss',
            'View Customer OneBss',
            'Delete Customer OneBss',
        ];
        $insertPermissions = [];
        foreach ($permissions as $permission) {
            $insertPermissions[] = [
                'name' => $permission,
                'guard_name' => 'web',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
    	DB::table('permissions')->insert($insertPermissions);

        $roles = [
            'Super Admin',
            'Admin',
            'Sales',
            'OneBss Admin',
            'OneBss Sales',
        ];
        $insertRoles = [];
        foreach ($roles as $role) {
            $insertRoles[] = [
                'name' => $role,
            ];
        }
    	DB::table('roles')->insert($insertRoles);
        // roles
        Role::firstWhere('name', 'OneBss Admin')->givePermissionTo(['Login OneBss', 'Import Excel OneBss', 'Export Excel OneBss', 'View Customer OneBss', 'Delete Customer OneBss']);
        Role::firstWhere('name', 'OneBss Sales')->givePermissionTo(['View Customer OneBss']);

        // users
        User::firstWhere('username', 'tymcrm')->syncRoles(['Admin']);
        User::firstWhere('username', 'tymcrm')->givePermissionTo(['Read DigiShop', 'Write DigiShop', 'Edit DigiShop', 'Delete DigiShop']);
        User::firstWhere('username', 'nhoxbin')->givePermissionTo(['Read DigiShop', 'Write DigiShop', 'Edit DigiShop', 'Delete DigiShop']);

        User::firstWhere('username', 'hpvt')->syncRoles(['Admin', 'OneBss Admin']);
        User::firstWhere('username', 'hpvt')->givePermissionTo(['Read DigiShop', 'Write DigiShop', 'Edit DigiShop', 'Delete DigiShop']);
        User::firstWhere('username', 'nhoxbin')->syncRoles(['Super Admin', 'Admin', 'OneBss Admin']);
    }
}
