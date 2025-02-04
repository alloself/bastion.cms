<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;


class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'root']);
        $permission = Permission::create(['name' => 'root']);

        $user = Role::create(['name' => 'user']);
        $userPermission = Permission::create(['name' => 'user']);
    }
}
