<?php

namespace App\Livewire;

use App\Models\User;
use App\Notifications\UserCredentialsNotification;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class AdminUsers extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

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

    /** @var array<int, string> */
    private array $sortableColumns = ['name', 'role', 'created_at', 'is_active'];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if (! in_array($column, $this->sortableColumns, true)) {
            return;
        }

        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    /** @return array<string, array<int, mixed>> */
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

    public function deleteUser(int $userId): void
    {
        $user = User::query()->findOrFail($userId);

        abort_if($user->is(Auth::user()), 403, 'You cannot delete your own account.');

        $user->delete();

        Flux::toast(variant: 'success', text: 'User deleted permanently.');
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

    #[Computed]
    public function users()
    {
        return User::query()
            ->when($this->search !== '', fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$this->search.'%')->orWhere('email', 'like', '%'.$this->search.'%')))
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);
    }

    public function render(): View
    {
        return view('livewire.admin.users');
    }
}
