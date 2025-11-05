<?php

namespace Tests\Feature\Api;

use App\Models\Driver;
use App\Models\FuelOrder;
use App\Models\OrderStatus; // <-- تعديل
use App\Models\Product;
use App\Models\Station;
use Laravel\Sanctum\Sanctum;
use Tests\ApiTestCase;

class FuelOrderTest extends ApiTestCase
{
    // لم نعد بحاجة لمتغير status منفصل أو setUp مخصصة هنا

    /** @test */
    public function a_super_admin_can_view_a_list_of_fuel_orders()
    {
        FuelOrder::factory()->count(5)->create(); // الـ Factory سيهتم بإنشاء كل شيء
        $response = $this->getJson('/api/fuel-orders');
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function a_user_without_view_permission_cannot_list_fuel_orders()
    {
        Sanctum::actingAs($this->basicUser);
        $response = $this->getJson('/api/fuel-orders');
        $response->assertForbidden();
    }

    /** @test */
    public function an_admin_can_create_a_new_fuel_order()
    {
        Sanctum::actingAs($this->adminUser);
        $driver = Driver::factory()->create();
        $station = Station::factory()->create();
        $product = Product::factory()->create();
        $status = OrderStatus::factory()->create(); // <-- تعديل

        $orderData = [
            'driver_id' => $driver->id,
            'station_id' => $station->id,
            'product_id' => $product->id,
            'order_status_id' => $status->id, // <-- تعديل
            'quantity' => 10000,
            'order_date' => now()->toDateString(),
        ];

        $response = $this->postJson('/api/fuel-orders', $orderData);

        $response->assertCreated();
        $this->assertDatabaseHas('fuel_orders', [
            'driver_id' => $driver->id,
            'station_id' => $station->id
        ]);
    }

    /** @test */
    public function an_admin_can_update_a_fuel_order()
    {
        Sanctum::actingAs($this->adminUser);
        $order = FuelOrder::factory()->create(['quantity' => 5000]);
        $updateData = ['quantity' => 7500];

        $response = $this->putJson("/api/fuel-orders/{$order->id}", $updateData);

        $response->assertOk();
        $this->assertDatabaseHas('fuel_orders', ['id' => $order->id, 'quantity' => 7500]);
    }

    /** @test */
    public function a_super_admin_can_delete_a_fuel_order()
    {
        $order = FuelOrder::factory()->create();
        $response = $this->deleteJson("/api/fuel-orders/{$order->id}");
        $response->assertNoContent();
        $this->assertSoftDeleted('fuel_orders', ['id' => $order->id]);
    }

    /** @test */
    public function an_admin_cannot_delete_a_fuel_order()
    {
        Sanctum::actingAs($this->adminUser);
        $order = FuelOrder::factory()->create();
        $response = $this->deleteJson("/api/fuel-orders/{$order->id}");
        $response->assertForbidden();
    }
}
