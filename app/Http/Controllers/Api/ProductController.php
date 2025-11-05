<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Api\ProductResource;
use App\Models\Product;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(): AnonymousResourceCollection
    {
        $products = Product::query()->latest()->paginate(15);
        return ProductResource::collection($products);
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());
        return response()->json([
            'message' => 'Product created successfully.',
            'data' => ProductResource::make($product),
        ], Response::HTTP_CREATED);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(ProductResource::make($product));
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $product->update($request->validated());
        return response()->json([
            'message' => 'Product updated successfully.',
            'data' => ProductResource::make($product->fresh()),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
