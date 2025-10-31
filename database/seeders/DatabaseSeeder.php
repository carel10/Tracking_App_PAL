<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use App\Models\Permission;
use App\Models\UserActivityLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create Divisions
        $divisions = [
            ['division_name' => 'IT'],
            ['division_name' => 'HR'],
            ['division_name' => 'Finance'],
            ['division_name' => 'Operations']
        ];
        
        foreach ($divisions as $division) {
            Division::create($division);
        }

        // Create Roles
        $roles = [
            [
                'role_name' => 'Super Admin',
                'role_description' => 'Full system access'
            ],
            [
                'role_name' => 'Admin',
                'role_description' => 'Administrative access'
            ],
            [
                'role_name' => 'User',
                'role_description' => 'Standard user access'
            ]
        ];

        foreach ($roles as $role) {
            Role::create(array_merge($role, ['created_at' => now()]));
        }

        // Create Permissions
        $permissions = [
            [
                'permission_name' => 'View Dashboard',
                'permission_code' => 'dashboard.view',
                'category' => 'Dashboard'
            ],
            [
                'permission_name' => 'Manage Users',
                'permission_code' => 'users.manage',
                'category' => 'User Management'
            ],
            [
                'permission_name' => 'View Users',
                'permission_code' => 'users.view',
                'category' => 'User Management'
            ],
            [
                'permission_name' => 'Manage Roles',
                'permission_code' => 'roles.manage',
                'category' => 'Access Control'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::create(array_merge($permission, ['created_at' => now()]));
        }

        // Create Users
        $users = [
            [
                'username' => 'superadmin',
                'email' => 'superadmin@example.com',
                'full_name' => 'Super Administrator',
                'division_id' => 1, // IT Division
                'role_id' => 1, // Super Admin
                'status' => 'active',
                'password_hash' => Hash::make('password123')
            ],
            [
                'username' => 'admin',
                'email' => 'admin@example.com',
                'full_name' => 'Administrator',
                'division_id' => 2, // HR Division
                'role_id' => 2, // Admin
                'status' => 'active',
                'password_hash' => Hash::make('password123')
            ],
            [
                'username' => 'user',
                'email' => 'user@example.com',
                'full_name' => 'Regular User',
                'division_id' => 3, // Finance Division
                'role_id' => 3, // User
                'status' => 'active',
                'password_hash' => Hash::make('password123')
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        // Create some activity logs
        $activities = [
            [
                'user_id' => 1,
                'activity' => 'Logged in to the system',
                'timestamp' => now(),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ],
            [
                'user_id' => 2,
                'activity' => 'Updated user profile',
                'timestamp' => now()->subHours(1),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ],
            [
                'user_id' => 3,
                'activity' => 'Viewed dashboard',
                'timestamp' => now()->subHours(2),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ];

        foreach ($activities as $activity) {
            UserActivityLog::create($activity);
        }
    }
}
