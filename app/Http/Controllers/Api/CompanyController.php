<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\Api\CompanyResource;
// 1. استيراد التريت
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends Controller
{
    // 2. استخدام التريت
    use AuthorizesRequests;

    /**
     * Constructor to apply middleware.
     */
    public function __construct()
    {
        // الآن، هذه الدالة ستكون معرفة وصحيحة
        $this->authorizeResource(Company::class, 'company');
    }

    /**
     * Display a listing of the resource.
     * @return AnonymousResourceCollection // <-- تعديل نوع الإرجاع
     */
    public function index(): AnonymousResourceCollection // <-- تعديل نوع الإرجاع
    {
        $companies = Company::query()->latest()->paginate(15);

        // --- بداية التعديل ---
        // قم بإرجاع الـ Resource Collection مباشرة
        return CompanyResource::collection($companies);
        // --- نهاية التعديل ---
    }


    /**
     * Store a newly created resource in storage.
     * @param StoreCompanyRequest $request
     * @return JsonResponse
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $company = Company::create($request->validated());
        return response()->json([
            'message' => 'Company created successfully.',
            'data' => CompanyResource::make($company),
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     * @param Company $company
     * @return JsonResponse
     */
     public function show(Company $company): JsonResponse
    {
        return response()->json(CompanyResource::make($company));
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateCompanyRequest $request
     * @param Company $company
     * @return JsonResponse
     */
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $company->update($request->validated());
        return response()->json([
            'message' => 'Company updated successfully.',
            'data' => CompanyResource::make($company->fresh()),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @param Company $company
     * @return JsonResponse
     */
    public function destroy(Company $company): JsonResponse
    {
        $company->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
