<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $page = $request->get('page', 1);
        $perPage = min($request->get('per_page', 15), 100); // Max 100 items per page
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Create cache key based on query parameters
        $cacheKey = 'products_'.md5(json_encode([
            'page' => $page,
            'per_page' => $perPage,
            'search' => $search,
            'sort_by' => $sortBy,
            'sort_direction' => $sortDirection,
        ]));

        // Cache the query for 5 minutes
        return Cache::tags('products')->remember($cacheKey, 60 * 5, function () use ($search, $sortBy, $sortDirection, $perPage) {
            $query = Product::query();

            // Search functionality
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            }

            // Sort
            if (in_array($sortBy, Product::$sortable)) {
                $query->orderBy($sortBy, $sortDirection === 'asc' ? 'asc' : 'desc');
            }

            $products = $query->paginate($perPage);

            return ProductResource::collection($products);
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = Product::create($request->validated());

        return response()->json([
            'message' => 'Product created successfully',
            'product' => new ProductResource($product),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json([
            'product' => new ProductResource($product),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        info('request: '.json_encode($request->all()));
        $product->update($request->validated());
        $product->refresh();

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => new ProductResource($product),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully',
        ]);
    }

    /**
     * Restore a soft-deleted product.
     */
    public function restore(int $id): JsonResponse
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        return response()->json([
            'message' => 'Product restored successfully',
            'product' => new ProductResource($product),
        ]);
    }

    /**
     * Force delete a soft-deleted product.
     */
    public function forceDelete(int $id): JsonResponse
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->forceDelete();

        return response()->json([
            'message' => 'Product permanently deleted',
        ]);
    }

    /**
     * Get trashed products.
     */
    public function trashed(): AnonymousResourceCollection
    {
        $products = Product::onlyTrashed()->paginate(15);

        return ProductResource::collection($products);
    }
}
