<!DOCTYPE html>
<html lang="en" class="min-h-screen bg-[#17221b]">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin signup | Link-Tech Company</title>@include('partials.head')<style>
        body [data-flux-label],
        body [data-flux-control]+label,
        body [data-flux-description] {
            color: #d4edb7 !important;
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
    <main class="flex min-h-screen items-center justify-center p-6">
        <div class="w-full max-w-lg rounded-2xl border border-white/10 bg-[#202d24] p-6 shadow-2xl sm:p-9"><a href="{{ route('home') }}" class="flex items-center gap-3"><span class="flex size-10 items-center justify-center rounded-xl bg-[#d4edb7] text-[#17221b]"><x-app-logo-icon class="size-6" /></span><span class="font-bold">Link-Tech Company <span class="font-normal text-[#b5c6ba]">Admin</span></span></a>
            <div class="mt-9">
                <p class="text-xs font-bold uppercase tracking-[.2em] text-[#d4edb7]">Admin registration</p>
                <h1 class="mt-3 text-3xl font-bold">Create an admin account</h1>
                <p class="mt-3 text-[#b5c6ba]">Create the protected account used to manage your Link-Tech Company workspace.</p>
            </div>
            <form method="POST" action="{{ route('admin.register.store') }}" class="mt-8 space-y-5">@csrf
                <flux:input name="name" label="Full name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Your full name" />
                <p class="-mt-3 text-xs text-[#b5c6ba]">This name appears in the admin activity log.</p>
                <flux:input name="email" label="Admin email" type="email" value="{{ old('email') }}" required autocomplete="email" placeholder="owner@business.com" />
                <flux:input name="admin_key" label="Invitation key (optional)" type="password" autocomplete="off" placeholder="Leave empty if not configured" />
                <p class="-mt-3 text-xs text-[#b5c6ba]">A key becomes required automatically once the server has one configured.</p>
                <flux:input name="password" label="Password (12+ characters)" type="password" required autocomplete="new-password" viewable placeholder="Create a strong password" />
                <flux:input name="password_confirmation" label="Confirm password" type="password" required autocomplete="new-password" viewable placeholder="Repeat your password" />
                <flux:button type="submit" variant="primary" class="w-full">Create admin account</flux:button>
            </form>
            <p class="mt-7 text-center text-sm text-[#b5c6ba]">Already have admin access? <a class="font-semibold text-[#d4edb7] hover:underline" href="{{ route('admin.login') }}">Admin login</a></p>
        </div>
    </main>
</body>

</html>