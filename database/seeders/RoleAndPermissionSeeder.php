<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'sites.view',
            'sites.manage',
            'accounts.manage',
            'coa-mappings.manage',
            'periods.manage',
            'periods.lock',
            'pnl.view-own-site',
            'pnl.view-all-sites',
            'imports.create',
            'imports.manage',
            'journals.manage',
            'journals.approve',
            'pettycash.manage',
            'tax.manage',
            'reports.generate',
            'reports.approve',
            'reports.deliver',
            'users.manage',
            'roles.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $financeManager = Role::firstOrCreate(['name' => 'Finance Manager', 'guard_name' => 'web']);
        $siteAccountant = Role::firstOrCreate(['name' => 'Site Accountant', 'guard_name' => 'web']);
        $auditor = Role::firstOrCreate(['name' => 'Auditor', 'guard_name' => 'web']);

        $superAdmin->syncPermissions(Permission::all());

        $financeManager->syncPermissions([
            'sites.view',
            'accounts.manage',
            'coa-mappings.manage',
            'periods.manage',
            'pnl.view-all-sites',
            'imports.create',
            'imports.manage',
            'journals.manage',
            'journals.approve',
            'pettycash.manage',
            'tax.manage',
            'reports.generate',
            'reports.approve',
            'reports.deliver',
        ]);

        $siteAccountant->syncPermissions([
            'sites.view',
            'pnl.view-own-site',
            'imports.create',
            'journals.manage',
            'pettycash.manage',
            'reports.approve',
        ]);

        $auditor->syncPermissions([
            'sites.view',
            'pnl.view-all-sites',
        ]);

        $admin = User::updateOrCreate(
            ['email' => 'admin@arkaledger.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'dark_mode' => true,
                'is_active' => true,
            ]
        );
        $admin->syncRoles($superAdmin);

        $finance = User::updateOrCreate(
            ['email' => 'finance@arkaledger.test'],
            [
                'name' => 'Finance Manager',
                'password' => Hash::make('password'),
                'dark_mode' => true,
                'is_active' => true,
            ]
        );
        $finance->syncRoles($financeManager);
    }
}
