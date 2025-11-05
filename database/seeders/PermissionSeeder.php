<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إعادة تعيين الأدوار والصلاحيات المخزنة مؤقتاً (cache)
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- تعريف الحارس ---
        $guardName = 'api';

        // قائمة الصلاحيات التي سيتم إنشاؤها
        $permissions = [
            'dashboard.view',
            'user.view', 'user.create', 'user.update', 'user.delete',
            'role.view', 'role.create', 'role.update', 'role.delete',
            'company.view', 'company.create', 'company.update', 'company.delete',
            'station.view', 'station.create', 'station.update', 'station.delete',
            'driver.view', 'driver.create', 'driver.update', 'driver.delete',
            'truck.view', 'truck.create', 'truck.update', 'truck.delete',
            'product.view', 'product.create', 'product.update', 'product.delete',
            'region.view', 'region.create', 'region.update', 'region.delete',
            'fuel_order.view', 'fuel_order.create', 'fuel_order.update', 'fuel_order.delete',
            'setting.view', 'setting.update',
            'order_status.view', 'order_status.create', 'order_status.update', 'order_status.delete',
        ];

        // إنشاء الصلاحيات مع تحديد الحارس
        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => $guardName,
            ]);
        }

        // --- إنشاء الأدوار ---

        // 1. إنشاء دور "Super Admin"
        Role::create([
            'name' => 'Super Admin',
            'guard_name' => $guardName,
        ]);
        // لا نعطي صلاحيات هنا، لأننا نتعامل معه بشكل خاص في AuthServiceProvider

        // 2. إنشاء دور "Admin"
        $adminRole = Role::create([
            'name' => 'Admin',
            'guard_name' => $guardName,
        ]);
        // إعطاء دور "Admin" كل الصلاحيات ما عدا الحذف
        $adminPermissions = Permission::where('guard_name', $guardName)
                                      ->whereNotIn('name', [
                                          'user.delete',
                                          'role.delete',
                                          'company.delete',
                                          'station.delete',
                                          'driver.delete',
                                          'truck.delete',
                                          'product.delete',
                                          'fuel_order.delete',
                                          'order_status.delete',
                                          'region.delete',
                                      ])->pluck('name');
        $adminRole->givePermissionTo($adminPermissions);

        // 3. إنشاء دور "User"
        $userRole = Role::create([
            'name' => 'User',
            'guard_name' => $guardName,
        ]);
        // إعطاء دور "User" صلاحيات العرض فقط
        $userRole->givePermissionTo([]);
    }
}
