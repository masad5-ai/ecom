<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index(Request $request, CartManager $cart): View|RedirectResponse
    {
        [$items, $summary] = $cart->getState();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        return view('checkout.index', [
            'items' => $items,
            'summary' => $summary,
            'user' => $request->user(),
        ]);
    }

    public function store(Request $request, CartManager $cart): RedirectResponse
    {
        [$items, $summary] = $cart->getState();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:150'],
            'line_one' => ['required', 'string', 'max:255'],
            'line_two' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postcode' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'size:2'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();

        $order = DB::transaction(function () use ($user, $items, $summary, $validated) {
            $shippingAddress = Address::create([
                'user_id' => $user?->id,
                'type' => 'shipping',
                'label' => 'Shipping Address',
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'company' => $validated['company'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'],
                'line_one' => $validated['line_one'],
                'line_two' => $validated['line_two'] ?? null,
                'city' => $validated['city'],
                'state' => $validated['state'] ?? null,
                'postcode' => $validated['postcode'],
                'country' => strtoupper($validated['country']),
            ]);

            $billingAddress = Address::create([
                'user_id' => $user?->id,
                'type' => 'billing',
                'label' => 'Billing Address',
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'company' => $validated['company'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'],
                'line_one' => $validated['line_one'],
                'line_two' => $validated['line_two'] ?? null,
                'city' => $validated['city'],
                'state' => $validated['state'] ?? null,
                'postcode' => $validated['postcode'],
                'country' => strtoupper($validated['country']),
            ]);

            $order = Order::create([
                'order_number' => 'ORD-' . Str::upper(Str::random(8)),
                'user_id' => $user?->id,
                'status' => 'processing',
                'payment_status' => 'pending',
                'billing_address_id' => $billingAddress->id,
                'shipping_address_id' => $shippingAddress->id,
                'subtotal' => $summary['subtotal'],
                'discount_total' => 0,
                'tax_total' => $summary['tax'],
                'shipping_total' => $summary['shipping'],
                'grand_total' => $summary['total'],
                'currency' => 'AUD',
                'notes' => $validated['notes'] ?? null,
                'placed_at' => now(),
            ]);

            foreach ($items as $item) {
                /** @var \App\Models\Product $product */
                $product = $item['product'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'unit_price' => $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'discount_total' => 0,
                    'tax_total' => round($item['unit_price'] * $item['quantity'] * 0.1, 2),
                    'line_total' => $item['subtotal'],
                ]);

                $product->decrement('stock', $item['quantity']);
            }

            return $order;
        });

        $cart->clear();

        return redirect()
            ->route('account.dashboard')
            ->with('status', "Order {$order->order_number} placed successfully. Once payment is confirmed, we'll start preparing your items.");
    }
}
