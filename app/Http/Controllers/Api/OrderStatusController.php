<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderStatus\StoreOrderStatusRequest;
use App\Http\Requests\OrderStatus\UpdateOrderStatusRequest;
use App\Http\Resources\Api\OrderStatusResource;
use App\Models\OrderStatus;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class OrderStatusController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        // اسم الموديل 'order_status' ليطابق اسم الـ policy
        $this->authorizeResource(OrderStatus::class, 'order_status');
    }

    public function index(): AnonymousResourceCollection
    {
        // لا نحتاج لترقيم الصفحات هنا، نريد كل الحالات للقوائم المنسدلة
        $statuses = OrderStatus::query()->latest()->get();
        return OrderStatusResource::collection($statuses);
    }

    public function store(StoreOrderStatusRequest $request): JsonResponse
    {
        $status = OrderStatus::create($request->validated());
        return response()->json([
            'message' => 'Order status created successfully.',
            'data' => OrderStatusResource::make($status),
        ], Response::HTTP_CREATED);
    }

    public function show(OrderStatus $orderStatus): JsonResponse
    {
        return response()->json(OrderStatusResource::make($orderStatus));
    }

    public function update(UpdateOrderStatusRequest $request, OrderStatus $orderStatus): JsonResponse
    {
        $orderStatus->update($request->validated());
        return response()->json([
            'message' => 'Order status updated successfully.',
            'data' => OrderStatusResource::make($orderStatus->fresh()),
        ]);
    }

    public function destroy(OrderStatus $orderStatus): JsonResponse
    {
        $orderStatus->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
