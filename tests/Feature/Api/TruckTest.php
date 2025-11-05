<?php

namespace Tests\Feature\Api;

use App\Models\Driver;
use App\Models\Truck;
use Laravel\Sanctum\Sanctum;
use Tests\ApiTestCase;

class TruckTest extends ApiTestCase
{
    /** @test */
    public function a_super_admin_can_view_a_list_of_trucks()
    {
        Truck::factory()->count(5)->create();
        $response = $this->getJson('/api/trucks');
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function a_user_without_view_permission_cannot_list_trucks()
    {
        Sanctum::actingAs($this->basicUser);
        $response = $this->getJson('/api/trucks');
        $response->assertForbidden();
    }

    /** @test */
    public function an_admin_can_create_a_new_truck()
    {
        Sanctum::actingAs($this->adminUser);
        $driver = Driver::factory()->create(); // يجب أن يكون السائق موجوداً
        $truckData = [
            'truck_number' => 'TR-12345',
            'truck_type' => 'MAN',
            'color' => 'Blue',
            'trailer_number' => 'TL-54321',
            'driver_id' => $driver->id,
        ];

        $response = $this->postJson('/api/trucks', $truckData);

        $response->assertCreated();
        $this->assertDatabaseHas('trucks', ['truck_number' => 'TR-12345', 'driver_id' => $driver->id]);
    }

    /** @test */
    public function an_admin_can_update_a_truck()
    {
        Sanctum::actingAs($this->adminUser);
        $truck = Truck::factory()->create();
        $updateData = ['truck_type' => 'Iveco'];

        $response = $this->putJson("/api/trucks/{$truck->id}", $updateData);

        $response->assertOk();
        $this->assertDatabaseHas('trucks', ['id' => $truck->id, 'truck_type' => 'Iveco']);
    }

    /** @test */
    public function a_super_admin_can_delete_a_truck()
    {
        $truck = Truck::factory()->create();
        $response = $this->deleteJson("/api/trucks/{$truck->id}");
        $response->assertNoContent();
        $this->assertSoftDeleted('trucks', ['id' => $truck->id]);
    }

    /** @test */
    public function an_admin_cannot_delete_a_truck()
    {
        Sanctum::actingAs($this->adminUser);
        $truck = Truck::factory()->create();
        $response = $this->deleteJson("/api/trucks/{$truck->id}");
        $response->assertForbidden();
    }
}
