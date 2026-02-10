<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'view dashboard']);
        Permission::firstOrCreate(['name' => 'view analyses']);
        Permission::firstOrCreate(['name' => 'view all analyses']);

        Permission::firstOrCreate(['name' => 'manage documents']);
        Permission::firstOrCreate(['name' => 'manage analyses']);
        Permission::firstOrCreate(['name' => 'manage all analyses']);
        Permission::firstOrCreate(['name' => 'manage users']);
        Permission::firstOrCreate(['name' => 'manage all users']);
        Permission::firstOrCreate(['name' => 'manage universities']);
        Permission::firstOrCreate(['name' => 'manage roles and permissions']);
        Permission::firstOrCreate(['name' => 'manage subscriptions']);
        Permission::firstOrCreate(['name' => 'manage credits']);

        Permission::firstOrCreate(['name' => 'analyze documents']);
        Permission::firstOrCreate(['name' => 'view reports']);
        Permission::firstOrCreate(['name' => 'export data']);
        Permission::firstOrCreate(['name' => 'manage settings']);
        Permission::firstOrCreate(['name' => 'view settings']);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        $userRole->givePermissionTo([
            'view dashboard',
            'view analyses',
            'analyze documents',
            'view reports',
            'export data',
        ]);
        $adminRole->givePermissionTo([
            'view dashboard',
            'view analyses',
            'analyze documents',
            'view reports',
            'export data',
            'manage documents',
            'manage analyses',
            'manage users',
            'manage universities',
            'manage roles and permissions',
            'manage subscriptions',
        ]);
        $superAdminRole->givePermissionTo(Permission::all());
    }
}
