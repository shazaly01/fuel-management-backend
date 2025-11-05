<?php

namespace Tests\Feature\Api;

use App\Models\Driver;
use App\Models\FuelOrder;
use App\Models\OrderStatus;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\PermissionRegistrar; // <-- إضافة جديدة
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $adminUser;
    protected User $basicUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);

        // --- بداية التعديل: حل مشكلة الصلاحيات ---
        // مسح cache الصلاحيات بشكل صريح قبل كل اختبار
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
        // --- نهاية التعديل ---

        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('Super Admin');

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole('Admin');

        $this->basicUser = User::factory()->create();
        $this->basicUser->assignRole('User');
    }

    #[Test]
    public function unauthenticated_user_cannot_view_dashboard(): void
    {
        $this->getJson('/api/dashboard')->assertUnauthorized();
    }

    #[Test]
    public function unauthorized_user_cannot_view_dashboard(): void
    {
        Sanctum::actingAs($this->basicUser);
        $this->getJson('/api/dashboard')->assertForbidden();
    }

    #[Test]
    public function authorized_user_can_view_dashboard_with_correct_stats(): void
    {
        Sanctum::actingAs($this->adminUser);

        // --- بداية التعديل: حل مشكلة الإحصائيات ---
        // 1. إنشاء السائقين أولاً وبشكل معزول
        $availableDrivers = Driver::factory()->count(5)->create(['status' => 'available']);
        $onTripDrivers = Driver::factory()->count(2)->create(['status' => 'on_trip']);
        $allDrivers = $availableDrivers->merge($onTripDrivers); // مجموعة كل السائقين

        // 2. إنشاء الحالات
        $pendingStatus = OrderStatus::factory()->create(['name' => 'Pending']);
        $deliveredStatus = OrderStatus::factory()->create(['name' => 'Delivered']);

        // 3. إنشاء الطلبات وربطها بالسائقين الموجودين بالفعل (بدلاً من إنشاء سائقين جدد)
        FuelOrder::factory()->count(10)->create([
            'order_status_id' => $pendingStatus->id,
            'driver_id' => $allDrivers->random()->id, // اختيار سائق عشوائي من الموجودين
        ]);
        FuelOrder::factory()->count(25)->create([
            'order_status_id' => $deliveredStatus->id,
            'driver_id' => $allDrivers->random()->id, // اختيار سائق عشوائي من الموجودين
        ]);
        // --- نهاية التعديل ---

        $response = $this->getJson('/api/dashboard');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'general' => ['users', 'companies', 'stations', 'trucks'],
                'drivers' => ['total', 'available', 'on_trip'],
                'orders' => [],
            ]
        ]);

        $response->assertJsonPath('data.general.users', 3);
        $response->assertJsonPath('data.drivers.total', 7); // الآن يجب أن تكون 7
        $response->assertJsonPath('data.drivers.available', 5);
        $response->assertJsonPath('data.drivers.on_trip', 2);
        $response->assertJsonPath('data.orders.Pending', 10);
        $response->assertJsonPath('data.orders.Delivered', 25);
    }

    #[Test]
    public function super_admin_can_also_view_dashboard(): void
    {
        Sanctum::actingAs($this->superAdmin);
        $this->getJson('/api/dashboard')->assertOk();
    }
}
