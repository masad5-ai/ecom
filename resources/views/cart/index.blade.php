@extends('layouts.frontend')

@section('title', 'Your Cart')

@section('content')
    <section class="mx-auto max-w-7xl px-4 pt-12 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-semibold text-white">Bag</h1>
        <p class="mt-2 text-sm text-slate-400">Review your selections before we prep them for dispatch.</p>

        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            <div class="space-y-4 lg:col-span-2">
                @forelse($items as $item)
                    <article class="flex flex-col gap-4 rounded-3xl border border-white/5 bg-slate-900/60 p-5 shadow-lg shadow-black/20 sm:flex-row sm:items-center">
                        <div class="h-32 w-32 flex-shrink-0 overflow-hidden rounded-2xl border border-white/5 bg-slate-900">
                            @if($item['product']?->images?->isNotEmpty())
                                <img src="{{ asset($item['product']->images->first()->path) }}" alt="{{ $item['product']->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center text-slate-500">Image</div>
                            @endif
                        </div>
                        <div class="flex flex-1 flex-col gap-3">
                            <div class="flex flex-wrap items-baseline justify-between gap-2">
                                <h2 class="text-lg font-semibold text-white">{{ $item['product']->name }}</h2>
                                <p class="text-sm font-semibold text-white">${{ number_format($item['subtotal'], 2) }}</p>
                            </div>
                            <p class="text-xs uppercase tracking-wide text-slate-400">{{ $item['product']->brand?->name ?? 'Signature' }} &middot; SKU {{ $item['product']->sku }}</p>
                            <div class="flex flex-wrap items-center gap-3">
                                <form method="POST" action="{{ route('cart.update', $item['product']) }}" class="flex flex-wrap items-center gap-3">
                                    @csrf
                                    @method('PATCH')
                                    <label class="flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs text-white">
                                        Qty
                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="99" class="w-14 rounded-full border border-white/10 bg-slate-900/80 px-2 py-1 text-center text-white focus:border-brand-400 focus:ring-brand-400/30">
                                    </label>
                                    <button class="rounded-full border border-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-white/10">Update</button>
                                </form>
                                <form method="POST" action="{{ route('cart.destroy', $item['product']) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-full border border-white/10 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-red-300 transition hover:bg-red-500/10">Remove</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-white/5 bg-slate-900/60 p-8 text-center text-slate-300">
                        <p class="text-lg font-semibold text-white">Your cart is empty</p>
                        <p class="mt-2 text-sm text-slate-400">Head back to the shop to explore curated drops.</p>
                        <a href="{{ route('catalog.index') }}" class="mt-4 inline-flex items-center gap-2 rounded-full bg-brand-500 px-6 py-2 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-brand-400">Shop now &rarr;</a>
                    </div>
                @endforelse
            </div>

            <aside class="space-y-6 rounded-3xl border border-white/5 bg-slate-900/60 p-6">
                <h2 class="text-lg font-semibold text-white">Summary</h2>
                <dl class="space-y-3 text-sm text-slate-300">
                    <div class="flex items-center justify-between">
                        <dt>Subtotal</dt>
                        <dd>${{ number_format($summary['subtotal'], 2) }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>Shipping</dt>
                        <dd>${{ number_format($summary['shipping'], 2) }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt>Tax</dt>
                        <dd>${{ number_format($summary['tax'], 2) }}</dd>
                    </div>
                    <div class="flex items-center justify-between text-base font-semibold text-white">
                        <dt>Total</dt>
                        <dd>${{ number_format($summary['total'], 2) }}</dd>
                    </div>
                </dl>

                <a href="{{ route('checkout.index') }}" class="block rounded-full bg-brand-500 px-4 py-3 text-center text-sm font-semibold uppercase tracking-wide text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-400">Proceed to checkout</a>
                <p class="text-xs text-slate-500">Orders $150+ enjoy complimentary express shipping Australia-wide.</p>
            </aside>
        </div>
    </section>
@endsection
