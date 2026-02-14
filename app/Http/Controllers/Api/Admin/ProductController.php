<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand']);

        // Filter by name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->where('stock', '>', 0)->where('stock', '<', 5);
            } elseif ($request->stock_status === 'in_stock') {
                $query->where('stock', '>=', 5);
            }
        }

        return response()->json($query->latest()->paginate(15));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'required|exists:brands,id',
                'price' => 'required|numeric',
                'original_price' => 'nullable|numeric',
                'sale_price' => 'nullable|numeric',
                'cost_price' => 'nullable|numeric',
                'description' => 'nullable|string',
                'image' => 'nullable|string',
                'gallery' => 'nullable|array',
                'specifications' => 'nullable|array',
                'stock' => 'required|integer|min:0',
            ]);

            $validated['slug'] = Str::slug($validated['name']);
            
            // Handle image field specifically if it's sent as a base64 string or similar
            // For now, ensure it's not too long for the column if provided
            if (isset($validated['image']) && strlen($validated['image']) > 255) {
                // This is a safety check; ideally, you'd handle file uploads
                $validated['image'] = substr($validated['image'], 0, 255);
            }

            $product = Product::create($validated);

            return response()->json($product, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product creation failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Product creation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        return Product::with(['category', 'brand'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'price' => 'required|numeric',
            'original_price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'cost_price' => 'nullable|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'gallery' => 'nullable|array',
            'specifications' => 'nullable|array',
            'stock' => 'required|integer|min:0',
        ]);

        if ($product->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $product->update($validated);

        return response()->json($product);
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
