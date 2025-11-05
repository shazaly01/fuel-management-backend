<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected User $testUser;
    protected string $password = 'password123';

    /**
     * إعداد بيئة الاختبار.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 1. تشغيل الـ Seeder لإنشاء الأدوار والصلاحيات
        $this->seed(PermissionSeeder::class);

        // 2. إنشاء مستخدم تجريبي بكلمة مرور معروفة
        $this->testUser = User::factory()->create([
            'password' => bcrypt($this->password),
        ]);
        // منحه دورًا للتحقق من تحميله عند تسجيل الدخول
        $this->testUser->assignRole('Admin');
    }

    #[Test]
    public function a_user_can_login_with_correct_credentials(): void
    {
        // Arrange
        $credentials = [
            'username' => $this->testUser->username,
            'password' => $this->password,
        ];

        // Act
        $response = $this->postJson('/api/login', $credentials);

        // Assert
        $response->assertOk(); // 200 OK
        $response->assertJsonStructure([
            'message',
            'access_token',
            'token_type',
            'user' => [
                'id',
                'full_name',
                'username',
                'email',
                'roles' => [
                    '*' => ['id', 'name', 'permissions']
                ],
                'permissions'
            ],
        ]);
        $response->assertJsonPath('user.id', $this->testUser->id);
        $response->assertJsonPath('user.roles.0.name', 'Admin'); // التأكد من تحميل الأدوار
    }

    #[Test]
    public function a_user_cannot_login_with_incorrect_password(): void
    {
        // Arrange
        $credentials = [
            'username' => $this->testUser->username,
            'password' => 'wrong-password',
        ];

        // Act
        $response = $this->postJson('/api/login', $credentials);

        // Assert
        $response->assertStatus(401); // Unauthorized
        $response->assertJsonFragment(['message' => 'The provided credentials do not match our records.']);
    }

    #[Test]
    public function a_user_cannot_login_with_non_existent_username(): void
    {
        // Arrange
        $credentials = [
            'username' => 'nonexistentuser',
            'password' => $this->password,
        ];

        // Act
        $response = $this->postJson('/api/login', $credentials);

        // Assert
        $response->assertStatus(401);
    }

    #[Test]
    public function login_requires_a_username_and_password(): void
    {
        // Test without password
        $this->postJson('/api/login', ['username' => $this->testUser->username])
             ->assertStatus(422) // Unprocessable Entity
             ->assertJsonValidationErrors('password');

        // Test without username
        $this->postJson('/api/login', ['password' => $this->password])
             ->assertStatus(422)
             ->assertJsonValidationErrors('username');
    }

    #[Test]
    public function a_logged_in_user_can_logout(): void
    {
        // 1. تسجيل الدخول للحصول على توكن حقيقي
        $loginResponse = $this->postJson('/api/login', [
            'username' => $this->testUser->username,
            'password' => $this->password,
        ]);
        $token = $loginResponse->json('access_token');

        // التأكد من أن التوكن موجود في قاعدة البيانات بعد تسجيل الدخول
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $this->testUser->id,
        ]);

        // 2. استخدام التوكن لتسجيل الخروج
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        // 3. التأكد من أن التوكن قد تم حذفه من قاعدة البيانات
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $this->testUser->id,
        ]);
    }

    #[Test]
    public function an_unauthenticated_user_cannot_logout(): void
    {
        // Act & Assert
        $this->postJson('/api/logout')->assertUnauthorized();
    }
}
