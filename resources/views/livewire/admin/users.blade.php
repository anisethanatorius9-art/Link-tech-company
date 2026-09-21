<div class="min-h-full w-full flex-1 bg-[#f6f7f2] p-4 text-[#17221b] lg:p-8 dark:bg-zinc-950 dark:text-zinc-100">
    <div class="mx-auto max-w-7xl space-y-6">
        <div class="flex flex-col justify-between gap-5 border-b border-[#dce3d8] pb-6 sm:flex-row sm:items-end dark:border-zinc-800">
            <div>
                <flux:text class="mb-2 text-xs font-bold uppercase tracking-[.2em] text-[#2d7a57]">Workspace management</flux:text>
                <flux:heading size="xl">Users &amp; staff</flux:heading>
                <flux:text class="mt-2">Manage access, credentials, and staff accounts from one place.</flux:text>
            </div>
            <flux:button wire:click="openCreate" variant="primary" icon="user-plus">Create user</flux:button>
        </div>

        <flux:card class="border-[#dce3d8] bg-white p-4 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="w-full md:max-w-md">
                    <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Search by name or email" />
                </div>
                <div class="grid grid-cols-3 gap-6 border-t border-[#edf0eb] pt-4 lg:min-w-[390px] lg:border-l lg:border-t-0 lg:pl-6 lg:pt-0 dark:border-zinc-800">
                    <div>
                        <flux:text class="text-xs uppercase tracking-wide text-zinc-500">Total</flux:text>
                        <flux:heading size="sm" class="mt-1">{{ $this->users->total() }}</flux:heading>
                    </div>
                    <div>
                        <flux:text class="text-xs uppercase tracking-wide text-zinc-500">Active</flux:text>
                        <flux:heading size="sm" class="mt-1 text-emerald-600">{{ $activeCount }}</flux:heading>
                    </div>
                    <div>
                        <flux:text class="text-xs uppercase tracking-wide text-zinc-500">Suspended</flux:text>
                        <flux:heading size="sm" class="mt-1 text-rose-600">{{ $suspendedCount }}</flux:heading>
                    </div>
                </div>
            </div>
        </flux:card>

        <flux:card class="overflow-hidden border-[#dce3d8] bg-white p-0 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <flux:table :paginate="$this->users" class="min-w-[960px] text-sm">
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">Name</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'role'" :direction="$sortDirection" wire:click="sort('role')">Role &amp; position</flux:table.column>
                        <flux:table.column>Contact</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'is_active'" :direction="$sortDirection" wire:click="sort('is_active')">Access</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'created_at'" :direction="$sortDirection" wire:click="sort('created_at')">Joined</flux:table.column>
                        <flux:table.column align="end">Actions</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($this->users as $user)
                            <flux:table.row :key="$user->id">
                                <flux:table.cell class="py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-[#dff1e4] text-sm font-bold text-[#246344] dark:bg-emerald-950 dark:text-emerald-300">{{ $user->initials() }}</div>
                                        <div class="min-w-0">
                                            <div class="truncate font-semibold text-[#17221b] dark:text-zinc-100">{{ $user->name }}</div>
                                            <div class="text-xs text-zinc-500">Joined {{ $user->created_at?->format('M Y') }}</div>
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="py-4">
                                    <div class="font-medium">{{ ucfirst($user->role) }}</div>
                                    <div class="mt-1 text-xs text-zinc-500">{{ $user->position ?: 'No position added' }}</div>
                                </flux:table.cell>
                                <flux:table.cell class="py-4">
                                    <div class="max-w-[230px] truncate">{{ $user->email }}</div>
                                    <div class="mt-1 text-xs text-zinc-500">{{ $user->phone ?: 'No phone added' }}</div>
                                </flux:table.cell>
                                <flux:table.cell class="py-4">
                                    <flux:badge :color="$user->is_active ? 'green' : 'red'">{{ $user->is_active ? 'Active' : 'Suspended' }}</flux:badge>
                                    @if($user->must_change_password)
                                        <div class="mt-1 text-xs text-amber-600">Password reset required</div>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="whitespace-nowrap py-4">{{ $user->created_at?->format('M j, Y') }}</flux:table.cell>
                                <flux:table.cell align="end" class="py-4">
                                    <div class="flex justify-end gap-1">
                                        <flux:button wire:click="resetPassword({{ $user->id }})" wire:confirm="Send new temporary credentials to this user?" icon="key" variant="ghost" size="sm" aria-label="Reset password" title="Reset password" />
                                        <flux:button wire:click="toggleAccess({{ $user->id }})" wire:confirm="{{ $user->is_active ? 'Stop this user from accessing the system?' : 'Allow this user to access the system again?' }}" icon="{{ $user->is_active ? 'no-symbol' : 'check' }}" variant="{{ $user->is_active ? 'ghost' : 'primary' }}" size="sm" aria-label="{{ $user->is_active ? 'Suspend user' : 'Activate user' }}" title="{{ $user->is_active ? 'Suspend user' : 'Activate user' }}" :disabled="$user->is(auth()->user())" />
                                        <flux:button wire:click="deleteUser({{ $user->id }})" wire:confirm="Permanently delete this user and their account data? This action cannot be undone." icon="trash" variant="danger" size="sm" aria-label="Delete user" title="Delete user" :disabled="$user->is(auth()->user())" />
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row><flux:table.cell colspan="6" class="py-12 text-center text-[#637168]">No users match your search.</flux:table.cell></flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>
        </flux:card>
    </div>

    <flux:modal name="create-user" wire:model.self="showCreateForm" class="md:w-[min(720px,calc(100vw-2rem))]"><form wire:submit="createUser" class="space-y-5"><div><flux:heading size="lg">Create workspace user</flux:heading><flux:subheading class="mt-1">Choose whether credentials should be emailed or handed over manually.</flux:subheading></div><div class="grid gap-4 md:grid-cols-2"><flux:input wire:model="name" label="Full name" required /><flux:input wire:model="email" type="email" label="Email address" required /><flux:input wire:model="phone" label="Phone number" /><flux:input wire:model="position" label="Department / position" placeholder="Procurement Officer" required /><flux:select wire:model="role" label="System role"><option value="officer">Procurement officer</option><option value="finance">Finance</option><option value="admin">Company admin</option></flux:select><flux:select wire:model="deliveryChannel" label="Credential delivery"><option value="email">Send temporary password by email</option><option value="manual">Set credentials manually</option></flux:select>@if($deliveryChannel === 'manual')<flux:input wire:model="password" type="password" label="Temporary password" class="md:col-span-2" required />@endif</div><div class="flex flex-wrap items-center gap-4"><flux:checkbox wire:model="isActive" label="Activate account immediately" /><flux:checkbox wire:model="forcePasswordChange" label="Force password change on first login" /></div><div class="flex justify-end gap-3"><flux:button type="button" wire:click="$set('showCreateForm', false)" variant="ghost">Cancel</flux:button><flux:button type="submit" variant="primary" icon="user-plus">Create user</flux:button></div></form></flux:modal>
</div>
