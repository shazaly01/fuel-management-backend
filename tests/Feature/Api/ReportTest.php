<?php

namespace Tests\Feature\Api;

use App\Models\Driver;
use App\Models\FuelOrder;
use App\Models\OrderStatus;
use Laravel\Sanctum\Sanctum;
use Tests\ApiTestCase;
use Illuminate\Support\Carbon;
use App\Models\Station;
use App\Models\Company;
class ReportTest extends ApiTestCase
{
    private OrderStatus $deliveredStatus;
    private OrderStatus $pendingStatus;

    // نقوم بإعداد حالات الطلب التي سنحتاجها في جميع الاختبارات
    protected function setUp(): void
    {
        parent::setUp();
        $this->pendingStatus = OrderStatus::factory()->create(['name' => 'Pending']);
        $this->deliveredStatus = OrderStatus::factory()->create(['name' => 'Delivered']);
    }

    /** @test */
    public function an_authorized_user_can_view_the_driver_report_without_filters()
    {
        // Arrange: إنشاء 5 سائقين
        Driver::factory()->count(5)->create();

        // Act: استدعاء نقطة النهاية بدون فلاتر
        $response = $this->getJson('/api/reports/drivers');

        // Assert: التأكد من أن الاستجابة صحيحة وتحتوي على 5 سائقين
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function the_driver_report_can_be_filtered_by_status()
    {
        // Arrange: إنشاء 3 سائقين متاحين و 2 غير متاحين
        Driver::factory()->count(3)->create(['status' => 'available']);
        Driver::factory()->count(2)->create(['status' => 'on-trip']);

        // Act: استدعاء نقطة النهاية مع فلتر الحالة
        $response = $this->getJson('/api/reports/drivers?status=available');

        // Assert: التأكد من أن الاستجابة تحتوي فقط على 3 سائقين
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
        // يمكننا أيضاً التأكد من أن كل سائق في الاستجابة حالته 'available'
        foreach ($response->json('data') as $driver) {
            $this->assertEquals('available', $driver['status']);
        }
    }

    /** @test */
    public function the_driver_report_can_be_filtered_by_order_limit()
    {
        // Arrange:
        // سائق 1: لديه طلبيتان قيد الانتظار
        $driver1 = Driver::factory()->create();
        FuelOrder::factory()->count(2)->create([
            'driver_id' => $driver1->id,
            'order_status_id' => $this->pendingStatus->id,
        ]);

        // سائق 2: لديه 4 طلبيات (2 قيد الانتظار و 2 تم تسليمها) -> المجموع 2 غير مسلمة
        $driver2 = Driver::factory()->create();
        FuelOrder::factory()->count(2)->create([
            'driver_id' => $driver2->id,
            'order_status_id' => $this->pendingStatus->id,
        ]);
        FuelOrder::factory()->count(2)->create([
            'driver_id' => $driver2->id,
            'order_status_id' => $this->deliveredStatus->id,
        ]);

        // سائق 3: لديه 5 طلبيات قيد الانتظار
        $driver3 = Driver::factory()->create();
        FuelOrder::factory()->count(5)->create([
            'driver_id' => $driver3->id,
            'order_status_id' => $this->pendingStatus->id,
        ]);

        // Act: استدعاء نقطة النهاية مع فلتر سقف الطلبيات = 3
        // يجب أن يظهر السائق 1 والسائق 2 فقط
        $response = $this->getJson('/api/reports/drivers?order_limit=3');

        // Assert:
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        // التأكد من أن السائقين الصحيحين هما من ظهرا في النتائج
        $returnedDriverIds = collect($response->json('data'))->pluck('id');
        $this->assertTrue($returnedDriverIds->contains($driver1->id));
        $this->assertTrue($returnedDriverIds->contains($driver2->id));
        $this->assertFalse($returnedDriverIds->contains($driver3->id));
    }

    /** @test */
    public function a_user_without_permission_cannot_view_the_driver_report()
    {
        // Arrange: تسجيل الدخول كمستخدم عادي لا يملك صلاحية 'driver.view'
        Sanctum::actingAs($this->basicUser);

        // Act: محاولة الوصول للتقرير
        $response = $this->getJson('/api/reports/drivers');

        // Assert: التأكد من أن الوصول تم رفضه
        $response->assertForbidden();
    }



      /** @test */
    public function the_order_report_can_be_viewed_by_authorized_users()
    {
        FuelOrder::factory()->count(5)->create();

        $response = $this->getJson('/api/reports/orders');

        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function the_order_report_can_be_filtered_by_status()
    {
        // Arrange: إنشاء 3 طلبيات قيد الانتظار و 2 تم تسليمها
        FuelOrder::factory()->count(3)->create(['order_status_id' => $this->pendingStatus->id]);
        FuelOrder::factory()->count(2)->create(['order_status_id' => $this->deliveredStatus->id]);

        // Act: جلب الطلبيات التي قيد الانتظار فقط
        $response = $this->getJson('/api/reports/orders?order_status_id=' . $this->pendingStatus->id);

        // Assert
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function the_order_report_can_be_filtered_by_days_ago()
    {
        // Arrange:
        // 3 طلبيات اليوم
        FuelOrder::factory()->count(3)->create(['order_date' => Carbon::today()]);
        // 2 طلبيات منذ 5 أيام
        FuelOrder::factory()->count(2)->create(['order_date' => Carbon::today()->subDays(5)]);

        // Act: جلب طلبيات آخر 3 أيام (يجب أن تظهر طلبيات اليوم فقط)
        $response = $this->getJson('/api/reports/orders?days_ago=3');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    // --- نهاية إضافة اختبارات تقرير الطلبيات ---


    // --- بداية إضافة اختبارات تقرير المحطات ---

    /** @test */
    public function the_station_report_can_be_viewed_by_authorized_users()
    {
        Station::factory()->count(5)->create();

        $response = $this->getJson('/api/reports/stations');

        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function the_station_report_can_be_filtered_by_company()
    {
        // Arrange:
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();
        // 3 محطات تابعة للشركة A
        Station::factory()->count(3)->create(['company_id' => $companyA->id]);
        // 2 محطات تابعة للشركة B
        Station::factory()->count(2)->create(['company_id' => $companyB->id]);

        // Act: جلب المحطات التابعة للشركة A فقط
        $response = $this->getJson('/api/reports/stations?company_id=' . $companyA->id);

        // Assert
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

}
