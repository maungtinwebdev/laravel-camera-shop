<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric',
            'shipping_address' => 'required|string',
        ]);

        $userId = $request->user() ? $request->user()->id : 1; // Use auth user or default to 1

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $userId,
                'status' => 'pending',
                'total_price' => $validated['total_price'],
                'shipping_address' => $validated['shipping_address'],
            ]);

            foreach ($validated['items'] as $item) {
                $product = \App\Models\Product::find($item['product_id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
            }

            DB::commit();

            return response()->json($order->load('items.product'), 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Order creation failed: ' . $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items.product'])
            ->latest()
            ->get();

        return response()->json($orders);
    }
}
