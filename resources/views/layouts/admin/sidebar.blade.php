<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>@include('partials.head')</head>

<body class="min-h-screen bg-[#f6f7f2] dark:bg-zinc-950">
    <flux:sidebar sticky collapsible="mobile" class="border-e border-[#293a2e] bg-[#17221b] text-white">
        <flux:sidebar.header>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-2" wire:navigate><span class="flex size-9 items-center justify-center rounded-lg bg-[#d4edb7] text-[#17221b]"><x-app-logo-icon class="size-5" /></span><span><strong class="block text-sm">Link-Tech Company</strong><span class="block text-[10px] uppercase tracking-[.18em] text-[#b5c6ba]">Admin console</span></span></a>
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>
        <flux:sidebar.nav>
            <flux:sidebar.group heading="Management" class="grid">
                <flux:sidebar.item icon="chart-bar" :href="route('admin.dashboard')" :current="request()->routeIs('admin.dashboard')" wire:navigate>Overview</flux:sidebar.item>
                <flux:sidebar.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')" wire:navigate>Users & staff</flux:sidebar.item>
                <flux:sidebar.item icon="receipt-percent" :href="route('admin.quotes')" :current="request()->routeIs('admin.quotes')" wire:navigate>Quotes</flux:sidebar.item>
                <flux:sidebar.item icon="bell-alert" :href="route('admin.quote-history')" :current="request()->routeIs('admin.quote-history')" wire:navigate>Quote decisions</flux:sidebar.item>
                <flux:sidebar.item icon="briefcase" :href="route('admin.procurement')" :current="request()->routeIs('admin.procurement')" wire:navigate>Procurement</flux:sidebar.item>
                <flux:sidebar.item icon="arrow-down-tray" :href="route('admin.reports')" :current="request()->routeIs('admin.reports')" wire:navigate>Reports & exports</flux:sidebar.item>
                <flux:sidebar.item icon="cog-6-tooth" :href="route('admin.settings')" :current="request()->routeIs('admin.settings', 'admin.company.settings')" wire:navigate>System settings</flux:sidebar.item>
            </flux:sidebar.group>
            <flux:sidebar.group heading="Operations" class="mt-5 grid">
                <flux:sidebar.item icon="envelope" :href="route('admin.pos.terminal')" :current="request()->routeIs('admin.pos.terminal')" wire:navigate>Company feedback</flux:sidebar.item>
                <flux:sidebar.item icon="home" :href="route('home')" :current="false">View website</flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>
        <flux:spacer />
        <div class="border-t border-white/10 px-2 py-4">
            <div class="mb-3 flex items-center gap-2">
                <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />
                <div class="min-w-0">
                    <div class="truncate text-sm font-semibold">{{ auth()->user()->name }}</div>
                    <div class="truncate text-xs text-[#b5c6ba]">Administrator</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">@csrf<flux:button as="button" type="submit" icon="arrow-right-start-on-rectangle" variant="ghost" class="w-full justify-start !text-white hover:bg-white/10 hover:!text-white">Sign out</flux:button>
            </form>
        </div>
    </flux:sidebar>
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:spacer /><span class="text-sm font-semibold">Admin console</span>
    </flux:header>
    {{ $slot }}
    @persist('toast')<flux:toast.group>
        <flux:toast />
    </flux:toast.group>@endpersist
    @fluxScripts
</body>

</html>
