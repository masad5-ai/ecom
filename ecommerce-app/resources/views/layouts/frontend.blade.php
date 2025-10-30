<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ecommerce') }} – @yield('title', 'Premium Vapes & Accessories')</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --brand-primary: #3c5599;
                --brand-secondary: #23a094;
                --brand-accent: #f55f44;
            }
        </style>
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
        <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950">
            <header class="border-b border-white/5">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-6">
                        <a href="{{ route('home') }}" class="flex items-center gap-3 text-lg font-semibold tracking-wide text-white">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-brand-500 bg-[radial-gradient(circle_at_top,var(--brand-secondary),var(--brand-primary))] text-white shadow-lg shadow-brand-500/30">
                                {{ substr(config('app.name', 'E-Shop'), 0, 1) }}
                            </span>
                            <span class="hidden sm:block">
                                {{ config('app.name', 'Nebula Vape Hub') }}
                            </span>
                        </a>
                        <nav class="hidden items-center gap-4 text-sm font-medium text-slate-200 sm:flex">
                            <a href="{{ route('catalog.index') }}" class="transition hover:text-white">Shop</a>
                            <a href="{{ route('catalog.index', ['category' => 'disposables']) }}" class="transition hover:text-white">Disposables</a>
                            <a href="{{ route('catalog.index', ['category' => 'pods']) }}" class="transition hover:text-white">Pods</a>
                            <a href="{{ route('catalog.index', ['category' => 'devices']) }}" class="transition hover:text-white">Devices</a>
                            <a href="{{ route('catalog.index', ['category' => 'bundles']) }}" class="transition hover:text-white">Bundles</a>
                        </nav>
                    </div>

                    <div class="flex items-center gap-3 text-sm">
                        <form action="{{ route('catalog.index') }}" method="GET" class="hidden items-center rounded-full border border-white/5 bg-white/5 px-3 py-1.5 text-sm text-slate-100 focus-within:border-brand-400 focus-within:ring-2 focus-within:ring-brand-400/40 sm:flex">
                            <input name="search" type="search" placeholder="Search flavours, brands…" class="bg-transparent text-sm focus:outline-none" value="{{ request('search') }}">
                            <button class="ml-2 rounded-full bg-brand-500 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white">Search</button>
                        </form>

                        <a href="{{ route('cart.index') }}" class="relative inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-white shadow-inner shadow-white/10 transition hover:bg-white/15">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l1.2-6H6.4M7 13l-1 5h12M7 13L5.4 5M17 18a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm-8 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"/>
                            </svg>
                            <span>Cart</span>
                        </a>

                        @auth
                            <div class="relative">
                                <details class="group">
                                    <summary class="flex cursor-pointer list-none items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-sm font-semibold text-white shadow-inner shadow-white/5 transition group-open:bg-white/10">
                                        <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                                        <svg class="h-4 w-4 transition group-open:rotate-180" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 8l4 4 4-4" />
                                        </svg>
                                    </summary>
                                    <div class="absolute right-0 mt-2 w-48 rounded-xl border border-white/10 bg-slate-900/95 p-2 shadow-xl shadow-black/30 backdrop-blur">
                                        <a href="{{ route('account.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm text-slate-200 transition hover:bg-white/10">My Account</a>
                                        <a href="{{ route('account.orders.index') }}" class="block rounded-lg px-3 py-2 text-sm text-slate-200 transition hover:bg-white/10">Orders</a>
                                        @if(Auth::user()->isAdmin())
                                            <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm text-slate-200 transition hover:bg-white/10">Admin</a>
                                        @elseif(Auth::user()->isVendor())
                                            <a href="{{ route('vendor.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm text-slate-200 transition hover:bg-white/10">Vendor</a>
                                        @endif
                                        <form method="POST" action="{{ route('logout') }}" class="mt-1">
                                            @csrf
                                            <button class="w-full rounded-lg px-3 py-2 text-left text-sm text-red-300 transition hover:bg-red-500/10">Sign out</button>
                                        </form>
                                    </div>
                                </details>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full border border-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10">Sign In</a>
                            <a href="{{ route('register') }}" class="rounded-full bg-brand-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-brand-500/40 transition hover:bg-brand-400">Join Now</a>
                        @endauth
                    </div>
                </div>
            </header>

            <main>
                @if (session('status'))
                    <div class="bg-brand-500/10 text-brand-100">
                        <div class="mx-auto max-w-7xl px-4 py-3 text-sm font-medium sm:px-6 lg:px-8">
                            {{ session('status') }}
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>

            <footer class="mt-16 border-t border-white/5 bg-slate-950/60">
                <div class="mx-auto max-w-7xl space-y-8 px-4 py-10 text-sm text-slate-400 sm:px-6 lg:px-8">
                    <div class="grid gap-8 md:grid-cols-4">
                        <div>
                            <p class="text-lg font-semibold text-white">{{ config('app.name', 'Nebula Vape Hub') }}</p>
                            <p class="mt-3 text-sm text-slate-400">Australia’s premium destination for curated vape devices, bold flavours, and luxe accessories.</p>
                        </div>
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-white/80">Shop</p>
                            <ul class="mt-3 space-y-2">
                                <li><a class="transition hover:text-white" href="{{ route('catalog.index', ['category' => 'disposables']) }}">Disposables</a></li>
                                <li><a class="transition hover:text-white" href="{{ route('catalog.index', ['category' => 'pods']) }}">Pods &amp; Cartridges</a></li>
                                <li><a class="transition hover:text-white" href="{{ route('catalog.index', ['category' => 'devices']) }}">Devices &amp; Mods</a></li>
                                <li><a class="transition hover:text-white" href="{{ route('catalog.index', ['category' => 'bundles']) }}">Bundles</a></li>
                            </ul>
                        </div>
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-white/80">Support</p>
                            <ul class="mt-3 space-y-2">
                                <li><a class="transition hover:text-white" href="{{ route('account.orders.index') }}">Track Order</a></li>
                                <li><a class="transition hover:text-white" href="{{ route('account.profile.edit') }}">Account</a></li>
                                <li><a class="transition hover:text-white" href="#">Shipping &amp; Returns</a></li>
                                <li><a class="transition hover:text-white" href="#">Wholesale</a></li>
                            </ul>
                        </div>
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-wider text-white/80">Connect</p>
                            <ul class="mt-3 space-y-2">
                                <li><a class="transition hover:text-white" href="mailto:support@example.com">support@example.com</a></li>
                                <li><span>Mon – Fri / 9am – 6pm AEST</span></li>
                                <li class="flex gap-3 pt-2 text-lg text-white">
                                    <a href="#" class="transition hover:text-brand-300">IG</a>
                                    <a href="#" class="transition hover:text-brand-300">TT</a>
                                    <a href="#" class="transition hover:text-brand-300">FB</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-4 border-t border-white/5 pt-6 text-xs text-slate-500">
                        <p>© {{ now()->year }} {{ config('app.name', 'Nebula Vape Hub') }}. Crafted with a blend of Vaperoo finesse, Uncle V minimalism, and Vices Oz boldness.</p>
                        <div class="flex items-center gap-3">
                            <span>18+ only. Vape responsibly.</span>
                            <span class="inline-flex h-6 items-center rounded-full bg-white/5 px-3">AU Market</span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
