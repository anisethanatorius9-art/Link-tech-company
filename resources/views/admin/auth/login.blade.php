<!DOCTYPE html>
<html lang="en" class="min-h-screen bg-[#17221b]">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin login | Link-Tech Company</title>@include('partials.head')<style>
        body [data-flux-label],
        body [data-flux-control]+label,
        body [data-flux-description] {
            color: #f4f7f2 !important;
        }

        body [data-flux-control] {
            color: #17221b !important;
        }

        body [data-flux-control]::placeholder {
            color: #637168 !important;
        }

        body [data-flux-errors] {
            color: #fca5a5 !important;
        }
    </style>
</head>

<body class="min-h-screen bg-[#17221b] text-white antialiased">
    <main class="grid min-h-screen lg:grid-cols-[.9fr_1.1fr]">
        <section class="hidden flex-col justify-between border-r border-white/10 p-10 lg:flex"><a href="{{ route('home') }}" class="flex items-center gap-3" wire:navigate><span class="flex size-10 items-center justify-center rounded-xl bg-[#d4edb7] text-[#17221b]"><x-app-logo-icon class="size-6" /></span><span class="font-bold tracking-tight">Link-Tech Company <span class="font-normal text-[#b5c6ba]">Admin</span></span></a>
            <div>
                <p class="max-w-sm text-4xl font-bold leading-tight">The business view, with the details that matter.</p>
                <p class="mt-5 max-w-sm leading-7 text-[#b5c6ba]">Manage people, procurement, stock, and performance from a protected workspace.</p>
            </div><span class="text-xs text-[#718277]">Private management access ·Link-Tech Company</span>
        </section>
        <section class="flex items-center justify-center p-6 sm:p-10">
            <div class="w-full max-w-md">
                <div class="mb-10 lg:hidden"><a href="{{ route('home') }}" class="flex items-center gap-3"><span class="flex size-10 items-center justify-center rounded-xl bg-[#d4edb7] text-[#17221b]"><x-app-logo-icon class="size-6" /></span><span class="font-bold">Link-Tech Company Admin</span></a></div>
                <div class="mb-8">
                    <p class="text-xs font-bold uppercase tracking-[.2em] text-[#d4edb7]">Secure admin access</p>
                    <h1 class="mt-3 text-3xl font-bold">Sign in to your console</h1>
                    <p class="mt-3 text-[#b5c6ba]">Use your administrator credentials to continue.</p>
                </div>@if (session('status'))<div class="mb-5 rounded-lg border border-[#7fa98c] bg-[#2d7a57]/20 p-3 text-sm text-[#d4edb7]">{{ session('status') }}</div>@endif<form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5">@csrf
                    <flux:input name="email" label="Admin email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="owner@business.com" />
                    <p class="-mt-3 text-xs text-[#b5c6ba]">Use the email assigned to your administrator account.</p>
                    <flux:input name="password" label="Password" type="password" required autocomplete="current-password" placeholder="Enter your admin password" viewable />
                    <p class="-mt-3 text-xs text-[#b5c6ba]">Your password is required to protect business data.</p>
                    <flux:checkbox name="remember" label="Keep me signed in" />
                    <flux:button type="submit" variant="primary" class="w-full">Enter admin console</flux:button>
                </form>
                <p class="mt-7 text-center text-sm text-[#b5c6ba]">Need a new owner account? <a class="font-semibold text-[#d4edb7] hover:underline" href="{{ route('admin.register') }}">Admin signup</a></p>
                <p class="mt-3 text-center text-sm"><a class="text-[#b5c6ba] hover:text-white" href="{{ route('login') }}">Go to user login</a></p>
            </div>
        </section>
    </main>
</body>

</html>