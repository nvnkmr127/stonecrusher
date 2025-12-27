<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AttendancePermissionSeeder extends Seeder
{
    public function run()
    {
        // Define permissions
        $permissions = [
            'attendance.view_any',
            'attendance.mark',
            'attendance.edit',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign to Admin
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo($permissions);

        // Assign to Manager
        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'attendance.view_any',
            'attendance.mark',
        ]);

        // Ensure User role exists but has no attendance permissions
        Role::firstOrCreate(['name' => 'user']);
    }
}
