<div class="min-h-full w-full flex-1 bg-[#f6f7f2] p-4 text-[#17221b] lg:p-8 dark:bg-zinc-950 dark:text-zinc-100">
    <div class="mx-auto max-w-7xl space-y-8">
        <div class="flex flex-col justify-between gap-4 border-b border-[#dce3d8] pb-6 sm:flex-row sm:items-end dark:border-zinc-800">
            <div>
                <flux:text class="mb-2 text-xs font-bold uppercase tracking-[.2em] text-[#2d7a57]">Management</flux:text>
                <flux:heading size="xl">Users &amp; staff</flux:heading>
                <flux:text class="mt-2">View everyone who has joined the Ventis system.</flux:text>
            </div>
            <flux:button wire:click="openCreate" variant="primary" icon="plus">Create user</flux:button>
        </div>

        <flux:card class="border-[#dce3d8] bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
            <flux:input wire:model.live="search" icon="magnifying-glass" placeholder="Search by name or email" />
        </flux:card>

        <flux:card class="overflow-hidden border-[#dce3d8] bg-white p-0 dark:border-zinc-800 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#edf7ef] text-xs uppercase tracking-wider text-[#637168] dark:bg-zinc-800">
                        <tr><th class="px-5 py-3">Name</th><th class="px-5 py-3">Contact</th><th class="px-5 py-3">Position</th><th class="px-5 py-3">Access</th><th class="px-5 py-3 text-right">Actions</th></tr>
                    </thead>
                    <tbody class="divide-y divide-[#edf0eb] dark:divide-zinc-800">
                        @forelse ($users as $user)
                            <tr>
                                <td class="px-5 py-4 font-semibold">{{ $user->name }}</td>
                                <td class="space-y-1 px-5 py-4 text-[#637168]"><div>{{ $user->email }}</div><div class="text-xs">{{ $user->phone ?: 'No phone added' }}</div></td>
                                <td class="px-5 py-4"><div>{{ $user->position ?: ucfirst($user->role) }}</div><div class="mt-1 text-xs text-zinc-500">{{ $user->isAdmin() ? 'Company admin' : 'Procurement officer' }}</div></td>
                                <td class="space-y-1 px-5 py-4"><div><flux:badge :color="$user->is_active ? 'green' : 'red'">{{ $user->is_active ? 'Active' : 'Suspended' }}</flux:badge></div>@if($user->must_change_password)<div class="text-xs text-amber-600">Password reset required</div>@endif</td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex justify-end gap-2"><flux:button wire:click="resetPassword({{ $user->id }})" wire:confirm="Send new temporary credentials to this user?" icon="key" variant="ghost" size="sm">Reset password</flux:button><flux:button wire:click="toggleAccess({{ $user->id }})" wire:confirm="{{ $user->is_active ? 'Stop this user from accessing the system?' : 'Allow this user to access the system again?' }}" icon="{{ $user->is_active ? 'no-symbol' : 'check' }}" variant="{{ $user->is_active ? 'ghost' : 'primary' }}" size="sm" :disabled="$user->is(auth()->user())">{{ $user->is_active ? 'Suspend' : 'Activate' }}</flux:button></div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-12 text-center text-[#637168]">No users match your search.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>
    </div>

    <flux:modal name="create-user" wire:model.self="showCreateForm" class="md:w-[min(720px,calc(100vw-2rem))]"><form wire:submit="createUser" class="space-y-5"><div><flux:heading size="lg">Create workspace user</flux:heading><flux:subheading class="mt-1">Choose whether credentials should be emailed or handed over manually.</flux:subheading></div><div class="grid gap-4 md:grid-cols-2"><flux:input wire:model="name" label="Full name" required /><flux:input wire:model="email" type="email" label="Email address" required /><flux:input wire:model="phone" label="Phone number" /><flux:input wire:model="position" label="Department / position" placeholder="Procurement Officer" required /><flux:select wire:model="role" label="System role"><option value="officer">Procurement officer</option><option value="finance">Finance</option><option value="admin">Company admin</option></flux:select><flux:select wire:model="deliveryChannel" label="Credential delivery"><option value="email">Send temporary password by email</option><option value="manual">Set credentials manually</option></flux:select>@if($deliveryChannel === 'manual')<flux:input wire:model="password" type="password" label="Temporary password" class="md:col-span-2" required />@endif</div><div class="flex flex-wrap items-center gap-4"><flux:checkbox wire:model="isActive" label="Activate account immediately" /><flux:checkbox wire:model="forcePasswordChange" label="Force password change on first login" /></div><div class="flex justify-end gap-3"><flux:button type="button" wire:click="$set('showCreateForm', false)" variant="ghost">Cancel</flux:button><flux:button type="submit" variant="primary" icon="user-plus">Create user</flux:button></div></form></flux:modal>
</div>
