<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function stats()
    {
        $last7Days = collect(range(0, 6))->map(function ($i) {
            return Carbon::today()->subDays($i)->format('Y-m-d');
        })->reverse();

        $salesTrend = DB::table('orders')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as total'))
            ->where('created_at', '>=', Carbon::today()->subDays(6))
            ->groupBy('date')
            ->get()
            ->pluck('total', 'date');

        $trendData = $last7Days->map(function ($date) use ($salesTrend) {
            return [
                'date' => Carbon::parse($date)->format('M d'),
                'total' => $salesTrend->get($date, 0)
            ];
        });

        $topCategories = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(5)
            ->get();

        $totalRevenue = Order::sum('total_price');
        $totalOrders = Order::count();
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return response()->json([
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'avg_order_value' => round($avgOrderValue, 2),
            'total_products' => Product::count(),
            'total_users' => User::count(),
            'low_stock_count' => Product::where('stock', '<', 5)->count(),
            'recent_orders' => Order::with('user')->latest()->take(5)->get(),
            'sales_trend' => $trendData,
            'top_categories' => $topCategories
        ]);
    }
}
