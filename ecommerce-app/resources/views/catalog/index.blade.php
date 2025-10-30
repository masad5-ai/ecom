@extends('layouts.frontend')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', ($selectedCategory->name ?? $selectedBrand->name ?? 'Shop the Collection') . ' – Catalogue')

@section('content')
    <section class="mx-auto max-w-7xl px-4 pt-12 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold text-white">
                    {{ $selectedCategory->name ?? $selectedBrand->name ?? 'Discover premium vapes & accessories' }}
                </h1>
                <p class="mt-2 text-sm text-slate-400">
                    Filtered from Vaperoo's massive catalogue, refined with Uncle V's navigation simplicity, and injected with Vices Oz's nightlife vibe.
                </p>
            </div>
            <form method="GET" class="flex flex-wrap items-center gap-3 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-200">
                <input type="hidden" name="category" value="{{ $selectedCategory->slug ?? '' }}">
                <input type="hidden" name="brand" value="{{ $selectedBrand->slug ?? '' }}">
                <label class="flex items-center gap-2 text-xs uppercase tracking-wide text-slate-400">
                    Sort by
                    <select name="sort" class="rounded-full border border-white/10 bg-slate-950/60 px-3 py-1 text-sm text-slate-100 focus:border-brand-400 focus:ring-brand-400/30">
                        <option value="-newest" @selected(($filters['sort'] ?? '') === '-newest')>Newest</option>
                        <option value="name" @selected(($filters['sort'] ?? '') === 'name')>Name (A-Z)</option>
                        <option value="-price" @selected(($filters['sort'] ?? '') === '-price')>Price: High to Low</option>
                        <option value="price" @selected(($filters['sort'] ?? '') === 'price')>Price: Low to High</option>
                    </select>
                </label>
                <button class="rounded-full bg-brand-500 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-400">Apply</button>
            </form>
        </div>

        <div class="mt-10 grid gap-8 lg:grid-cols-4">
            <aside class="space-y-8 rounded-3xl border border-white/5 bg-slate-900/60 p-6">
                <form method="GET" class="space-y-6">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">Search</label>
                        <div class="mt-2 flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5">
                            <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Flavour or SKU" class="w-full bg-transparent text-sm text-white placeholder:text-slate-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Categories</p>
                        <ul class="mt-3 space-y-2 text-sm text-slate-300">
                            @foreach($categories as $category)
                                <li>
                                    <label class="flex cursor-pointer items-center gap-2 rounded-xl px-3 py-2 transition hover:bg-white/5">
                                        <input type="radio" name="category" value="{{ $category->slug }}" @checked(optional($selectedCategory)->id === $category->id) class="rounded border-white/10 bg-slate-900 text-brand-500 focus:ring-brand-400">
                                        <span>{{ $category->name }}</span>
                                    </label>
                                    @if($category->children->isNotEmpty())
                                        <ul class="mt-1 space-y-1 pl-6 text-xs text-slate-400">
                                            @foreach($category->children as $child)
                                                <li>
                                                    <label class="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-1 transition hover:bg-white/5">
                                                        <input type="radio" name="category" value="{{ $child->slug }}" @checked(optional($selectedCategory)->id === $child->id) class="rounded border-white/10 bg-slate-900 text-brand-500 focus:ring-brand-400">
                                                        <span>{{ $child->name }}</span>
                                                    </label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                            <li>
                                <label class="flex cursor-pointer items-center gap-2 rounded-xl px-3 py-2 text-xs uppercase tracking-wide text-slate-500 transition hover:bg-white/5">
                                    <input type="radio" name="category" value="" class="rounded border-white/10 bg-slate-900 text-brand-500 focus:ring-brand-400">
                                    Clear filter
                                </label>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Brands</p>
                        <select name="brand" class="mt-2 w-full rounded-xl border border-white/10 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-brand-400 focus:ring-brand-400/30">
                            <option value="">All brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->slug }}" @selected(optional($selectedBrand)->id === $brand->id)>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <label class="space-y-1">
                            <span class="text-xs uppercase tracking-wide text-slate-400">Min price</span>
                            <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}" class="w-full rounded-xl border border-white/10 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-brand-400 focus:ring-brand-400/30" placeholder="0">
                        </label>
                        <label class="space-y-1">
                            <span class="text-xs uppercase tracking-wide text-slate-400">Max price</span>
                            <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" class="w-full rounded-xl border border-white/10 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-brand-400 focus:ring-brand-400/30" placeholder="500">
                        </label>
                    </div>

                    <button class="w-full rounded-full bg-brand-500 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-400">Update results</button>
                </form>
            </aside>

            <div class="lg:col-span-3">
                <p class="text-xs uppercase tracking-wide text-slate-400">Showing {{ $products->firstItem() }} – {{ $products->lastItem() }} of {{ $products->total() }} results</p>

                <div class="mt-6 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse($products as $product)
                        <article class="group flex flex-col overflow-hidden rounded-3xl border border-white/5 bg-slate-900/60 shadow-lg shadow-black/20 transition hover:-translate-y-1 hover:border-white/10">
                            <div class="relative aspect-[4/5] bg-slate-900">
                                @if($product->images->isNotEmpty())
                                    <img src="{{ asset($product->images->first()->path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105 group-hover:opacity-90">
                                @else
                                    <div class="flex h-full w-full items-center justify-center bg-slate-900/80 text-slate-500">Image coming soon</div>
                                @endif
                                @if($product->is_featured)
                                    <span class="absolute left-4 top-4 rounded-full bg-brand-500/90 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white shadow-lg shadow-brand-500/40">Featured</span>
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col gap-3 p-5">
                                <div class="flex items-center justify-between text-xs text-slate-400">
                                    <span>{{ $product->brand?->name ?? 'Signature' }}</span>
                                    <span>#{{ $product->sku }}</span>
                                </div>
                                <h2 class="text-lg font-semibold text-white line-clamp-1">{{ $product->name }}</h2>
                                <p class="text-sm text-slate-400 line-clamp-2">{{ $product->excerpt ?? Str::limit(strip_tags($product->description), 120) }}</p>
                                <div class="mt-auto flex items-center justify-between">
                                    <div>
                                        <p class="text-xl font-semibold text-white">${{ number_format($product->activePrice(), 2) }}</p>
                                        @if($product->sale_price)
                                            <p class="text-xs text-slate-500 line-through">${{ number_format($product->price, 2) }}</p>
                                        @endif
                                    </div>
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('cart.store', $product) }}">
                                            @csrf
                                            <button class="rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-white/20">Add</button>
                                        </form>
                                        <a href="{{ route('products.show', $product) }}" class="rounded-full border border-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-white/10">View</a>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-3xl border border-white/5 bg-slate-900/60 p-10 text-center text-slate-300">
                            <p class="text-lg font-semibold text-white">Nothing here… yet.</p>
                            <p class="mt-2 text-sm text-slate-400">Adjust filters or browse our featured categories to discover curated drops.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection

