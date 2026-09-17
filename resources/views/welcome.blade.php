<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Link-Tech Company') }} | Retail operations, simplified</title>
    @include('partials.head')
    <style>
        :root { --ink: #17221b; --muted: #637168; --green: #2d7a57; --line: #dce4dc; --paper: #f5f7f2; }
        body { background: var(--paper); color: var(--ink); }
        .blueprint { background-image: linear-gradient(rgba(45,122,87,.07) 1px, transparent 1px), linear-gradient(90deg, rgba(45,122,87,.07) 1px, transparent 1px); background-size: 32px 32px; }
        .rise { animation: rise .7s ease-out both; }
        @keyframes rise { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="min-h-screen antialiased">
    <div class="pointer-events-none fixed inset-0 -z-10 blueprint"></div>
    <header class="mx-auto max-w-7xl px-5 pt-5 lg:px-8">
        <nav class="flex items-center justify-between border-b border-[#dce4dc] pb-5">
            <a href="{{ route('home') }}" class="flex items-center gap-3" wire:navigate>
                <span class="flex size-10 items-center justify-center rounded-xl bg-white p-1"><x-app-logo-icon class="size-8" /></span>
                <span><span class="block text-lg font-bold tracking-tight">Link-Tech Company</span><span class="block text-[10px] font-bold uppercase tracking-[.22em] text-[#637168]">Business management</span></span>
            </a>
            <div class="hidden items-center gap-8 text-sm font-semibold text-[#637168] md:flex"><a href="#roles" class="hover:text-[#2d7a57]">For teams</a><a href="#control" class="hover:text-[#2d7a57]">Control</a><a href="#start" class="hover:text-[#2d7a57]">Get started</a></div>
            <div class="flex items-center gap-2 sm:gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-lg border border-[#cbd7cc] bg-white px-3 py-2 text-sm font-semibold hover:border-[#2d7a57]">Open console <flux:icon name="arrow-up-right" class="size-4" /></a>
                @else
                    <a href="{{ route('admin.login') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-[#637168] hover:text-[#2d7a57] sm:text-sm"><flux:icon name="shield-check" class="size-4" /> Admin login</a>
                    <a href="{{ route('login') }}" class="hidden text-sm font-semibold text-[#637168] hover:text-[#2d7a57] sm:block">User login</a>
                    @if (Route::has('register'))<a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#2d7a57] px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-[#2d7a57]/20 hover:bg-[#246447]">Start now <flux:icon name="arrow-right" class="size-4" /></a>@endif
                @endauth
            </div>
        </nav>
    </header>

    <main>
        <section class="mx-auto grid max-w-7xl items-center gap-12 px-5 pb-20 pt-16 lg:grid-cols-[.88fr_1.12fr] lg:px-8 lg:pb-28 lg:pt-24">
            <div class="rise">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-[#b9d8c3] bg-[#edf7ef] px-3 py-1.5 text-xs font-bold text-[#2d7a57]"><span class="size-2 rounded-full bg-[#2d7a57]"></span>Made for modern retail</div>
                <h1 class="max-w-2xl text-5xl font-bold leading-[1.03] tracking-[-.045em] sm:text-6xl lg:text-7xl">Your store, <span class="text-[#2d7a57]">in rhythm.</span></h1>
                <p class="mt-7 max-w-xl text-lg leading-8 text-[#637168]">Link-Tech Company connects every sale, stock movement, supplier request, and shift into one calm, dependable workspace.</p>
                <div class="mt-9 flex flex-col gap-3 sm:flex-row"><a href="{{ Route::has('register') ? route('register') : route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#17221b] px-5 py-3.5 text-sm font-semibold text-white hover:bg-[#2d7a57]">Create your workspace <flux:icon name="arrow-right" class="size-4" /></a><a href="#roles" class="inline-flex items-center justify-center gap-2 rounded-lg border border-[#cbd7cc] bg-white px-5 py-3.5 text-sm font-semibold hover:border-[#2d7a57]">Explore the workflow <flux:icon name="chevron-down" class="size-4" /></a></div>
                <div class="mt-10 flex flex-wrap gap-x-6 gap-y-3 text-sm text-[#637168]"><span class="flex items-center gap-2"><flux:icon name="check-circle" class="size-4 text-[#2d7a57]" />Quick checkout</span><span class="flex items-center gap-2"><flux:icon name="check-circle" class="size-4 text-[#2d7a57]" />Live stock view</span><span class="flex items-center gap-2"><flux:icon name="check-circle" class="size-4 text-[#2d7a57]" />Secure roles</span></div>
            </div>
            <div class="rise relative" style="animation-delay: .15s">
                <div class="absolute -inset-8 rounded-[2rem] bg-[#d4edb7]/70 blur-3xl"></div>
                <div class="relative overflow-hidden rounded-2xl border border-[#cbd7cc] bg-white p-3 shadow-2xl shadow-[#17221b]/10 sm:p-5">
                    <div class="flex items-center justify-between border-b border-[#edf0eb] pb-4"><div class="flex items-center gap-2"><span class="size-2.5 rounded-full bg-[#2d7a57]"></span><span class="text-xs font-bold uppercase tracking-[.18em] text-[#637168]">Today at Link-Tech</span></div><span class="text-xs text-[#637168]">02 Sep 2026</span></div>
                    <div class="grid gap-3 pt-4 sm:grid-cols-3"><div class="rounded-xl bg-[#f5f7f2] p-4"><span class="text-xs text-[#637168]">Sales today</span><strong class="mt-2 block text-xl">TZS 4.82m</strong><span class="mt-1 block text-xs text-[#2d7a57]">+12.8% this week</span></div><div class="rounded-xl bg-[#f5f7f2] p-4"><span class="text-xs text-[#637168]">Transactions</span><strong class="mt-2 block text-xl">186</strong><span class="mt-1 block text-xs text-[#637168]">Across 3 shifts</span></div><div class="rounded-xl bg-[#eaf5ed] p-4"><span class="text-xs text-[#637168]">Stock alerts</span><strong class="mt-2 block text-xl text-[#b16b18]">07</strong><span class="mt-1 block text-xs text-[#637168]">Needs attention</span></div></div>
                    <div class="mt-4 rounded-xl border border-[#edf0eb] p-4"><div class="flex items-center justify-between"><div><span class="text-xs uppercase tracking-[.16em] text-[#637168]">Revenue trend</span><strong class="mt-1 block text-lg">This week</strong></div><span class="rounded-full bg-[#edf7ef] px-2.5 py-1 text-xs font-bold text-[#2d7a57]">Live</span></div><div class="mt-6 flex h-32 items-end gap-2 border-b border-[#edf0eb]">@foreach ([38, 52, 44, 67, 61, 82, 94] as $height)<div class="flex-1 rounded-t bg-[#2d7a57]" @style(['height' => $height.'%', 'opacity' => $loop->last ? '1' : '.55'])></div>@endforeach</div><div class="mt-2 flex justify-between text-[10px] text-[#637168]"><span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span></div></div>
                    <div class="mt-4 flex items-center justify-between rounded-xl bg-[#17221b] p-4 text-white"><div><span class="text-xs text-[#b5c6ba]">Next action</span><strong class="mt-1 block">4 supplier requests to review</strong></div><flux:icon name="arrow-up-right" class="size-5 text-[#d4edb7]" /></div>
                </div>
            </div>
        </section>

        <section id="roles" class="border-y border-[#dce4dc] bg-white"><div class="mx-auto max-w-7xl px-5 py-20 lg:px-8"><div class="max-w-2xl"><span class="text-xs font-bold uppercase tracking-[.22em] text-[#2d7a57]">One platform, clear roles</span><h2 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">Every person gets a better shift.</h2><p class="mt-4 leading-7 text-[#637168]">A focused experience for the counter. A complete view for the people running the business.</p></div><div class="mt-12 grid gap-5 md:grid-cols-2"><div class="rounded-2xl border border-[#dce4dc] bg-[#f5f7f2] p-7 transition hover:-translate-y-1 hover:border-[#9cc6aa]"><div class="flex size-12 items-center justify-center rounded-xl bg-[#17221b] text-[#d4edb7]"><flux:icon name="shopping-cart" class="size-6" /></div><h3 class="mt-6 text-2xl font-bold">The cashier view</h3><p class="mt-3 leading-7 text-[#637168]">Find products quickly, build accurate orders, accept cash, mobile money, or card, then close the shift with confidence.</p><div class="mt-6 flex flex-wrap gap-2 text-xs font-semibold text-[#2d7a57]"><span class="rounded-full bg-white px-3 py-1.5">POS terminal</span><span class="rounded-full bg-white px-3 py-1.5">Receipts</span><span class="rounded-full bg-white px-3 py-1.5">Cash drawer</span></div></div><div class="rounded-2xl border border-[#dce4dc] bg-[#edf7ef] p-7 transition hover:-translate-y-1 hover:border-[#9cc6aa]"><div class="flex size-12 items-center justify-center rounded-xl bg-[#2d7a57] text-white"><flux:icon name="chart-bar" class="size-6" /></div><h3 class="mt-6 text-2xl font-bold">The owner view</h3><p class="mt-3 leading-7 text-[#637168]">See revenue, profit, stock pressure, supplier activity, and team performance without chasing spreadsheets.</p><div class="mt-6 flex flex-wrap gap-2 text-xs font-semibold text-[#2d7a57]"><span class="rounded-full bg-white px-3 py-1.5">P&amp;L reporting</span><span class="rounded-full bg-white px-3 py-1.5">Inventory</span><span class="rounded-full bg-white px-3 py-1.5">Procurement</span></div></div></div></div></section>
+
+        <section id="control" class="mx-auto max-w-7xl px-5 py-20 lg:px-8"><div class="grid items-center gap-12 lg:grid-cols-[1fr_.8fr]"><div><span class="text-xs font-bold uppercase tracking-[.22em] text-[#2d7a57]">Operational clarity</span><h2 class="mt-4 max-w-xl text-3xl font-bold tracking-tight sm:text-4xl">Catch the important things early.</h2><p class="mt-5 max-w-xl leading-7 text-[#637168]">Low stock, pending procurement, open shifts, and daily performance all belong in one place. Ventis turns operational noise into the next clear action.</p><div class="mt-8 grid gap-4 sm:grid-cols-2"><div class="border-l-2 border-[#2d7a57] pl-4"><strong class="block">Role-based access</strong><span class="mt-1 block text-sm leading-6 text-[#637168]">Protect sensitive business data while keeping work moving.</span></div><div class="border-l-2 border-[#2d7a57] pl-4"><strong class="block">Audit-ready activity</strong><span class="mt-1 block text-sm leading-6 text-[#637168]">Know who processed, changed, or approved every action.</span></div></div></div><div class="rounded-2xl bg-[#17221b] p-7 text-white shadow-xl shadow-[#17221b]/15"><div class="flex items-center justify-between"><span class="text-xs uppercase tracking-[.2em] text-[#b5c6ba]">Business pulse</span><flux:icon name="lock-closed" class="size-5 text-[#d4edb7]" /></div><div class="mt-8 space-y-5"><div class="flex items-end justify-between border-b border-white/10 pb-4"><span class="text-[#b5c6ba]">Gross sales</span><strong class="text-2xl">TZS 4.82m</strong></div><div class="flex items-end justify-between border-b border-white/10 pb-4"><span class="text-[#b5c6ba]">Net profit</span><strong class="text-2xl text-[#d4edb7]">TZS 1.36m</strong></div><div class="flex items-end justify-between"><span class="text-[#b5c6ba]">Open shifts</span><strong class="text-2xl">03</strong></div></div></div></div></section>
+
+        <section id="start" class="bg-[#d4edb7]"><div class="mx-auto flex max-w-7xl flex-col justify-between gap-6 px-5 py-14 sm:flex-row sm:items-center lg:px-8"><div><h2 class="text-2xl font-bold tracking-tight">Ready for a smoother day?</h2><p class="mt-2 text-[#365543]">Bring sales, stock, and your team into one operating rhythm.</p></div><a href="{{ Route::has('register') ? route('register') : route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#17221b] px-5 py-3.5 text-sm font-semibold text-white hover:bg-[#2d7a57]">Start with Ventis <flux:icon name="arrow-right" class="size-4" /></a></div></section>
+    </main>
+    <footer class="mx-auto flex max-w-7xl flex-col justify-between gap-3 px-5 py-8 text-sm text-[#637168] sm:flex-row lg:px-8"><span>© {{ date('Y') }} Ventis POS</span><span>Sales, stock, and operations in one place.</span></footer>
+</body>
+</html>
