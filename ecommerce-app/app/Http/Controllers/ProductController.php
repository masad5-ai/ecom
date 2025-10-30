<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        $product->load(['images', 'brand', 'category']);

        $relatedProducts = Product::published()
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->getKey())
            ->with('images')
            ->take(8)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
