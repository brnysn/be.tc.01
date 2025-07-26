<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Order::class);

        $page = $request->get('page', 1);
        $perPage = min($request->get('per_page', 15), 100); // Max 100 items per page
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        // No cache requested for this route

        $query = Order::query();

        // With relationships
        if ($request->has('with')) {
            $with = explode(',', $request->get('with'));
            $query->with($with);
        }

        // Customer global scope is applied in the model

        // Sort
        if (in_array($sortBy, Order::$sortable)) {
            $query->orderBy($sortBy, $sortDirection === 'asc' ? 'asc' : 'desc');
        }

        return OrderResource::collection($query->paginate($perPage));
    }

    public function show(Order $order, Request $request)
    {
        $this->authorize('view', $order);

        // With relationships
        if ($request->has('with')) {
            $with = explode(',', $request->get('with'));
            $order->load($with);
        }

        return new OrderResource($order);
    }

    public function store(StoreOrderRequest $request)
    {
        $this->authorize('create', Order::class);

        DB::beginTransaction();

        // Create an empty order
        $order = auth()->user()->orders()->create();

        // Add order items to the order
        $products = Product::whereIn('id', collect($request->items)->pluck('product_id'))->get();
        $totalPrice = 0;
        foreach ($request->items as $item) {
            $product = $products->firstWhere('id', $item['product_id']);
            $order->items()->create([
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
            ]);
            $totalPrice += $product->price * $item['quantity'];

            // Stock check is already done in the request
            $product->decreaseStockQuantity($item['quantity']);
        }

        // Update order total price
        $order->total_price = $totalPrice;
        $order->save();

        DB::commit();

        $order->refresh();
        $order->load('items.product');

        return new OrderResource($order);
    }
}
