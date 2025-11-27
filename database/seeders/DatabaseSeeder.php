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
    public function run(): void
    {
        /* ======================================================
         * RESET TABLES
         * ====================================================== */
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        } catch (\Exception $e) {}

        if (DB::getDriverName() === 'mysql') {
            DB::table('user_roles')->truncate();
            DB::table('role_permissions')->truncate();
            User::truncate();
            Role::truncate();
            Division::truncate();
            Permission::truncate();
        } else {
            DB::table('user_roles')->delete();
            DB::table('role_permissions')->delete();
            User::query()->delete();
            Role::query()->delete();
            Division::query()->delete();
            Permission::query()->delete();
        }

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Exception $e) {}


        /* ======================================================
         * CREATE DEFAULT DIVISIONS (ONLY IT & HR)
         * ====================================================== */
        $baseDivisions = [
            ['name' => 'IT Department', 'description' => 'Information Technology Division'],
            ['name' => 'HR Department', 'description' => 'Human Resources Division'],
        ];

        foreach ($baseDivisions as $d) {
            Division::firstOrCreate(['name' => $d['name']], $d);
        }


        /* ======================================================
         * PERMISSIONS – COMPLETE STRUCTURE
         * ====================================================== */
        $permissions = [

            // Dashboard
            ['name' => 'View Dashboard', 'code' => 'dashboard.view', 'module' => 'dashboard'],

            // Procurement
            ['name' => 'View Procurement', 'code' => 'procurement.view', 'module' => 'procurement'],
            ['name' => 'Create Procurement', 'code' => 'procurement.create', 'module' => 'procurement'],
            ['name' => 'Update Procurement', 'code' => 'procurement.update', 'module' => 'procurement'],
            ['name' => 'Delete Procurement', 'code' => 'procurement.delete', 'module' => 'procurement'],
            ['name' => 'Request Approval', 'code' => 'procurement.approval-request', 'module' => 'procurement'],

            // Projects
            ['name' => 'View Projects', 'code' => 'projects.view', 'module' => 'projects'],
            ['name' => 'Update Projects', 'code' => 'projects.update', 'module' => 'projects'],
            ['name' => 'Assign Projects', 'code' => 'projects.assign', 'module' => 'projects'],

            // Vendor
            ['name' => 'View Vendors', 'code' => 'vendor.view', 'module' => 'vendor-management'],
            ['name' => 'Create Vendor', 'code' => 'vendor.create', 'module' => 'vendor-management'],
            ['name' => 'Update Vendor', 'code' => 'vendor.update', 'module' => 'vendor-management'],
            ['name' => 'Delete Vendor', 'code' => 'vendor.delete', 'module' => 'vendor-management'],

            // Payments
            ['name' => 'View Payments', 'code' => 'payments.view', 'module' => 'payments'],
            ['name' => 'Approve Payments', 'code' => 'payments.approve', 'module' => 'payments'],
            ['name' => 'Process Payments', 'code' => 'payments.process', 'module' => 'payments'],

            // Inspections
            ['name' => 'View Inspections', 'code' => 'inspections.view', 'module' => 'inspections'],
            ['name' => 'Create Inspection', 'code' => 'inspections.create', 'module' => 'inspections'],
            ['name' => 'Verify Inspection', 'code' => 'inspections.verify', 'module' => 'inspections'],

            // Approvals
            ['name' => 'View Approvals', 'code' => 'approvals.view', 'module' => 'approvals'],
            ['name' => 'Approve Pengadaan', 'code' => 'approvals.approve', 'module' => 'approvals'],
            ['name' => 'Reject Pengadaan', 'code' => 'approvals.reject', 'module' => 'approvals'],

            // Notifications
            ['name' => 'View Notifications', 'code' => 'notifications.view', 'module' => 'notifications'],

            // Users
            ['name' => 'View Users', 'code' => 'users.view', 'module' => 'users'],
            ['name' => 'Create Users', 'code' => 'users.create', 'module' => 'users'],
            ['name' => 'Update Users', 'code' => 'users.update', 'module' => 'users'],
            ['name' => 'Delete Users', 'code' => 'users.delete', 'module' => 'users'],

            // Roles
            ['name' => 'View Roles', 'code' => 'roles.view', 'module' => 'roles'],
            ['name' => 'Assign Roles', 'code' => 'roles.assign', 'module' => 'roles'],

            // Divisions
            ['name' => 'Manage Divisions', 'code' => 'divisions.manage', 'module' => 'divisions'],

            // Audit
            ['name' => 'View Audit Logs', 'code' => 'audit.view', 'module' => 'audit'],

            // Settings
            ['name' => 'Manage Settings', 'code' => 'settings.update', 'module' => 'settings'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['code' => $p['code']], $p);
        }


        /* ======================================================
         * DEFAULT ROLES
         * ====================================================== */
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin'], [
            'hierarchy_level' => 0,
            'description' => 'Full access',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'Admin'], [
            'hierarchy_level' => 1,
            'description' => 'System administrator',
        ]);

        $basicUserRole = Role::firstOrCreate(['name' => 'User'], [
            'hierarchy_level' => 2,
            'description' => 'Basic user',
        ]);


        /* Super Admin gets EVERYTHING */
        $superAdminRole->permissions()->syncWithoutDetaching(Permission::pluck('id'));


        /* Admin gets limited */
        $adminPerms = Permission::whereIn('code', [
            'users.view','users.create','users.update','users.delete',
            'roles.view','roles.assign',
            'divisions.manage',
            'audit.view'
        ])->pluck('id');

        $adminRole->permissions()->syncWithoutDetaching($adminPerms);


        /* ======================================================
         * PROJECT_PAL DIVISIONS
         * ====================================================== */
        $projectDivisions = [
            ['name' => 'Produksi', 'description' => 'Production'],
            ['name' => 'Desain', 'description' => 'Design'],
            ['name' => 'Supply Chain', 'description' => 'Supply Chain'],
            ['name' => 'Treasury', 'description' => 'Treasury'],
            ['name' => 'Accounting', 'description' => 'Accounting'],
            ['name' => 'Quality Assurance', 'description' => 'QA'],
            ['name' => 'Sekretaris Direksi', 'description' => 'Secretary'],
        ];

        foreach ($projectDivisions as $d) {
            Division::firstOrCreate(['name' => $d['name']], $d);
        }


        /* ======================================================
         * DIVISION ROLES
         * ====================================================== */
        $divisionRoles = [
            ['name' => 'produksi_staff', 'division' => 'Produksi', 'desc' => 'Procurement User'],
            ['name' => 'desain_staff', 'division' => 'Desain', 'desc' => 'Design Staff'],
            ['name' => 'supply_chain_staff', 'division' => 'Supply Chain', 'desc' => 'Supply Chain Staff'],
            ['name' => 'treasury_staff', 'division' => 'Treasury', 'desc' => 'Treasury Staff'],
            ['name' => 'accounting_staff', 'division' => 'Accounting', 'desc' => 'Accounting Staff'],
            ['name' => 'qa_staff', 'division' => 'Quality Assurance', 'desc' => 'QA Staff'],
            ['name' => 'sekretaris_staff', 'division' => 'Sekretaris Direksi', 'desc' => 'Secretary Staff'],
        ];

        foreach ($divisionRoles as $r) {
            $div = Division::where('name', $r['division'])->first();

            Role::firstOrCreate(['name' => $r['name']], [
                'hierarchy_level' => 3,
                'description' => $r['desc'],
                'division_id' => $div->id,
            ]);
        }


        /* ======================================================
         * PERMISSION MAPPING PER DIVISION
         * ====================================================== */
        $rolePermMap = [
            'produksi_staff' => [
                'dashboard.view',
                'procurement.view','procurement.create','procurement.update','procurement.approval-request',
            ],

            'desain_staff' => [
                'dashboard.view',
                'projects.view','projects.update','projects.assign'
            ],

            'supply_chain_staff' => [
                'dashboard.view',
                'projects.view',
                'vendor.view','vendor.create','vendor.update','vendor.delete'
            ],

            'treasury_staff' => [
                'dashboard.view',
                'payments.view','payments.process'
            ],

            'accounting_staff' => [
                'dashboard.view',
                'payments.view','payments.approve'
            ],

            'qa_staff' => [
                'dashboard.view',
                'inspections.view','inspections.create','inspections.verify'
            ],

            'sekretaris_staff' => [
                'dashboard.view',
                'approvals.view','approvals.approve','approvals.reject',
                'projects.view'
            ],
        ];

        foreach ($rolePermMap as $roleName => $perms) {
            $role = Role::where('name', $roleName)->first();
            $permIds = Permission::whereIn('code', $perms)->pluck('id');
            $role->permissions()->syncWithoutDetaching($permIds);
        }


        /* ======================================================
         * CREATE USERS PER DIVISION
         * ====================================================== */
        $defaultPassword = Hash::make('password');

        $divisionUsers = [
            ['name' => 'User Division', 'email' => 'user@pal.com', 'division' => 'Produksi', 'role' => 'produksi_staff'],
            ['name' => 'Desain Staff', 'email' => 'desain@pal.com', 'division' => 'Desain', 'role' => 'desain_staff'],
            ['name' => 'Supply Chain Manager', 'email' => 'supplychain@pal.com', 'division' => 'Supply Chain', 'role' => 'supply_chain_staff'],
            ['name' => 'Treasury Staff', 'email' => 'treasury@pal.com', 'division' => 'Treasury', 'role' => 'treasury_staff'],
            ['name' => 'Accounting Staff', 'email' => 'accounting@pal.com', 'division' => 'Accounting', 'role' => 'accounting_staff'],
            ['name' => 'QA Inspector', 'email' => 'qa@pal.com', 'division' => 'Quality Assurance', 'role' => 'qa_staff'],
            ['name' => 'Sekretaris Direksi', 'email' => 'sekretaris@pal.com', 'division' => 'Sekretaris Direksi', 'role' => 'sekretaris_staff'],
        ];

        foreach ($divisionUsers as $u) {
            $division = Division::where('name', $u['division'])->first();

            $user = User::firstOrCreate(['email' => $u['email']], [
                'full_name' => $u['name'],
                'password_hash' => $defaultPassword,
                'division_id' => $division->id,
                'status' => 'active',
            ]);

            $role = Role::where('name', $u['role'])->first();
            $user->roles()->syncWithoutDetaching([$role->id]);
        }


        /* ======================================================
         * SUPER ADMIN + ADMIN
         * ====================================================== */
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@trackingapp.com'],
            [
                'full_name' => 'Super Administrator',
                'password_hash' => Hash::make('Admin123!'),
                'division_id' => Division::where('name', 'IT Department')->first()->id,
                'status' => 'active',
            ]
        );
        $superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'full_name' => 'Administrator',
                'password_hash' => Hash::make('Admin123!'),
                'division_id' => Division::where('name', 'HR Department')->first()->id,
                'status' => 'active',
            ]
        );
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);


        /* ======================================================
         * DONE
         * ====================================================== */
        $this->command->info("Tracking_App_PAL Database seeded successfully (FINANCE & JOHN DOE REMOVED)!");
    }
}
