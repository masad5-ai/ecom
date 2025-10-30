<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = $request->user()->products()
            ->withCount('orderItems')
            ->latest()
            ->paginate(12);

        return view('vendor.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('vendor.products.create', [
            'categories' => Category::active()->orderBy('name')->get(),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $this->validateProduct($request);

        $product = $user->products()->create($data);

        $this->syncImages($product, $request->string('image_urls'));

        return redirect()->route('vendor.products.edit', $product)->with('status', 'Product created successfully.');
    }

    public function edit(Request $request, Product $product): View
    {
        abort_unless($product->user_id === $request->user()->id || $request->user()->isAdmin(), 403);

        $product->load('images');

        return view('vendor.products.edit', [
            'product' => $product,
            'categories' => Category::active()->orderBy('name')->get(),
            'brands' => Brand::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->user_id === $request->user()->id || $request->user()->isAdmin(), 403);

        $data = $this->validateProduct($request, $product->id);

        $product->update($data);

        $this->syncImages($product, $request->string('image_urls'));

        return back()->with('status', 'Product updated successfully.');
    }

    protected function validateProduct(Request $request, ?int $productId = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku,' . $productId],
            'category_id' => ['nullable', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:draft,published,archived'],
            'is_featured' => ['sometimes', 'boolean'],
            'attributes' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
            'published_at' => ['nullable', 'date'],
            'image_urls' => ['nullable', 'string'],
        ]);

        $data['is_featured'] = $request->boolean('is_featured');
        $data['attributes'] = $request->input('attributes', []);
        $data['metadata'] = $request->input('metadata', []);

        return $data;
    }

    protected function syncImages(Product $product, ?string $imageUrls): void
    {
        if ($imageUrls === null) {
            return;
        }

        $paths = collect(preg_split('/\r\n|\r|\n/', trim($imageUrls)))
            ->filter()
            ->values();

        $existing = $product->images()->pluck('path')->all();

        foreach ($paths as $index => $path) {
            if (! in_array($path, $existing, true)) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'position' => $index,
                    'is_primary' => $index === 0,
                ]);
            }
        }
    }
}
