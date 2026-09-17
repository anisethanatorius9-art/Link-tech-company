<x-layouts::admin :title="__('Admin dashboard')">
    <div class="min-h-full w-full flex-1 bg-[#f6f7f2] p-4 text-[#17221b] lg:p-8 dark:bg-zinc-950 dark:text-zinc-100">
        <div class="mx-auto max-w-7xl space-y-8">
            <div class="flex flex-col justify-between gap-4 border-b border-[#dce3d8] pb-6 sm:flex-row sm:items-end dark:border-zinc-800">
                <div>
                    <flux:text class="mb-2 text-xs font-bold uppercase tracking-[.2em] text-[#2d7a57]">Protected admin area</flux:text>
                    <flux:heading size="xl">Business overview</flux:heading>
                    <flux:text class="mt-2">Manage your users, requests, and operational reports.</flux:text>
                </div>
                <flux:button href="{{ route('admin.pos.terminal') }}" icon="computer-desktop" variant="ghost">Open POS terminal</flux:button>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <flux:card class="border-[#dce3d8] bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                    <flux:text>Registered users</flux:text>
                    <flux:heading size="xl" class="mt-3">{{ $users }}</flux:heading>
                    <flux:text class="mt-2 text-xs">All user accounts</flux:text>
                </flux:card>
                <flux:card class="border-[#dce3d8] bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                    <flux:text>Admin accounts</flux:text>
                    <flux:heading size="xl" class="mt-3">{{ $admins }}</flux:heading>
                    <flux:text class="mt-2 text-xs">Protected operators</flux:text>
                </flux:card>
                <flux:card class="border-[#dce3d8] bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                    <flux:text>Total requests</flux:text>
                    <flux:heading size="xl" class="mt-3">{{ $requests }}</flux:heading>
                    <flux:text class="mt-2 text-xs">Submitted by users</flux:text>
                </flux:card>
                <flux:card class="border-[#e9d7b7] bg-[#fff8eb] p-5 dark:border-zinc-800 dark:bg-amber-950/20">
                    <flux:text>Pending review</flux:text>
                    <flux:heading size="xl" class="mt-3 text-amber-700">{{ $pending }}</flux:heading>
                    <flux:text class="mt-2 text-xs">Requires action</flux:text>
                </flux:card>
            </div>
            <div id="users" class="grid gap-6 lg:grid-cols-[1fr_340px]">
                <flux:card class="overflow-hidden border-[#dce3d8] bg-white p-0 dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="border-b border-[#dce3d8] p-5 dark:border-zinc-800">
                        <flux:heading size="lg">Users who joined the system</flux:heading>
                        <flux:text class="mt-1">Names and account details visible only to administrators.</flux:text>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-[#f6f7f2] text-xs uppercase tracking-wider text-[#637168] dark:bg-zinc-800">
                                <tr>
                                    <th class="px-5 py-3">User</th>
                                    <th class="px-5 py-3">Role</th>
                                    <th class="px-5 py-3">Joined</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#edf0eb] dark:divide-zinc-800">@forelse ($userList as $account)<tr>
                                    <td class="px-5 py-4">
                                        <div class="font-semibold">{{ $account->name }}</div>
                                        <div class="text-xs text-[#637168]">{{ $account->email }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <flux:badge :color="$account->isAdmin() ? 'green' : 'zinc'">{{ ucfirst($account->role) }}</flux:badge>
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-[#637168]">{{ $account->created_at?->format('d M Y') }}</td>
                                </tr>@empty<tr>
                                    <td colspan="3" class="px-5 py-12 text-center text-[#637168]">No users have joined yet.</td>
                                </tr>@endforelse</tbody>
                        </table>
                    </div>
                </flux:card>
                <flux:card id="requests" class="border-[#dce3d8] bg-[#17221b] p-6 text-white dark:border-zinc-800">
                    <flux:text class="text-[#d4edb7]">Report centre</flux:text>
                    <flux:heading size="lg" class="mt-2">Accurate by default.</flux:heading>
                    <flux:text class="mt-3 text-[#b5c6ba]">Review requests, compare supplier prices, set profit percentages, and generate customer-safe quotations.</flux:text>
                    <div class="mt-7 space-y-4 border-t border-white/10 pt-5 text-sm">
                        <div class="flex justify-between"><span class="text-[#b5c6ba]">Completed</span><strong>{{ $completed }}</strong></div>
                        <div class="flex justify-between"><span class="text-[#b5c6ba]">Pending</span><strong>{{ $pending }}</strong></div>
                        <div class="flex justify-between"><span class="text-[#b5c6ba]">Exports</span><strong>PDF + Excel</strong></div>
                    </div>
                </flux:card>
            </div>
        </div>
    </div>
</x-layouts::admin>