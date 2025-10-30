<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $metrics = [
            'ordersToday' => Order::whereDate('created_at', today())->count(),
            'ordersProcessing' => Order::where('status', 'processing')->count(),
            'revenueThisMonth' => Order::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('grand_total'),
            'productsLowStock' => Product::whereColumn('stock', '<=', 'min_stock')->count(),
            'customerCount' => User::where('role', 'customer')->count(),
            'vendorCount' => User::where('role', 'vendor')->count(),
        ];

        $recentOrders = Order::with('customer')
            ->latest('placed_at')
            ->take(8)
            ->get();

        $topProducts = Product::withCount(['orderItems as total_sold' => function ($query) {
            $query->select(DB::raw('coalesce(sum(quantity), 0)'));
        }])
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('metrics', 'recentOrders', 'topProducts'));
    }
}
