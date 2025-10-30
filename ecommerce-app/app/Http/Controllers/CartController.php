<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function index(CartManager $cart): View
    {
        [$items, $summary] = $cart->getState();

        return view('cart.index', compact('items', 'summary'));
    }

    public function store(Request $request, Product $product, CartManager $cart): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['sometimes', 'integer', 'min:1', 'max:99'],
        ]);

        $quantity = $validated['quantity'] ?? 1;

        if ($product->stock < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Requested quantity exceeds available stock.',
            ]);
        }

        $cart->addProduct($product, $quantity);

        return back()->with('status', 'Product added to cart.');
    }

    public function update(Request $request, Product $product, CartManager $cart): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $quantity = $validated['quantity'];

        if ($product->stock < $quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Requested quantity exceeds available stock.',
            ]);
        }

        $cart->updateProduct($product, $quantity);

        return back()->with('status', 'Cart updated.');
    }

    public function destroy(Product $product, CartManager $cart): RedirectResponse
    {
        $cart->removeProduct($product);

        return back()->with('status', 'Item removed from cart.');
    }
}
