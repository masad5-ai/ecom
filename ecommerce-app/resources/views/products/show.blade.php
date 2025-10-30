@extends('layouts.frontend')

@php
    use Illuminate\Support\Str;
@endphp

@section('title', $product->name)

@section('content')
    <section class="mx-auto max-w-7xl px-4 pt-12 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-2">
            <div class="space-y-4">
                @php
                    $primaryImage = $product->images->first();
                    $primaryImageUrl = $primaryImage ? (Str::startsWith($primaryImage->path, ['http://', 'https://']) ? $primaryImage->path : asset($primaryImage->path)) : null;
                @endphp
                <div class="relative overflow-hidden rounded-3xl border border-white/5 bg-slate-900/60 shadow-xl shadow-black/30">
                    @if($product->images->isNotEmpty())
                        <img src="{{ $primaryImageUrl }}" alt="{{ $product->name }}" class="w-full object-cover">
                    @else
                        <div class="flex h-96 items-center justify-center text-slate-500">Image coming soon</div>
                    @endif
                    <span class="absolute left-4 top-4 rounded-full bg-brand-500/90 px-4 py-1 text-xs font-semibold uppercase tracking-wide text-white shadow-lg shadow-brand-500/40">
                        {{ $product->brand?->name ?? 'Signature blend' }}
                    </span>
                </div>
                @if($product->images->count() > 1)
                    <div class="flex gap-3 overflow-x-auto pb-2">
                        @foreach($product->images->skip(1) as $image)
                            @php
                                $thumbUrl = Str::startsWith($image->path, ['http://', 'https://']) ? $image->path : asset($image->path);
                            @endphp
                            <img src="{{ $thumbUrl }}" alt="{{ $product->name }}" class="h-20 w-20 flex-shrink-0 rounded-xl border border-white/10 object-cover">
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="space-y-6 rounded-3xl border border-white/5 bg-slate-900/60 p-8">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-500">{{ $product->category?->name ?? 'Curated release' }}</p>
                    <h1 class="text-3xl font-semibold text-white">{{ $product->name }}</h1>
                    <p class="text-sm text-slate-400">
                        {!! nl2br(e($product->tagline ?? $product->excerpt ?? 'A flagship release blending the best of Vaperoo variety, Uncle V premium touch, and Vices Oz bold character.')) !!}
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <p class="text-3xl font-semibold text-white">${{ number_format($product->activePrice(), 2) }}</p>
                    @if($product->sale_price)
                        <p class="text-sm text-slate-500 line-through">${{ number_format($product->price, 2) }}</p>
                    @endif
                    <span class="rounded-full border border-green-400/30 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-green-300">
                        {{ $product->stock > 0 ? 'In Stock' : 'Restocking' }}
                    </span>
                </div>

                <form method="POST" action="{{ route('cart.store', $product) }}" class="flex flex-wrap items-center gap-3">
                    @csrf
                    <label class="flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-white">
                        Qty
                        <input type="number" name="quantity" value="1" min="1" max="{{ max(1, $product->stock) }}" class="w-16 rounded-full border border-white/10 bg-slate-900/80 px-2 py-1 text-center text-white focus:border-brand-400 focus:ring-brand-400/30">
                    </label>
                    <button class="rounded-full bg-brand-500 px-6 py-2 text-sm font-semibold uppercase tracking-wide text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-400">Add to cart</button>
                    <a href="{{ route('catalog.index', ['brand' => $product->brand?->slug]) }}" class="rounded-full border border-white/10 px-6 py-2 text-sm font-semibold uppercase tracking-wide text-white transition hover:bg-white/10">More from this brand</a>
                </form>

                <div class="space-y-4 text-sm text-slate-300">
                    <div>
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Profile</h2>
                        <p class="mt-2 leading-relaxed">{!! nl2br(e($product->description ?? 'Hand-picked components, meticulously balanced flavours, and hardware compatibility inspired by Australia’s favourite vape destinations.')) !!}</p>
                    </div>
                    @if($product->attributes)
                        <div>
                            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-400">Specs</h2>
                            <ul class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach($product->attributes as $label => $value)
                                    <li class="rounded-2xl border border-white/5 bg-white/5 px-4 py-3">
                                        <p class="text-xs uppercase tracking-wide text-slate-400">{{ Str::headline($label) }}</p>
                                        <p class="text-sm text-white">{{ is_array($value) ? implode(', ', $value) : $value }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if($relatedProducts->isNotEmpty())
        <section class="mx-auto mt-16 max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-white">You might also like</h2>
                <a href="{{ route('catalog.index', ['category' => $product->category?->slug]) }}" class="text-xs font-semibold uppercase tracking-wide text-brand-300 transition hover:text-brand-200">View category →</a>
            </div>
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($relatedProducts as $related)
                    <article class="group overflow-hidden rounded-3xl border border-white/5 bg-slate-900/60 shadow-lg shadow-black/20 transition hover:-translate-y-1 hover:border-white/10">
                        <div class="relative aspect-[4/5] bg-slate-900">
                            @if($related->images->isNotEmpty())
                                @php
                                    $relatedUrl = Str::startsWith($related->images->first()->path, ['http://', 'https://']) ? $related->images->first()->path : asset($related->images->first()->path);
                                @endphp
                                <img src="{{ $relatedUrl }}" alt="{{ $related->name }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-105 group-hover:opacity-90">
                            @endif
                        </div>
                        <div class="space-y-2 p-4">
                            <p class="text-xs uppercase tracking-wide text-slate-400">{{ $related->brand?->name ?? 'Signature' }}</p>
                            <h3 class="text-base font-semibold text-white line-clamp-1">{{ $related->name }}</h3>
                            <p class="text-sm font-semibold text-white">${{ number_format($related->activePrice(), 2) }}</p>
                            <a href="{{ route('products.show', $related) }}" class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-brand-300 transition hover:text-brand-200">View drop &rarr;</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif
@endsection
