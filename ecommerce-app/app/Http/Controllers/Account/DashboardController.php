<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $recentOrders = $user->orders()
            ->with('items.product')
            ->latest('placed_at')
            ->take(5)
            ->get();

        $stats = [
            'ordersTotal' => $user->orders()->count(),
            'ordersPending' => $user->orders()->where('status', 'processing')->count(),
            'spent' => $user->orders()->sum('grand_total'),
        ];

        return view('account.dashboard', compact('user', 'recentOrders', 'stats'));
    }
}
