<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// استيراد جميع الـ Controllers المستخدمة
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\FuelOrderController; // تأكد من وجود هذا
use App\Http\Controllers\Api\StationController;
use App\Http\Controllers\Api\TruckController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderStatusController;
use App\Http\Controllers\Api\RegionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// --- المسارات العامة (Public) ---
Route::post('/login', [AuthController::class, 'login']);

// --- المسارات المحمية (Protected) ---
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        $user = $request->user();
        $user->load('roles.permissions');
        return response()->json($user);
    });

    Route::get('/dashboard', [ReportController::class, 'dashboardStats'])
         ->name('dashboard.stats')
         ->middleware('can:dashboard.view');

    Route::get('roles/permissions', [RoleController::class, 'getAllPermissions'])->name('roles.permissions');

    // --- [بداية التعديل هنا] ---
    // استخدام apiResource لضمان تعريف المسارات بالطريقة القياسية وتجنب التعارضات
    Route::apiResource('fuel-orders', FuelOrderController::class);
    // --- [نهاية التعديل هنا] ---

    // باقي مسارات apiResource
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('stations', StationController::class);
    Route::apiResource('drivers', DriverController::class);
    Route::apiResource('regions', RegionController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('order-statuses', OrderStatusController::class);
    Route::apiResource('trucks', TruckController::class);

    // مسارات التقارير (تبقى كما هي)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/drivers', [ReportController::class, 'driverReport'])->name('drivers')->middleware('can:viewAny,' . \App\Models\Driver::class);
        Route::get('/orders', [ReportController::class, 'orderReport'])->name('orders')->middleware('can:viewAny,' . \App\Models\FuelOrder::class);
        Route::get('/stations', [ReportController::class, 'stationReport'])->name('stations')->middleware('can:viewAny,' . \App\Models\Station::class);
    });

});
