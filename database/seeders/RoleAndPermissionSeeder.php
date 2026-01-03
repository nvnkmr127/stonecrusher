<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleManager = Role::firstOrCreate(['name' => 'manager']);
        $roleAccountant = Role::firstOrCreate(['name' => 'accountant']);
        $roleUser = Role::firstOrCreate(['name' => 'user']);

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => Hash::make('password')]
        );
        $admin->assignRole($roleAdmin);

        // Create Manager User
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            ['name' => 'Manager User', 'password' => Hash::make('password')]
        );
        $manager->assignRole($roleManager);

        // Create Accountant User
        $accountant = User::firstOrCreate(
            ['email' => 'accountant@example.com'],
            ['name' => 'Accountant User', 'password' => Hash::make('password')]
        );
        // Assign Manager role to Accountant for now or create specific permissions? 
        // User requested "Give Permissions".
        // Use Case 3.7 said "Actor: Admin / Accountant / Manager".
        // I'll give Accountant the same permissions as Manager generally, or just assign Manager role?
        // No, User asked for "create all roles". I'll assign 'accountant' role.
        // BUT my route middleware is `role:admin|manager`.
        // I should update routes to include `accountant` OR assign `manager` role to the accountant user as well?
        // Better to update routes to `role:admin|manager|accountant`.
        $accountant->assignRole($roleAccountant);

        // Create Regular User
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'Regular User', 'password' => Hash::make('password')]
        );
        $user->assignRole($roleUser);
    }
}
