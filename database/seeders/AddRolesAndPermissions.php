<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddRolesAndPermissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'god',
            'guard_name' => 'api'
        ]);
        Role::create([
            'name' => 'admin',
            'guard_name' => 'api'
        ]);
        Role::create([
            'name' => 'user',
            'guard_name' => 'api'
        ]);

        Permission::create([
            'name' => 'impersonate',
            'guard_name' => 'api',
        ]);

        Permission::create([
            'name' => 'revoke-impersonate',
            'guard_name' => 'api',
        ]);
    }
}
