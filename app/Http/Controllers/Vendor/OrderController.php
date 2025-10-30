<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $productIds = $request->user()->products()->pluck('id');

        $orders = Order::whereHas('items', fn ($query) => $query->whereIn('product_id', $productIds))
            ->with(['items' => fn ($query) => $query->whereIn('product_id', $productIds)])
            ->latest('placed_at')
            ->paginate(15);

        return view('vendor.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order): View
    {
        $productIds = $request->user()->products()->pluck('id');

        abort_unless($order->items()->whereIn('product_id', $productIds)->exists(), 403);

        $order->load(['items' => fn ($query) => $query->whereIn('product_id', $productIds), 'customer']);

        return view('vendor.orders.show', compact('order'));
    }
}
