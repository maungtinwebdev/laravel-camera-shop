<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        
        $search = $request->get('search', '');
        if (is_array($search)) {
            $search = implode(' ', $search);
        }
        $search = (string) $search;

        $category = $request->get('category', '');
        if (is_array($category)) {
            $category = implode(',', $category);
        }
        $category = (string) $category;

        $brand = $request->get('brand', '');
        if (is_array($brand)) {
            $brand = implode(',', $brand);
        }
        $brand = (string) $brand;
        
        $cacheKey = "products_index_{$page}_{$search}_{$category}_{$brand}";

        return Cache::remember($cacheKey, 600, function () use ($request, $search, $category, $brand) {
            $query = Product::with(['category', 'brand'])->latest();

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhereHas('category', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('brand', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            }

            if (!empty($category)) {
                $categories = explode(',', $category);
                $query->whereHas('category', function ($q) use ($categories) {
                    $q->whereIn('slug', $categories);
                });
            }

            if (!empty($brand)) {
                $brands = explode(',', $brand);
                $query->whereHas('brand', function ($q) use ($brands) {
                    $q->whereIn('slug', $brands);
                });
            }

            return $query->paginate(15);
        });
    }

    public function show($slug)
    {
        $product = Cache::remember("product_show_{$slug}", 3600, function () use ($slug) {
            return Product::with(['category', 'brand'])->where('slug', $slug)->firstOrFail();
        });
        
        // Increment view count asynchronously/separately from the cached response
        // Using a background task or just firing it without waiting if possible
        $product->increment('views_count');
        
        return response()->json($product);
    }

    public function mostViewed()
    {
        $products = Cache::remember('products_most_viewed', 1800, function () {
            return Product::with(['category', 'brand'])
                ->orderBy('views_count', 'desc')
                ->limit(8)
                ->get();
        });
            
        return response()->json($products);
    }
}
