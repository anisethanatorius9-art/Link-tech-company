<div class="min-h-full w-full flex-1 bg-[#f6f7f2] p-4 text-[#17221b] lg:p-8 dark:bg-zinc-950 dark:text-zinc-100">
    <div class="mx-auto max-w-7xl space-y-6">
        <div>
            <flux:heading size="xl">Tender requests</flux:heading>
            <flux:subheading>Review institution procurement requests before publishing them to vendors.</flux:subheading>
        </div>

        <flux:card class="overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-zinc-100 text-zinc-600 dark:bg-zinc-800/80 dark:text-zinc-400">
                        <tr>
                            <th class="p-4">Reference</th>
                            <th class="p-4">Tender</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">Deadline</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($tenders as $tender)
                            <tr class="align-top hover:bg-zinc-50 dark:hover:bg-zinc-800/30">
                                <td class="p-4 font-mono text-xs text-zinc-500">#{{ $tender->reference_no }}</td>
                                <td class="max-w-xl space-y-1 p-4">
                                    <div class="font-semibold">{{ $tender->title }}</div>
                                    <div class="whitespace-pre-line text-xs text-zinc-500">{{ $tender->description }}</div>
                                </td>
                                <td class="p-4"><flux:badge color="blue" size="sm">{{ $tender->category }}</flux:badge></td>
                                <td class="whitespace-nowrap p-4 text-zinc-600 dark:text-zinc-300">{{ $tender->deadline?->format('M d, Y - H:i') }}</td>
                                <td class="p-4">
                                    <div class="flex justify-end gap-2">
                                        <flux:button wire:click="approve({{ $tender->id }})" variant="primary" size="sm" icon="check">Approve</flux:button>
                                        <flux:button wire:click="reject({{ $tender->id }})" variant="subtle" size="sm" icon="x-mark">Reject</flux:button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-10 text-center text-zinc-500">No pending tender requests.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>
    </div>
</div>
