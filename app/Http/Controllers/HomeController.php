<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $banners = Banner::active()->take(5)->get();
        $featuredCategories = Category::active()
            ->whereNull('parent_id')
            ->orderBy('position')
            ->take(6)
            ->get();

        $featuredProducts = Product::with(['images', 'brand'])
            ->published()
            ->orderByDesc('published_at')
            ->take(12)
            ->get();

        $latestPages = Page::published()->latest('published_at')->take(3)->get();

        return view('home.index', compact('banners', 'featuredCategories', 'featuredProducts', 'latestPages'));
    }
}
