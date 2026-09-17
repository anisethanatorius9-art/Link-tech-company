<div class="min-h-full w-full flex-1 bg-[#f6f7f2] p-4 text-[#17221b] lg:p-8 dark:bg-zinc-950 dark:text-zinc-100">
    <div class="mx-auto max-w-7xl space-y-6">
        <div>
            <flux:heading size="xl">Submitted bids</flux:heading>
            <flux:subheading>Review and manage vendor bids and tender proposals.</flux:subheading>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <flux:card class="flex items-center gap-4"><flux:icon name="document-text" class="size-6 text-emerald-600" /><div><flux:text size="sm" class="text-zinc-500">Total bids</flux:text><flux:heading size="lg">{{ $total }}</flux:heading></div></flux:card>
            <flux:card class="flex items-center gap-4"><flux:icon name="clock" class="size-6 text-amber-600" /><div><flux:text size="sm" class="text-zinc-500">Pending review</flux:text><flux:heading size="lg">{{ $pending }}</flux:heading></div></flux:card>
            <flux:card class="flex items-center gap-4"><flux:icon name="check-circle" class="size-6 text-emerald-600" /><div><flux:text size="sm" class="text-zinc-500">Approved</flux:text><flux:heading size="lg">{{ $approved }}</flux:heading></div></flux:card>
        </div>

        <div class="grid grid-cols-1 gap-4 rounded-xl border border-zinc-200 bg-white p-4 md:grid-cols-3 dark:border-zinc-800 dark:bg-zinc-900">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search company or email..." />
            <flux:select wire:model.live="selectedTenderId">
                <option value="all">All tenders</option>
                @foreach ($tenders as $tender)
                    <option value="{{ $tender->id }}">{{ $tender->reference_no }} - {{ \Illuminate\Support\Str::limit($tender->title, 25) }}</option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="statusFilter">
                <option value="all">All statuses</option>
                <option value="submitted">Submitted</option>
                <option value="under_review">Under review</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </flux:select>
        </div>

        <flux:card class="overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-zinc-100 text-zinc-600 dark:bg-zinc-800/80 dark:text-zinc-400"><tr><th class="p-4">Company / contact</th><th class="p-4">Tender</th><th class="p-4">Amount</th><th class="p-4">Documents</th><th class="p-4">Status</th><th class="p-4 text-right">Actions</th></tr></thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($submissions as $submission)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30">
                                <td class="space-y-1 p-4"><div class="font-semibold">{{ $submission->company_name }}</div><div class="text-xs text-zinc-500">{{ $submission->email }} · {{ $submission->phone_number }}</div></td>
                                <td class="p-4"><div class="font-medium">{{ $submission->tender->title }}</div><span class="font-mono text-xs text-zinc-500">{{ $submission->tender->reference_no }}</span></td>
                                <td class="p-4 font-semibold text-emerald-600">TZS {{ number_format($submission->proposed_amount, 2) }}</td>
                                <td class="space-y-1 p-4"><div class="flex gap-2"><flux:button href="{{ \Illuminate\Support\Facades\Storage::url($submission->technical_proposal_path) }}" target="_blank" variant="subtle" size="xs" icon="document">Technical</flux:button><flux:button href="{{ \Illuminate\Support\Facades\Storage::url($submission->financial_proposal_path) }}" target="_blank" variant="subtle" size="xs" icon="currency-dollar">Financial</flux:button></div></td>
                                <td class="p-4">
                                    @if ($submission->status === 'submitted') <flux:badge color="amber" size="sm">Submitted</flux:badge>
                                    @elseif ($submission->status === 'under_review') <flux:badge color="blue" size="sm">Under review</flux:badge>
                                    @elseif ($submission->status === 'approved') <flux:badge color="green" size="sm">Approved</flux:badge>
                                    @else <flux:badge color="red" size="sm">Rejected</flux:badge> @endif
                                </td>
                                <td class="p-4 text-right"><div class="flex justify-end gap-2">@if ($submission->status !== 'approved')<flux:button wire:click="acceptBid({{ $submission->id }})" variant="primary" size="xs">Accept bid</flux:button>@endif@if ($submission->status === 'submitted')<flux:button wire:click="updateStatus({{ $submission->id }}, 'under_review')" variant="subtle" size="xs">Review</flux:button>@endif</div></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-8 text-center text-zinc-500">No tender submissions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>
        {{ $submissions->links() }}
    </div>
</div>
