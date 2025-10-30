<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CartManager
{
    public function __construct(protected Request $request)
    {
    }

    public function addProduct(Product $product, int $quantity): void
    {
        $user = $this->request->user();

        if ($user) {
            CartItem::updateOrCreate(
                ['user_id' => $user->id, 'product_id' => $product->id],
                [
                    'quantity' => $quantity,
                    'unit_price' => $product->activePrice(),
                ],
            );

            $this->syncSessionFromDatabase();

            return;
        }

        $items = collect($this->request->session()->get('cart.items', []));
        $items[$product->id] = [
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->activePrice(),
        ];

        $this->request->session()->put('cart.items', $items->toArray());
    }

    public function updateProduct(Product $product, int $quantity): void
    {
        $user = $this->request->user();

        if ($user) {
            CartItem::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->update([
                    'quantity' => $quantity,
                    'unit_price' => $product->activePrice(),
                ]);

            $this->syncSessionFromDatabase();

            return;
        }

        $items = collect($this->request->session()->get('cart.items', []));

        if ($items->has($product->id)) {
            $item = $items[$product->id];
            $item['quantity'] = $quantity;
            $items[$product->id] = $item;
        }

        $this->request->session()->put('cart.items', $items->toArray());
    }

    public function removeProduct(Product $product): void
    {
        $user = $this->request->user();

        if ($user) {
            CartItem::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->delete();

            $this->syncSessionFromDatabase();

            return;
        }

        $items = collect($this->request->session()->get('cart.items', []));
        $items->forget($product->id);
        $this->request->session()->put('cart.items', $items->toArray());
    }

    public function clear(): void
    {
        $user = $this->request->user();

        if ($user) {
            CartItem::where('user_id', $user->id)->delete();
        }

        $this->request->session()->forget('cart.items');
    }

    public function getState(): array
    {
        $items = $this->loadItems();
        $summary = $this->buildSummary($items);

        return [$items, $summary];
    }

    protected function loadItems(): Collection
    {
        $user = $this->request->user();
        $items = collect();

        if ($user) {
            $dbItems = CartItem::with('product.images')
                ->where('user_id', $user->id)
                ->get()
                ->map(function (CartItem $item) {
                    return [
                        'product' => $item->product,
                        'quantity' => $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'subtotal' => (float) $item->unit_price * $item->quantity,
                    ];
                });

            $items = $items->merge($dbItems);
            $this->syncSessionFromDatabase();
        } else {
            $sessionItems = collect($this->request->session()->get('cart.items', []));

            if ($sessionItems->isNotEmpty()) {
                $products = Product::with('images')
                    ->whereIn('id', $sessionItems->pluck('product_id'))
                    ->get()
                    ->keyBy('id');

                $items = $sessionItems->map(function ($item) use ($products) {
                    $product = $products->get($item['product_id']);

                    if (! $product) {
                        return null;
                    }

                    $unit = $item['unit_price'] ?? $product->activePrice();

                    return [
                        'product' => $product,
                        'quantity' => (int) $item['quantity'],
                        'unit_price' => (float) $unit,
                        'subtotal' => (float) $unit * (int) $item['quantity'],
                    ];
                })->filter();
            }
        }

        return $items;
    }

    protected function buildSummary(Collection $items): array
    {
        $subtotal = $items->sum('subtotal');
        $shipping = $subtotal >= 150 ? 0 : 12.95;
        $tax = $subtotal * 0.1;

        return [
            'subtotal' => round($subtotal, 2),
            'shipping' => round($shipping, 2),
            'tax' => round($tax, 2),
            'total' => round($subtotal + $shipping + $tax, 2),
        ];
    }

    protected function syncSessionFromDatabase(): void
    {
        $user = $this->request->user();

        if (! $user) {
            return;
        }

        $dbItems = CartItem::where('user_id', $user->id)->get()->mapWithKeys(function (CartItem $item) {
            return [
                $item->product_id => [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                ],
            ];
        });

        $this->request->session()->put('cart.items', $dbItems->toArray());
    }
}
