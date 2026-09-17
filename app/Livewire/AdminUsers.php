<?php

namespace App\Livewire;

use App\Models\User;
use App\Notifications\UserCredentialsNotification;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AdminUsers extends Component
{
    public string $search = '';

    public bool $showCreateForm = false;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $position = '';

    public string $role = 'user';

    public string $deliveryChannel = 'email';

    public string $password = '';

    public bool $isActive = true;

    public bool $forcePasswordChange = true;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'position' => ['required', 'string', 'max:255'],
            'role' => ['required', Rule::in(['officer', 'finance', 'user', 'admin'])],
            'deliveryChannel' => ['required', Rule::in(['email', 'manual'])],
            'password' => ['nullable', 'string', 'min:12', 'required_if:deliveryChannel,manual'],
            'isActive' => ['boolean'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showCreateForm = true;
    }

    public function createUser(): void
    {
        $validated = $this->validate();
        $temporaryPassword = $validated['deliveryChannel'] === 'email'
            ? Str::password(16, letters: true, numbers: true, symbols: true)
            : $validated['password'];

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?: null,
            'position' => $validated['position'],
            'role' => $validated['role'],
            'password' => $temporaryPassword,
            'is_active' => $validated['isActive'],
            'must_change_password' => $validated['forcePasswordChange'],
        ]);

        if ($validated['deliveryChannel'] === 'email') {
            $user->notify(new UserCredentialsNotification($temporaryPassword));
        }

        Flux::toast(variant: 'success', text: $validated['deliveryChannel'] === 'email'
            ? 'User created and credentials queued for email delivery.'
            : 'User created with manual credentials.');

        $this->showCreateForm = false;
        $this->resetForm();
    }

    public function toggleAccess(int $userId): void
    {
        $user = User::query()->findOrFail($userId);

        abort_if($user->is(Auth::user()), 403, 'You cannot change your own access.');

        $user->update(['is_active' => ! $user->is_active]);
    }

    public function resetPassword(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        $temporaryPassword = Str::password(16, letters: true, numbers: true, symbols: true);
        $user->update(['password' => $temporaryPassword, 'must_change_password' => true]);
        $user->notify(new UserCredentialsNotification($temporaryPassword));
        Flux::toast(variant: 'success', text: 'New temporary credentials queued for email delivery.');
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'email', 'phone', 'position', 'password']);
        $this->role = 'user';
        $this->deliveryChannel = 'email';
        $this->isActive = true;
        $this->forcePasswordChange = true;
        $this->resetValidation();
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$this->search.'%')->orWhere('email', 'like', '%'.$this->search.'%')))
            ->latest('created_at')
            ->get();

        return view('livewire.admin.users', ['users' => $users]);
    }
}
