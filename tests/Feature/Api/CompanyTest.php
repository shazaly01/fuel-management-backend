<?php

namespace Tests\Feature\Api;

use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\ApiTestCase; // <-- استخدام الكلاس الأساسي الذي أنشأناه

class CompanyTest extends ApiTestCase
{
    // لا حاجة لـ use RefreshDatabase هنا لأنه موجود في ApiTestCase
    // لا حاجة لـ setUp() هنا لأننا نرثها من ApiTestCase

    /** @test */
   public function a_super_admin_can_view_a_list_of_companies()
{
    Company::factory()->count(5)->create();
    $response = $this->getJson('/api/companies');
    $response->assertOk();
    // هذا التحقق صحيح وسيعمل بعد إصلاح الصلاحيات
    $response->assertJsonCount(5, 'data');
}

    /** @test */
    public function a_user_without_view_permission_cannot_list_companies()
    {
        // Arrange
        Sanctum::actingAs($this->basicUser); // تسجيل الدخول كمستخدم عادي

        // Act
        $response = $this->getJson('/api/companies');

        // Assert
        $response->assertForbidden(); // تأكد من أن الطلب تم رفضه (403 Forbidden)
    }

    /** @test */
    public function an_admin_can_create_a_new_company()
    {
        // Arrange
        Sanctum::actingAs($this->adminUser); // تسجيل الدخول كـ Admin
        $companyData = [
            'name' => 'New Test Company',
        ];

        // Act
        $response = $this->postJson('/api/companies', $companyData);

        // Assert
        $response->assertCreated(); // تأكد من أن الاستجابة هي 201 Created
        $response->assertJsonPath('data.name', 'New Test Company'); // تأكد من أن الاسم صحيح في الاستجابة
        $this->assertDatabaseHas('companies', ['name' => 'New Test Company']); // تأكد من وجود البيانات في قاعدة البيانات
    }

    /** @test */
    public function an_admin_can_update_a_company()
    {
        // Arrange
        Sanctum::actingAs($this->adminUser);
        $company = Company::factory()->create(['name' => 'Old Name']);
        $updateData = ['name' => 'New Updated Name'];

        // Act
        $response = $this->putJson("/api/companies/{$company->id}", $updateData);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('companies', ['id' => $company->id, 'name' => 'New Updated Name']);
    }

    /** @test */
    public function a_super_admin_can_delete_a_company()
    {
        // Arrange
        // الـ Super Admin مسجل دخوله افتراضياً
        $company = Company::factory()->create();

        // Act
        $response = $this->deleteJson("/api/companies/{$company->id}");

        // Assert
        $response->assertNoContent(); // تأكد من أن الاستجابة هي 204 No Content
        $this->assertSoftDeleted('companies', ['id' => $company->id]); // تأكد من أن الحذف كان ناعماً (Soft Delete)
    }

    /** @test */
    public function an_admin_cannot_delete_a_company()
    {
        // Arrange
        Sanctum::actingAs($this->adminUser); // الـ Admin لا يملك صلاحية الحذف
        $company = Company::factory()->create();

        // Act
        $response = $this->deleteJson("/api/companies/{$company->id}");

        // Assert
        $response->assertForbidden();
    }
}
