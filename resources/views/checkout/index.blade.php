@extends('layouts.frontend')

@section('title', 'Checkout')

@section('content')
    <section class="mx-auto max-w-7xl px-4 pt-12 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-semibold text-white">Checkout</h1>
        <p class="mt-2 text-sm text-slate-400">Secure your drop in a few quick steps.</p>

        <div class="mt-8 grid gap-8 lg:grid-cols-3">
            <form method="POST" action="{{ route('checkout.store') }}" class="space-y-6 rounded-3xl border border-white/5 bg-slate-900/60 p-6 lg:col-span-2">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="text-sm">
                        <span class="text-xs uppercase tracking-wide text-slate-400">First name</span>
                        <input name="first_name" value="{{ old('first_name', $user->first_name ?? $user->name ?? '') }}" required class="mt-1 w-full rounded-xl border border-white/10 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-brand-400 focus:ring-brand-400/30">
                    </label>
                    <label class="text-sm">
                        <span class="text-xs uppercase tracking-wide text-slate-400">Last name</span>
                        <input name="last_name" value="{{ old('last_name') }}" required class="mt-1 w-full rounded-xl border border-white/10 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-brand-400 focus:ring-brand-400/30">
                    </label>
                    <label class="text-sm">
                        <span class="text-xs uppercase tracking-wide text-slate-400">Email</span>
                        <input name="email" type="email" value="{{ old('email', $user->email) }}" required class="mt-1 w-full rounded-xl border border-white/10 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-brand-400 focus:ring-brand-400/30">
                    </label>
                    <label class="text-sm">
                        <span class="text-xs uppercase tracking-wide text-slate-400">Phone</span>
                        <input name="phone" value="{{ old('phone', $user->phone) }}" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-brand-400 focus:ring-brand-400/30">
                    </label>
                    <label class="text-sm sm:col-span-2">
                        <span class="text-xs uppercase tracking-wide text-slate-400">Company</span>
                        <input name="company" value="{{ old('company') }}" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-brand-400 focus:ring-brand-400/30">
                    </label>
                    <label class="text-sm sm:col-span-2">
                        <span class="text-xs uppercase tracking-wide text-slate-400">Address line</span>
                        <input name="line_one" value="{{ old('line_one') }}" required class="mt-1 w-full rounded-xl border border-white/10 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-brand-400 focus:ring-brand-400/30">
                    </label>
                    <label class="text-sm sm:col-span-2">
                        <span class="text-xs uppercase tracking-wide text-slate-400">Address line 2</span>
                        <input name="line_two" value="{{ old('line_two') }}" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-brand-400 focus:ring-brand-400/30">
                    </label>
                    <label class="text-sm">
                        <span class="text-xs uppercase tracking-wide text-slate-400">City</span>
                        <input name="city" value="{{ old('city') }}" required class="mt-1 w-full rounded-xl border border-white/10 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-brand-400 focus:ring-brand-400/30">
                    </label>
                    <label class="text-sm">
                        <span class="text-xs uppercase tracking-wide text-slate-400">State</span>
                        <input name="state" value="{{ old('state') }}" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-brand-400 focus:ring-brand-400/30">
                    </label>
                    <label class="text-sm">
                        <span class="text-xs uppercase tracking-wide text-slate-400">Postcode</span>
                        <input name="postcode" value="{{ old('postcode') }}" required class="mt-1 w-full rounded-xl border border-white/10 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-brand-400 focus:ring-brand-400/30">
                    </label>
                    <label class="text-sm">
                        <span class="text-xs uppercase tracking-wide text-slate-400">Country (ISO)</span>
                        <input name="country" value="{{ old('country', 'AU') }}" maxlength="2" required class="mt-1 w-full rounded-xl border border-white/10 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-brand-400 focus:ring-brand-400/30">
                    </label>
                    <label class="text-sm sm:col-span-2">
                        <span class="text-xs uppercase tracking-wide text-slate-400">Order notes</span>
                        <textarea name="notes" rows="3" class="mt-1 w-full rounded-2xl border border-white/10 bg-slate-900/80 px-3 py-2 text-sm text-white focus:border-brand-400 focus:ring-brand-400/30">{{ old('notes') }}</textarea>
                    </label>
                </div>

                <button class="w-full rounded-full bg-brand-500 px-4 py-3 text-sm font-semibold uppercase tracking-wide text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-400">Place order</button>
            </form>

            <aside class="space-y-6 rounded-3xl border border-white/5 bg-slate-900/60 p-6">
                <h2 class="text-lg font-semibold text-white">Order summary</h2>
                <ul class="space-y-4 text-sm text-slate-300">
                    @foreach($items as $item)
                        <li class="flex items-center justify-between gap-4">
                            <div>
                                <p class="font-semibold text-white">{{ $item['product']->name }}</p>
                                <p class="text-xs text-slate-500">Qty {{ $item['quantity'] }}</p>
                            </div>
                            <p class="font-semibold text-white">${{ number_format($item['subtotal'], 2) }}</p>
                        </li>
                    @endforeach
                </ul>
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
                <p class="text-xs text-slate-500">By placing this order you confirm you are 18+ and purchasing within Australian regulations.</p>
            </aside>
        </div>
    </section>
@endsection
