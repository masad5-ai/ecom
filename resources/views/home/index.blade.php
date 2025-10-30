@extends('layouts.frontend')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', 'Modern Vape Marketplace')

@section('content')
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(35,160,148,0.25),_transparent_60%)]"></div>
        <div class="relative mx-auto flex max-w-7xl flex-col gap-14 px-4 py-16 sm:px-6 lg:px-8 lg:flex-row lg:items-center">
            <div class="max-w-xl space-y-6">
                <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-1 text-xs font-semibold uppercase tracking-[0.3em] text-brand-300">
                    Next-gen devices · premium flavours · curated bundles
                </div>
                <h1 class="text-4xl font-bold leading-tight text-white sm:text-5xl">
                    Ignite your flavour journey with an Aussie-first, concierge-style vape experience.
                </h1>
                <p class="text-lg text-slate-300">
                    Inspired by Vaperoo’s rich catalogue, Uncle V’s clean navigation, and Vices Oz’s nightlife energy—our platform brings the best of all worlds into a single immersive destination.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('catalog.index') }}" class="rounded-full bg-brand-500 px-6 py-3 text-sm font-semibold uppercase tracking-wide text-white shadow-lg shadow-brand-500/40 transition hover:bg-brand-400">
                        Shop Collection
                    </a>
                    <a href="{{ route('catalog.index', ['category' => 'disposables']) }}" class="rounded-full border border-white/10 px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/10">
                        Explore Disposables
                    </a>
                </div>
                <dl class="grid gap-6 sm:grid-cols-3">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Dispatch</dt>
                        <dd class="text-lg font-semibold text-white">48hr AUS-wide</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Flavours curated</dt>
                        <dd class="text-lg font-semibold text-white">{{ number_format($featuredProducts->count() * 12) }}+</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-400">Members</dt>
                        <dd class="text-lg font-semibold text-white">Join the inner circle</dd>
                    </div>
                </dl>
            </div>
            <div class="relative flex-1">
                <div class="relative h-[420px] rounded-[32px] border border-white/10 bg-gradient-to-br from-slate-900 via-slate-800/70 to-slate-900 shadow-2xl shadow-black/40 backdrop-blur">
                    <div class="absolute inset-0 overflow-hidden rounded-[32px]">
                        <div class="absolute -left-8 top-12 h-80 w-80 rounded-full bg-[radial-gradient(circle,_rgba(59,130,246,0.3)_0%,_transparent_70%)]"></div>
                        <div class="absolute right-2 bottom-6 h-56 w-56 rounded-full bg-[radial-gradient(circle,_rgba(244,114,182,0.35)_0%,_transparent_70%)]"></div>
                    </div>
                    <div class="relative flex h-full flex-col justify-between p-10">
                        <div>
                            <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Members Exclusive</p>
                            <h2 class="mt-3 text-2xl font-semibold text-white">Nebula Nightfall Bundle</h2>
                            <p class="mt-2 text-sm text-slate-300">A curated trio featuring Vaperoo’s hero disposables, Uncle V’s limited pods, and Vices’ signature night series.</p>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-xs text-slate-300">
                                <span>Limited drop</span>
                                <span>Ships with temperature-safe kit</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-3xl font-bold text-white">$149.95</span>
                                <a href="{{ route('catalog.index', ['category' => 'bundles']) }}" class="rounded-full bg-white/10 px-5 py-2 text-sm font-semibold text-white transition hover:bg-white/20">Secure Bundle</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if($banners->isNotEmpty())
        <section class="mx-auto mt-10 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 md:grid-cols-{{ min(3, $banners->count()) }}">
                @foreach($banners as $banner)
                    <article class="group relative overflow-hidden rounded-3xl border border-white/5 bg-slate-900/30 p-8 shadow-lg shadow-black/30 transition hover:-translate-y-1 hover:border-white/10">
                        <div class="absolute inset-0 bg-gradient-to-br from-white/5 via-transparent to-black/40"></div>
                        <div class="relative space-y-4">
                            <h3 class="text-xl font-semibold text-white">{{ $banner->title }}</h3>
                            <p class="text-sm text-slate-300">{{ $banner->subtitle }}</p>
                            @if($banner->button_url)
                                <a href="{{ $banner->button_url }}" class="inline-flex items-center gap-2 text-sm font-semibold text-brand-300 transition hover:text-brand-200">
                                    {{ $banner->button_label ?? 'Discover drop' }}
                                    <span aria-hidden="true">→</span>
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="mx-auto mt-16 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-white">Spotlight categories</h2>
                <p class="text-sm text-slate-400">A fusion of Vaperoo’s breadth, Uncle V’s curation, and Vices Oz’s nightlife energy.</p>
            </div>
            <a href="{{ route('catalog.index') }}" class="text-sm font-semibold text-brand-300 transition hover:text-brand-200">View full menu →</a>
        </div>
        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($featuredCategories as $category)
                <a href="{{ route('catalog.index', ['category' => $category->slug]) }}" class="group relative overflow-hidden rounded-3xl border border-white/5 bg-slate-900/50 p-6 shadow-lg shadow-black/20 transition hover:-translate-y-1 hover:border-white/10">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(35,160,148,0.18),_transparent_70%)] opacity-0 transition group-hover:opacity-100"></div>
                    <div class="relative space-y-3">
                        <span class="inline-flex items-center rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-brand-300">Curated</span>
                        <h3 class="text-xl font-semibold text-white">{{ $category->name }}</h3>
                        <p class="text-sm text-slate-300 line-clamp-3">{{ $category->description ?? 'Hand-picked flavours and devices to elevate the ritual.' }}</p>
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span>{{ $category->products()->published()->count() }} products</span>
                            <span class="text-brand-300 transition group-hover:text-brand-200">Explore →</span>
                        </div>
                    </div>
                </a>
            @empty
                <p class="col-span-full rounded-3xl border border-white/5 bg-slate-900/50 p-6 text-slate-300">Stay tuned—curated categories are loading shortly.</p>
            @endforelse
        </div>
    </section>

    <section class="mx-auto mt-16 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-white">Latest arrivals</h2>
                <p class="text-sm text-slate-400">Weekly drops blending the premium range of Vaperoo, the boutique finds of Uncle V, and the nightlife signature of Vices Oz.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('catalog.index', ['sort' => '-newest']) }}" class="rounded-full border border-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-300 transition hover:bg-white/10">Newest</a>
                <a href="{{ route('catalog.index', ['sort' => '-price']) }}" class="rounded-full border border-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-300 transition hover:bg-white/10">Premium</a>
                <a href="{{ route('catalog.index', ['sort' => 'price']) }}" class="rounded-full border border-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-slate-300 transition hover:bg-white/10">Value</a>
            </div>
        </div>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($featuredProducts as $product)
                <article class="group flex flex-col overflow-hidden rounded-3xl border border-white/5 bg-slate-900/50 shadow-lg shadow-black/30 transition hover:-translate-y-1 hover:border-white/10">
                    <div class="relative aspect-[4/5] overflow-hidden bg-slate-900">
                        @if($product->images->isNotEmpty())
                            <img src="{{ asset($product->images->first()->path) }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105 group-hover:opacity-90">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-slate-900/80 text-slate-500">Image coming soon</div>
                        @endif
                        <div class="absolute left-4 top-4 rounded-full bg-brand-500/90 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white shadow-lg shadow-brand-500/40">
                            {{ $product->brand?->name ?? 'Signature' }}
                        </div>
                    </div>
                    <div class="flex flex-1 flex-col gap-3 p-6">
                        <h3 class="text-lg font-semibold text-white line-clamp-1">{{ $product->name }}</h3>
                        <p class="text-sm text-slate-400 line-clamp-2">{{ $product->excerpt ?? Str::limit(strip_tags($product->description), 90) }}</p>
                        <div class="mt-auto flex items-center justify-between">
                            <div>
                                <p class="text-xl font-semibold text-white">${{ number_format($product->activePrice(), 2) }}</p>
                                @if($product->sale_price)
                                    <p class="text-xs text-slate-400 line-through">${{ number_format($product->price, 2) }}</p>
                                @endif
                            </div>
                            <a href="{{ route('products.show', $product) }}" class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-white/20">
                                View
                                <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <p class="col-span-full rounded-3xl border border-white/5 bg-slate-900/50 p-6 text-slate-300">Products are being curated. Check back soon for fresh drops.</p>
            @endforelse
        </div>
    </section>

    <section class="mx-auto mt-16 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 lg:grid-cols-2">
            <div class="overflow-hidden rounded-3xl border border-white/5 bg-slate-900/50">
                <div class="border-b border-white/5 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white">Club Nebula perks</h3>
                </div>
                <div class="divide-y divide-white/5">
                    <div class="flex items-start gap-4 px-6 py-5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-500/20 text-brand-300">01</span>
                        <div>
                            <p class="text-sm font-semibold text-white">Same-day packing on premium lines</p>
                            <p class="text-sm text-slate-400">Orders placed before 2pm AEST leave the warehouse within hours—mirroring Vaperoo’s lightning fulfilment.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 px-6 py-5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-500/20 text-brand-300">02</span>
                        <div>
                            <p class="text-sm font-semibold text-white">Signature flavour concierge</p>
                            <p class="text-sm text-slate-400">Personalised flavour matchups take a cue from Uncle V’s boutique curation, ensuring every hit feels intentional.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 px-6 py-5">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-500/20 text-brand-300">03</span>
                        <div>
                            <p class="text-sm font-semibold text-white">Nightlife limited editions</p>
                            <p class="text-sm text-slate-400">Exclusive collabs and glow-series hardware drop monthly, inspired by Vices Oz’s nightlife DNA.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="overflow-hidden rounded-3xl border border-white/5 bg-slate-900/50">
                <div class="border-b border-white/5 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white">Journal &amp; flavour forecasts</h3>
                </div>
                <div class="divide-y divide-white/5">
                    @forelse($latestPages as $page)
                        <article class="px-6 py-5 transition hover:bg-white/5">
                            <a href="#" class="space-y-2">
                                <p class="text-xs uppercase tracking-wide text-slate-400">{{ $page->published_at?->format('d M Y') ?? 'Coming soon' }}</p>
                                <h4 class="text-base font-semibold text-white">{{ $page->title }}</h4>
                                <p class="text-sm text-slate-400 line-clamp-2">{{ $page->excerpt ?? Str::limit(strip_tags($page->body), 120) }}</p>
                            </a>
                        </article>
                    @empty
                        <p class="px-6 py-5 text-sm text-slate-400">Editorial content is brewing. Expect flavour deep dives, device tuning tips, and nightlife pairings soon.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
