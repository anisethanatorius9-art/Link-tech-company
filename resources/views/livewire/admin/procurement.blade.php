<div class="min-h-full w-full flex-1 bg-[#f6f7f2] p-4 text-[#17221b] lg:p-8 dark:bg-zinc-950 dark:text-zinc-100">
    <div class="mx-auto max-w-[1500px] space-y-6">
        <div class="flex flex-col justify-between gap-4 border-b border-[#dce3d8] pb-6 sm:flex-row sm:items-end dark:border-zinc-800">
            <div>
                <flux:text class="mb-2 text-xs font-bold uppercase tracking-[.2em] text-[#2d7a57]">Commercial pipeline</flux:text>
                <flux:heading size="xl">Procurement</flux:heading>
                <flux:subheading class="mt-2">Track every tender from intake to quotation, award, or loss.</flux:subheading>
            </div>
            <flux:button wire:click="openCreate" variant="primary" icon="plus">New tender record</flux:button>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <flux:card class="border-[#dce3d8] p-5 dark:border-zinc-800"><flux:text size="sm" class="text-zinc-500">All records</flux:text><flux:heading size="xl" class="mt-2">{{ $total }}</flux:heading></flux:card>
            <flux:card class="border-[#dce3d8] p-5 dark:border-zinc-800"><flux:text size="sm" class="text-zinc-500">Submitted this month</flux:text><flux:heading size="xl" class="mt-2">{{ $submittedThisMonth }}</flux:heading></flux:card>
            <flux:card class="border-[#dce3d8] p-5 dark:border-zinc-800"><flux:text size="sm" class="text-zinc-500">Quoted this month</flux:text><flux:heading size="xl" class="mt-2">{{ number_format((float) $quotedThisMonth, 2) }} TZS</flux:heading></flux:card>
            <flux:card class="border-[#dce3d8] p-5 dark:border-zinc-800"><flux:text size="sm" class="text-zinc-500">Win rate</flux:text><flux:heading size="xl" class="mt-2">{{ $winRate }}%</flux:heading></flux:card>
        </div>

        <div class="grid grid-cols-1 gap-3 rounded-xl border border-[#dce3d8] bg-white p-4 md:grid-cols-[minmax(0,1.5fr)_repeat(2,minmax(0,1fr))] dark:border-zinc-800 dark:bg-zinc-900">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Search NeST number, tender, or client..." />
            <flux:select wire:model.live="statusFilter">
                <option value="all">All statuses</option>
                <option value="draft">Draft</option>
                <option value="submitted">Submitted</option>
                <option value="under_evaluation">Under evaluation</option>
                <option value="awarded">Awarded</option>
                <option value="lost">Lost</option>
                <option value="cancelled">Cancelled</option>
            </flux:select>
            <flux:select wire:model.live="dateFilter">
                <option value="all">All dates</option>
                <option value="this_month">Deadline this month</option>
                <option value="overdue">Overdue and open</option>
            </flux:select>
        </div>

        <flux:card class="overflow-hidden border-[#dce3d8] p-0 dark:border-zinc-800">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1050px] text-left text-sm">
                    <thead class="bg-[#edf7ef] text-xs uppercase tracking-wider text-[#637168] dark:bg-zinc-800/80 dark:text-zinc-400">
                        <tr><th class="p-4">Tender</th><th class="p-4">Submitted by</th><th class="p-4">Client / institution</th><th class="p-4">Deadline</th><th class="p-4">Quotation</th><th class="p-4">Officer</th><th class="p-4">Status</th><th class="p-4 text-right">Action</th></tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($tenders as $tender)
                            <tr class="align-top hover:bg-zinc-50 dark:hover:bg-zinc-800/30">
                                <td class="space-y-1 p-4"><div class="font-semibold">{{ $tender->title }}</div><div class="font-mono text-xs text-zinc-500">{{ $tender->reference_number ?: $tender->reference_no }}</div></td>
                                <td class="p-4"><div class="font-semibold">{{ $tender->creator?->name ?: 'Admin / manual entry' }}</div><div class="text-xs text-zinc-500">{{ $tender->creator?->email ?: 'No user account linked' }}</div></td>
                                <td class="p-4 text-zinc-600 dark:text-zinc-300">{{ $tender->client_name ?: 'Not set' }}</td>
                                <td class="whitespace-nowrap p-4"><div class="font-medium">{{ $tender->submission_deadline?->format('d M Y, H:i') ?: $tender->deadline?->format('d M Y, H:i') }}</div><div class="text-xs text-zinc-500">{{ $tender->assignedOfficer?->name ?: 'Unassigned' }}</div></td>
                                <td class="whitespace-nowrap p-4 font-semibold">{{ $tender->quoted_amount !== null ? number_format((float) $tender->quoted_amount, 2).' '.$tender->currency : 'Not quoted' }}</td>
                                <td class="p-4 text-zinc-600 dark:text-zinc-300">{{ $tender->assignedOfficer?->name ?: 'Unassigned' }}</td>
                                <td class="p-4">
                                    @php($statusColor = match ($tender->status) { 'awarded' => 'green', 'submitted' => 'amber', 'under_evaluation' => 'blue', 'lost', 'cancelled' => 'red', default => 'zinc' })
                                    <flux:badge color="{{ $statusColor }}" size="sm">{{ str_replace('_', ' ', ucfirst($tender->status)) }}</flux:badge>
                                    @if ($tender->quote_status !== 'draft')<div class="mt-1 text-xs text-emerald-700">Quote {{ $tender->quote_status }}</div>@elseif ($tender->quoteItems()->exists())<div class="mt-1 text-xs text-amber-700">Quote ready to send</div>@endif
                                    <div class="mt-1 text-xs text-zinc-500">{{ $tender->documents->count() }} document(s)</div>
                                </td>
                                <td class="p-4 text-right"><flux:button wire:click="openEdit({{ $tender->id }})" variant="subtle" size="sm" icon="pencil-square">Open</flux:button></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="p-12 text-center text-zinc-500">No procurement records match these filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>
        {{ $tenders->links() }}
    </div>

    <flux:modal name="procurement-form" wire:model.self="showForm" class="md:w-[min(900px,calc(100vw-2rem))]">
        <form wire:submit="save" class="space-y-6">
            <div><flux:heading size="lg">{{ $editingTenderId ? 'Edit procurement record' : 'New tender record' }}</flux:heading><flux:subheading class="mt-1">Capture the tender once, then update its stage as the bid moves forward.</flux:subheading></div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <flux:input wire:model="referenceNumber" label="NeST / tender number" required />
                <flux:input wire:model="clientName" label="Issuing institution / client" required />
                <flux:input wire:model="title" label="Tender title" required class="md:col-span-2" />
                <flux:input wire:model="publishedDate" type="date" label="Published date" />
                <flux:input wire:model="submissionDeadline" type="datetime-local" label="Submission deadline" required />
                <flux:select wire:model="assignedOfficerId" label="Assigned officer"><option value="">Select officer</option>@foreach ($officers as $officer)<option value="{{ $officer->id }}">{{ $officer->name }}</option>@endforeach</flux:select>
                <flux:select wire:model="status" label="Tender status"><option value="draft">Draft</option><option value="submitted">Submitted</option><option value="under_evaluation">Under evaluation</option><option value="awarded">Awarded</option><option value="lost">Lost</option><option value="cancelled">Cancelled</option></flux:select>
            </div>

            <flux:separator text="Quotation and submission" />
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <flux:input wire:model="estimatedValue" type="number" step="0.01" min="0" label="Estimated client value" />
                <flux:input wire:model="quotedAmount" type="number" step="0.01" min="0" label="Submitted amount" />
                <flux:input wire:model="currency" maxlength="3" label="Currency" />
                <flux:select wire:model="submissionChannel" label="Submission channel"><option value="">Select channel</option><option value="NeST">NeST</option><option value="Email">Email</option><option value="Physical">Physical</option></flux:select>
                <flux:input wire:model="confirmationReference" label="Confirmation / evidence reference" class="md:col-span-2" />
            </div>

            <flux:separator text="Outcome" />
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <flux:input wire:model="contractValue" type="number" step="0.01" min="0" label="Final contract value" />
                <flux:textarea wire:model="outcomeNotes" label="Loss reason or outcome notes" rows="3" />
            </div>

            <flux:separator text="Supporting PDF" />
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <flux:select wire:model="documentType" label="Document type"><option value="quotation">Quotation</option><option value="financial_proposal">Financial proposal</option><option value="technical_proposal">Technical proposal</option><option value="nest_receipt">NeST acknowledgement receipt</option><option value="submission_evidence">Submission evidence</option><option value="other">Other</option></flux:select>
                <flux:input wire:model="document" type="file" accept="application/pdf" label="Upload PDF" />
            </div>
            @if ($editingTender)
                <div class="space-y-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-800"><flux:text class="font-semibold">Documents and history</flux:text><div class="space-y-2 text-sm">@forelse ($editingTender->documents as $document)<div class="flex items-center justify-between gap-3"><a class="text-emerald-700 hover:underline" href="{{ \Illuminate\Support\Facades\Storage::url($document->file_path) }}" target="_blank">{{ str_replace('_', ' ', ucfirst($document->document_type)) }}</a><flux:button wire:click="deleteDocument({{ $document->id }})" type="button" variant="ghost" size="xs" icon="trash">Remove</flux:button></div>@empty<span class="text-zinc-500">No documents uploaded yet.</span>@endforelse</div>@if ($editingTender->quoteItems()->exists() && $editingTender->documents->contains('document_type', 'quotation') && $editingTender->quote_status === 'draft')<div class="border-t border-zinc-200 pt-3 dark:border-zinc-800"><flux:button wire:click="sendQuoteToCustomer({{ $editingTender->id }})" type="button" variant="primary" icon="paper-airplane">Upload &amp; send quote to customer</flux:button><flux:text class="mt-2 text-xs text-zinc-500">The customer will receive an in-app and email notification with the quotation PDF.</flux:text></div>@endif<div class="border-t border-zinc-200 pt-3 dark:border-zinc-800">@forelse ($editingTender->activities->sortByDesc('created_at')->take(5) as $activity)<div class="flex justify-between gap-3 py-1 text-xs text-zinc-500"><span>{{ $activity->description }}</span><span class="whitespace-nowrap">{{ $activity->created_at->format('d M Y H:i') }}</span></div>@empty<span class="text-xs text-zinc-500">No activity recorded yet.</span>@endforelse</div></div>
            @endif

            <div class="flex justify-end gap-3"><flux:button type="button" wire:click="closeForm" variant="ghost">Cancel</flux:button><flux:button type="submit" variant="primary" icon="check">Save procurement record</flux:button></div>
        </form>
    </flux:modal>
</div>
