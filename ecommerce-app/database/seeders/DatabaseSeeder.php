<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Site Administrator',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('Password!123'),
        ]);

        $vendor = User::factory()->create([
            'name' => 'Vibe Collective',
            'email' => 'vendor@example.com',
            'role' => 'vendor',
            'password' => Hash::make('Password!123'),
        ]);

        $customer = User::factory()->create([
            'name' => 'Ava Summers',
            'email' => 'customer@example.com',
            'role' => 'customer',
            'password' => Hash::make('Password!123'),
        ]);

        $categories = collect([
            ['name' => 'Disposables', 'description' => 'Instant hit devices curated for bold nights out.'],
            ['name' => 'Pods', 'description' => 'Refill pods engineered for precise flavour delivery.'],
            ['name' => 'Devices', 'description' => 'Signature hardware blending Vaperoo performance with Vices style.'],
            ['name' => 'Bundles', 'description' => 'Curated sets inspired by Uncle V mixes for plug-and-play rituals.'],
            ['name' => 'Accessories', 'description' => 'Charge kits, carry sleeves, and maintenance essentials.'],
        ])->map(function (array $data, int $index) {
            $slug = Str::slug($data['name']);

            return Category::factory()->create([
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'],
                'position' => $index,
            ]);
        });

        $brands = Brand::factory()->count(6)->create();

        $products = Product::factory()
            ->count(24)
            ->state(function () use ($categories, $brands, $vendor) {
                return [
                    'category_id' => $categories->random()->id,
                    'brand_id' => $brands->random()->id,
                    'user_id' => $vendor->id,
                ];
            })
            ->create();

        $products->each(function (Product $product) {
            ProductImage::factory()->create([
                'product_id' => $product->id,
                'position' => 0,
                'is_primary' => true,
            ]);

            foreach (range(1, 2) as $position) {
                ProductImage::factory()->create([
                    'product_id' => $product->id,
                    'position' => $position,
                    'is_primary' => false,
                ]);
            }
        });

        Banner::factory()->count(3)->state(function () use ($categories) {
            return [
                'title' => Arr::random([
                    'Glow Season Limited',
                    'After Hours High-Impact',
                    'Weekend Sound System',
                ]),
                'button_label' => 'Shop drop',
                'button_url' => '/shop?category=' . ($categories->random()->slug ?? ''),
            ];
        })->create();

        Page::factory()->count(3)->create();

        $shippingAddress = Address::factory()->for($customer)->create([
            'type' => 'shipping',
            'is_default_shipping' => true,
        ]);

        $billingAddress = Address::factory()->for($customer)->create([
            'type' => 'billing',
            'is_default_billing' => true,
        ]);

        $productsForOrder = $products->shuffle()->take(3);

        $order = Order::create([
            'order_number' => 'ORD-' . Str::upper(Str::random(8)),
            'user_id' => $customer->id,
            'status' => 'processing',
            'payment_status' => 'paid',
            'payment_method' => 'card',
            'billing_address_id' => $billingAddress->id,
            'shipping_address_id' => $shippingAddress->id,
            'subtotal' => 0,
            'discount_total' => 0,
            'tax_total' => 0,
            'shipping_total' => 12.95,
            'grand_total' => 0,
            'currency' => 'AUD',
            'placed_at' => now()->subDays(2),
        ]);

        $subtotal = 0;
        $taxTotal = 0;

        foreach ($productsForOrder as $product) {
            $quantity = rand(1, 3);
            $lineSubtotal = $product->activePrice() * $quantity;
            $lineTax = $lineSubtotal * 0.1;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku,
                'unit_price' => $product->activePrice(),
                'quantity' => $quantity,
                'discount_total' => 0,
                'tax_total' => $lineTax,
                'line_total' => $lineSubtotal,
            ]);

            $subtotal += $lineSubtotal;
            $taxTotal += $lineTax;
        }

        $order->update([
            'subtotal' => round($subtotal, 2),
            'tax_total' => round($taxTotal, 2),
            'grand_total' => round($subtotal + $taxTotal + $order->shipping_total, 2),
        ]);
    }
}
