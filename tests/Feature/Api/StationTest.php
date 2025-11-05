<?php

namespace Tests\Feature\Api;

use App\Models\Company;
use App\Models\Station;
use Laravel\Sanctum\Sanctum;
use Tests\ApiTestCase;

class StationTest extends ApiTestCase
{
    /** @test */
    public function a_super_admin_can_view_a_list_of_stations()
    {
        Station::factory()->count(5)->create();
        $response = $this->getJson('/api/stations');
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function a_user_without_view_permission_cannot_list_stations()
    {
        Sanctum::actingAs($this->basicUser);
        $response = $this->getJson('/api/stations');
        $response->assertForbidden();
    }

    /** @test */
    public function an_admin_can_create_a_new_station()
    {
        Sanctum::actingAs($this->adminUser);
        $company = Company::factory()->create(); // يجب أن تكون الشركة موجودة
        $stationData = [
            'name' => 'New Test Station',
            'address' => '123 Main St',
            'station_number' => 'ST-9999',
            'company_id' => $company->id,
        ];

        $response = $this->postJson('/api/stations', $stationData);

        $response->assertCreated();
        $this->assertDatabaseHas('stations', ['name' => 'New Test Station', 'company_id' => $company->id]);
    }

    /** @test */
    public function an_admin_can_update_a_station()
    {
        Sanctum::actingAs($this->adminUser);
        $station = Station::factory()->create();
        $updateData = ['name' => 'Updated Station Name'];

        $response = $this->putJson("/api/stations/{$station->id}", $updateData);

        $response->assertOk();
        $this->assertDatabaseHas('stations', ['id' => $station->id, 'name' => 'Updated Station Name']);
    }

    /** @test */
    public function a_super_admin_can_delete_a_station()
    {
        $station = Station::factory()->create();
        $response = $this->deleteJson("/api/stations/{$station->id}");
        $response->assertNoContent();
        $this->assertSoftDeleted('stations', ['id' => $station->id]);
    }

    /** @test */
    public function an_admin_cannot_delete_a_station()
    {
        Sanctum::actingAs($this->adminUser);
        $station = Station::factory()->create();
        $response = $this->deleteJson("/api/stations/{$station->id}");
        $response->assertForbidden();
    }
}
