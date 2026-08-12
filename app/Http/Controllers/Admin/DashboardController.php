<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard statistics.
     */
    public function index(): View
    {
        $cancelled = [OrderStatus::Cancelled->value];

        $statistics = DB::table('orders')
            ->whereNotIn('status', $cancelled)
            ->selectRaw('COUNT(*) as total_orders, COALESCE(SUM(total), 0) as revenue')
            ->first();

        $productsCount = Product::count();
        $categoriesCount = Category::count();

        $recentOrders = Order::query()
            ->withCount('items')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', [
            'totalOrders' => $statistics->total_orders,
            'revenue' => number_format($statistics->revenue, 2, '.', ''),
            'productsCount' => $productsCount,
            'categoriesCount' => $categoriesCount,
            'recentOrders' => $recentOrders,
        ]);
    }
}