<div class="flex min-h-full w-full flex-1 flex-col gap-8 bg-zinc-50 p-4 dark:bg-zinc-950 lg:p-8">
    <div class="border-b border-zinc-200 pb-7 dark:border-zinc-800">
        <flux:heading size="xl">My quotations</flux:heading>
        <flux:subheading class="mt-2">Review the quotation PDF, price and payment control number sent by Link-Tech.</flux:subheading>
    </div>

    <div class="grid gap-5 lg:grid-cols-2">
        @forelse ($requests as $request)
            <flux:card class="space-y-5 bg-white dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <flux:heading size="lg">{{ $request->title }}</flux:heading>
                        <flux:text class="mt-1 text-sm text-zinc-500">{{ ucfirst(str_replace('_', ' ', $request->status)) }}</flux:text>
                    </div>
                    <flux:badge color="{{ $request->customer_decision === 'accepted' ? 'green' : ($request->customer_decision === 'rejected' ? 'red' : 'amber') }}">{{ $request->customer_decision ? ucfirst($request->customer_decision) : 'Action required' }}</flux:badge>
                </div>

                <div class="grid gap-3 rounded-xl bg-zinc-50 p-4 text-sm dark:bg-zinc-800/60 sm:grid-cols-2">
                    <div><span class="text-zinc-500">Quoted amount</span><div class="mt-1 font-semibold">{{ $request->quoted_amount !== null ? 'TZS '.number_format((float) $request->quoted_amount, 2) : 'Awaiting quotation' }}</div></div>
                    <div><span class="text-zinc-500">Payment status</span><div class="mt-1 font-semibold">{{ ucfirst($request->payment_status) }}</div></div>
                    <div class="sm:col-span-2"><span class="text-zinc-500">Payment control number</span><div class="mt-1 font-mono text-lg font-bold tracking-wide">{{ $request->control_number ?: 'Will appear with quotation' }}</div></div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-800">
                    @if ($request->quote_document_path)
                        <flux:button href="{{ \Illuminate\Support\Facades\Storage::url($request->quote_document_path) }}" target="_blank" variant="subtle" icon="document-arrow-down">Open quotation PDF</flux:button>
                    @else
                        <flux:text class="text-zinc-500">Quotation is being prepared by admin.</flux:text>
                    @endif
                    @if ($request->status === 'quote_sent')
                        <div class="flex gap-2">
                            <flux:button wire:click="reject({{ $request->id }})" variant="ghost" icon="x-mark">Reject</flux:button>
                            <flux:button wire:click="accept({{ $request->id }})" variant="primary" icon="check">Accept deal</flux:button>
                        </div>
                    @elseif ($request->status === 'accepted' || $request->status === 'paid')
                        <flux:text class="font-medium text-emerald-600">{{ $request->status === 'paid' ? 'Payment received' : 'Accepted, awaiting payment' }}</flux:text>
                    @endif
                </div>
            </flux:card>
        @empty
            <div class="rounded-2xl border border-dashed border-zinc-300 bg-white p-12 text-center dark:border-zinc-800 dark:bg-zinc-900"><flux:heading size="lg">No quotations yet</flux:heading><flux:subheading class="mt-2">Your quotation PDF and payment details will appear here after admin review.</flux:subheading></div>
        @endforelse
    </div>
</div>
