<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminUser;
    protected User $basicUser;

    /**
     * هذا الإعداد يتم تشغيله قبل كل اختبار في الكلاسات التي ترث هذا الكلاس.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 1. تشغيل الـ Seeders الأساسية لإنشاء الأدوار والصلاحيات
        $this->artisan('db:seed', ['--class' => 'Database\Seeders\PermissionSeeder']);

        // 2. إنشاء مستخدمين بأدوار مختلفة
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('Super Admin');

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Admin');

        $this->basicUser = User::factory()->create();
        $this->basicUser->assignRole('User');

        // 3. تسجيل الدخول كمستخدم افتراضي (يمكن تغييره في كل اختبار)
        // سنقوم بتسجيل دخول الـ Super Admin افتراضياً
        Sanctum::actingAs($this->superAdmin);
    }
}
