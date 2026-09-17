<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    public function showLogin(): View
    {
        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->where('role', 'admin')->first();

        if (! $user || ! $user->is_active || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'The admin credentials are incorrect.'])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function showRegister(): View
    {
        return view('admin.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'string', 'min:12'],
            'admin_key' => ['nullable', 'string'],
        ]);

        $configuredAdminKey = config('app.admin_signup_key');

        abort_unless(
            blank($configuredAdminKey) || hash_equals((string) $configuredAdminKey, (string) ($validated['admin_key'] ?? '')),
            403,
            'A valid admin invitation key is required.'
        );

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'admin',
        ]);

        return redirect()->route('admin.login')->with('status', 'Admin account created. You can now log in.');
    }
}
