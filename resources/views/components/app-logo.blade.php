@props([
    'sidebar' => false,
])

@php
    $brandName = config('app.name', 'Link-Tech Company');
    $brandLabel = collect(explode(' ', trim($brandName)))->take(2)->implode(' ');
@endphp

<a {{ $attributes->class('flex items-center gap-3 min-w-0 text-slate-900 transition-colors dark:text-white') }}>
    <span class="flex size-9 items-center justify-center rounded-xl bg-white/90 ring-1 ring-slate-200 shadow-sm dark:bg-white">
        <x-app-logo-icon class="size-5" />
    </span>

    <span class="min-w-0 leading-none">
        <span class="block text-[10px] font-semibold uppercase tracking-[0.24em] text-sky-600 dark:text-sky-300">Link-Tech</span>
        <span class="mt-1 block text-sm font-semibold text-slate-900 dark:text-white">{{ $brandLabel }}</span>
    </span>
</a>
