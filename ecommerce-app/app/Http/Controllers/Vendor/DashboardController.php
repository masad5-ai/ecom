<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $productIds = $user->products()->pluck('id');

        $orders = Order::whereHas('items', fn ($query) => $query->whereIn('product_id', $productIds))
            ->with(['items' => fn ($query) => $query->whereIn('product_id', $productIds)])
            ->latest('placed_at')
            ->take(8)
            ->get();

        $metrics = [
            'products' => $productIds->count(),
            'orders' => $orders->count(),
            'revenue' => $orders->flatMap->items->sum(fn ($item) => $item->line_total),
            'lowStock' => $user->products()->whereColumn('stock', '<=', 'min_stock')->count(),
        ];

        $topProducts = $user->products()
            ->withCount(['orderItems as total_sold' => function ($query) {
                $query->select(DB::raw('coalesce(sum(quantity), 0)'));
            }])
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        return view('vendor.dashboard', compact('metrics', 'orders', 'topProducts'));
    }
}
