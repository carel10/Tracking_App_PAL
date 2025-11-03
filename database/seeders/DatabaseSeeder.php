<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear existing data
        // Truncate tables if they exist
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } catch (\Exception $e) {
            // If MySQL is not available, skip truncate
        }
        
        if (DB::getDriverName() === 'mysql') {
            User::truncate();
            Role::truncate();
            Division::truncate();
            Permission::truncate();
            DB::table('user_roles')->truncate();
            DB::table('role_permissions')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } else {
            // For SQLite or other databases
            User::query()->delete();
            Role::query()->delete();
            Division::query()->delete();
            Permission::query()->delete();
            DB::table('user_roles')->delete();
            DB::table('role_permissions')->delete();
        }

        // Create Divisions
        $divisions = [
            ['name' => 'IT Department', 'description' => 'Information Technology Division'],
            ['name' => 'HR Department', 'description' => 'Human Resources Division'],
            ['name' => 'Finance Department', 'description' => 'Finance and Accounting Division'],
        ];

        foreach ($divisions as $division) {
            Division::create($division);
        }

        // Create Permissions
        $permissions = [
            ['name' => 'View Users', 'module' => 'users', 'description' => 'Can view users list'],
            ['name' => 'Create Users', 'module' => 'users', 'description' => 'Can create new users'],
            ['name' => 'Edit Users', 'module' => 'users', 'description' => 'Can edit existing users'],
            ['name' => 'Delete Users', 'module' => 'users', 'description' => 'Can delete users'],
            ['name' => 'View Roles', 'module' => 'roles', 'description' => 'Can view roles list'],
            ['name' => 'Create Roles', 'module' => 'roles', 'description' => 'Can create new roles'],
            ['name' => 'Edit Roles', 'module' => 'roles', 'description' => 'Can edit existing roles'],
            ['name' => 'Delete Roles', 'module' => 'roles', 'description' => 'Can delete roles'],
            ['name' => 'View Permissions', 'module' => 'permissions', 'description' => 'Can view permissions list'],
            ['name' => 'Manage Settings', 'module' => 'settings', 'description' => 'Can manage system settings'],
            ['name' => 'View Audit Logs', 'module' => 'audit', 'description' => 'Can view audit logs'],
            ['name' => 'Manage Divisions', 'module' => 'divisions', 'description' => 'Can manage divisions'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create Roles
        $superAdminRole = Role::create([
            'name' => 'Super Admin',
            'hierarchy_level' => 0,
            'description' => 'Full system access with all permissions',
            'division_id' => null,
        ]);

        $adminRole = Role::create([
            'name' => 'Admin',
            'hierarchy_level' => 1,
            'description' => 'Administrative access with limited permissions',
            'division_id' => null,
        ]);

        $userRole = Role::create([
            'name' => 'User',
            'hierarchy_level' => 2,
            'description' => 'Standard user with basic permissions',
            'division_id' => null,
        ]);

        // Assign all permissions to Super Admin
        $superAdminRole->permissions()->attach(Permission::pluck('id'));

        // Assign limited permissions to Admin
        $adminPermissions = Permission::whereIn('name', [
            'View Users', 'Create Users', 'Edit Users',
            'View Roles', 'View Permissions',
            'Manage Divisions'
        ])->pluck('id');
        $adminRole->permissions()->attach($adminPermissions);

        // Assign basic permissions to User
        $userPermissions = Permission::whereIn('name', [
            'View Users', 'View Roles', 'View Permissions'
        ])->pluck('id');
        $userRole->permissions()->attach($userPermissions);

        // Create Users
        $superAdmin = User::create([
            'full_name' => 'Super Administrator',
            'email' => 'admin@trackingapp.com',
            'password_hash' => Hash::make('Admin123!'),
            'division_id' => Division::where('name', 'IT Department')->first()->id,
            'status' => 'active',
        ]);

        $admin = User::create([
            'full_name' => 'Administrator',
            'email' => 'admin@example.com',
            'password_hash' => Hash::make('Admin123!'),
            'division_id' => Division::where('name', 'HR Department')->first()->id,
            'status' => 'active',
        ]);

        $regularUser = User::create([
            'full_name' => 'John Doe',
            'email' => 'user@example.com',
            'password_hash' => Hash::make('User123!'),
            'division_id' => Division::where('name', 'Finance Department')->first()->id,
            'status' => 'active',
        ]);

        // Assign roles to users
        $superAdmin->roles()->attach($superAdminRole->id);
        $admin->roles()->attach($adminRole->id);
        $regularUser->roles()->attach($userRole->id);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Default users created:');
        $this->command->info('1. Super Admin - admin@trackingapp.com / Admin123!');
        $this->command->info('2. Admin - admin@example.com / Admin123!');
        $this->command->info('3. User - user@example.com / User123!');
    }
}

