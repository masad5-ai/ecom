<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $productsQuery = Product::with(['images', 'brand', 'category'])->published();

        $category = null;
        $brand = null;

        if ($request->filled('category')) {
            $category = Category::where('slug', $request->string('category'))->firstOrFail();
            $descendantIds = $category->children()->pluck('id');
            $productsQuery->whereIn('category_id', $descendantIds->push($category->id));
        }

        if ($request->filled('brand')) {
            $brand = Brand::where('slug', $request->string('brand'))->firstOrFail();
            $productsQuery->where('brand_id', $brand->id);
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $productsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('sku', 'like', '%' . $search . '%')
                    ->orWhere('excerpt', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('min_price')) {
            $productsQuery->where('price', '>=', (float) $request->string('min_price'));
        }

        if ($request->filled('max_price')) {
            $productsQuery->where('price', '<=', (float) $request->string('max_price'));
        }

        if ($request->filled('sort')) {
            $sort = $request->string('sort');
            $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $column = ltrim($sort, '-');

            match ($column) {
                'price' => $productsQuery->orderBy('price', $direction),
                'name' => $productsQuery->orderBy('name', $direction),
                'newest' => $productsQuery->orderBy('published_at', 'desc'),
                default => $productsQuery->latest('published_at'),
            };
        } else {
            $productsQuery->orderByDesc('published_at');
        }

        $products = $productsQuery->paginate(12)->withQueryString();

        return view('catalog.index', [
            'products' => $products,
            'selectedCategory' => $category,
            'selectedBrand' => $brand,
            'categories' => Category::active()->whereNull('parent_id')->with('children')->orderBy('position')->get(),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
            'filters' => $request->only(['search', 'min_price', 'max_price', 'sort']),
        ]);
    }
}
