<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'stores.view', 'stores.manage',
            'users.view', 'users.manage',
            'roles.manage',
            'products.view', 'products.manage',
            'categories.manage', 'brands.manage',
            'suppliers.view', 'suppliers.manage',
            'purchases.view', 'purchases.manage', 'purchases.approve', 'purchases.pay',
            'sales.view', 'sales.create', 'sales.void', 'sales.return',
            'pos.access',
            'inventory.view', 'inventory.adjust', 'inventory.transfer',
            'employees.view', 'employees.manage',
            'attendance.manage', 'leave.manage',
            'payroll.view', 'payroll.manage', 'payroll.approve', 'payroll.pay',
            'expenses.view', 'expenses.manage',
            'reports.view', 'reports.all',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        $superAdmin = Role::findOrCreate('Super Admin');
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::findOrCreate('Admin');
        $admin->givePermissionTo([
            'users.view', 'users.manage', 'roles.manage',
            'products.view', 'products.manage', 'categories.manage', 'brands.manage',
            'suppliers.view', 'suppliers.manage',
            'purchases.view', 'purchases.manage', 'purchases.approve', 'purchases.pay',
            'sales.view', 'sales.create', 'sales.void', 'sales.return', 'pos.access',
            'inventory.view', 'inventory.adjust', 'inventory.transfer',
            'employees.view', 'employees.manage', 'attendance.manage', 'leave.manage',
            'payroll.view', 'payroll.manage', 'payroll.approve', 'payroll.pay',
            'expenses.view', 'expenses.manage', 'reports.view',
        ]);

        $manager = Role::findOrCreate('Manager');
        $manager->givePermissionTo([
            'products.view', 'products.manage',
            'suppliers.view', 'suppliers.manage',
            'purchases.view', 'purchases.manage', 'purchases.pay',
            'sales.view', 'sales.create', 'sales.return', 'pos.access',
            'inventory.view', 'inventory.adjust',
            'employees.view', 'employees.manage', 'attendance.manage', 'leave.manage',
            'payroll.view', 'expenses.view', 'reports.view',
        ]);

        $cashier = Role::findOrCreate('Cashier');
        $cashier->givePermissionTo([
            'sales.view', 'sales.create', 'pos.access', 'products.view',
        ]);

        $employee = Role::findOrCreate('Employee');
        $employee->givePermissionTo(['attendance.manage']);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
