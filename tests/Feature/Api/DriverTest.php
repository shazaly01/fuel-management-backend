<?php

namespace Tests\Feature\Api;

use App\Models\Driver;
use Laravel\Sanctum\Sanctum;
use Tests\ApiTestCase;

class DriverTest extends ApiTestCase
{
    /** @test */
    public function a_super_admin_can_view_a_list_of_drivers()
    {
        Driver::factory()->count(5)->create();
        $response = $this->getJson('/api/drivers');
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function a_user_without_view_permission_cannot_list_drivers()
    {
        Sanctum::actingAs($this->basicUser);
        $response = $this->getJson('/api/drivers');
        $response->assertForbidden();
    }

    /** @test */
    public function an_admin_can_create_a_new_driver()
    {
        Sanctum::actingAs($this->adminUser);
        $driverData = [
            'name' => 'New Test Driver',
            'license_number' => '12345-ABC',
            'phone_number' => '555-123-4567',
            'status' => 'available',
        ];

        $response = $this->postJson('/api/drivers', $driverData);

        $response->assertCreated();
        $this->assertDatabaseHas('drivers', ['name' => 'New Test Driver']);
    }

    /** @test */
    public function an_admin_can_update_a_driver()
    {
        Sanctum::actingAs($this->adminUser);
        $driver = Driver::factory()->create();
        $updateData = ['name' => 'Updated Driver Name'];

        $response = $this->putJson("/api/drivers/{$driver->id}", $updateData);

        $response->assertOk();
        $this->assertDatabaseHas('drivers', ['id' => $driver->id, 'name' => 'Updated Driver Name']);
    }

    /** @test */
    public function a_super_admin_can_delete_a_driver()
    {
        $driver = Driver::factory()->create();
        $response = $this->deleteJson("/api/drivers/{$driver->id}");
        $response->assertNoContent();
        $this->assertSoftDeleted('drivers', ['id' => $driver->id]);
    }

    /** @test */
    public function an_admin_cannot_delete_a_driver()
    {
        Sanctum::actingAs($this->adminUser);
        $driver = Driver::factory()->create();
        $response = $this->deleteJson("/api/drivers/{$driver->id}");
        $response->assertForbidden();
    }
}
