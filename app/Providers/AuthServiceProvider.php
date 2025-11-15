<?php

namespace App\Providers;

// --- بداية الإضافات ---
use App\Models\User;
use App\Policies\UserPolicy;
use Spatie\Permission\Models\Role;
use App\Policies\RolePolicy;
// --- نهاية الإضافات ---

use App\Models\Company;
use App\Models\Driver;
use App\Models\FuelOrder;
use App\Models\Station;
use App\Models\Truck;
use App\Policies\CompanyPolicy;
use App\Policies\DriverPolicy;
use App\Policies\FuelOrderPolicy;
use App\Policies\StationPolicy;
use App\Policies\TruckPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Product; // <-- [جديد]
use App\Policies\ProductPolicy; // <-- [جديد]
use App\Models\OrderStatus; // <-- [جديد]
use App\Policies\OrderStatusPolicy; // <-- [جديد]
use App\Models\Region; // <-- إضافة استيراد
use App\Policies\RegionPolicy; // <-- إضافة استيراد
use App\Models\WorkNature;
use App\Policies\WorkNaturePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // تسجيل الـ Policies التي كانت موجودة
        Company::class => CompanyPolicy::class,
        Station::class => StationPolicy::class,
        WorkNature::class => WorkNaturePolicy::class,
        Driver::class => DriverPolicy::class,
        Truck::class => TruckPolicy::class,
        Product::class => ProductPolicy::class,
        OrderStatus::class => OrderStatusPolicy::class,
        FuelOrder::class => FuelOrderPolicy::class,
        Region::class => RegionPolicy::class,
        // --- بداية التعديل: تسجيل الـ Policies الجديدة ---
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        // --- نهاية التعديل ---
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // هذا الكود يمنح الـ Super Admin صلاحية كاملة على كل شيء
        // يجب أن يأتي بعد registerPolicies
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });
    }
}
