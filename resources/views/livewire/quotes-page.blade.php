<div class="min-h-full w-full flex-1 bg-[#f6f7f2] p-4 text-[#17221b] lg:p-8 dark:bg-zinc-950 dark:text-zinc-100">
    <div class="mx-auto max-w-[1400px] space-y-6">
        <div class="flex flex-col justify-between gap-5 border-b border-[#dce3d8] pb-6 sm:flex-row sm:items-end dark:border-zinc-800">
            <div>
                <flux:text class="mb-2 text-xs font-bold uppercase tracking-[.22em] text-[#2d7a57]">Commercial documents</flux:text>
                <flux:heading size="xl">Quotation studio</flux:heading>
                <flux:subheading class="mt-2 max-w-2xl">Build a polished quotation, review totals in real time, and share the final PDF with your client.</flux:subheading>
            </div>

            <div class="flex items-center gap-3">
                <flux:button href="{{ route('user.tenders') }}" variant="ghost" icon="arrow-left">Back to tenders</flux:button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <flux:card class="p-5">
                <div class="flex items-center justify-between">
                    <flux:text class="text-sm text-zinc-500">Selected tender</flux:text>
                    <flux:badge color="emerald">Live</flux:badge>
                </div>
                <div class="mt-4">
                    <flux:heading size="lg">{{ $selectedTender ? ($selectedTender->reference_number ?: $selectedTender->reference_no) : 'No selection' }}</flux:heading>
                    <flux:text class="mt-1 text-sm text-zinc-500">{{ $selectedTender ? $selectedTender->title : 'Choose a tender to continue' }}</flux:text>
                </div>
            </flux:card>

            <flux:card class="p-5">
                <flux:text class="text-sm text-zinc-500">Subtotal</flux:text>
                <div class="mt-4 flex items-end justify-between gap-3">
                    <flux:heading size="lg">{{ number_format($this->subtotal(), 2) }}</flux:heading>
                    <flux:text class="text-sm uppercase tracking-wide text-zinc-500">{{ $selectedTender?->currency ?? 'TZS' }}</flux:text>
                </div>
            </flux:card>

            <flux:card class="p-5">
                <flux:text class="text-sm text-zinc-500">Grand total</flux:text>
                <div class="mt-4 flex items-end justify-between gap-3">
                    <flux:heading size="lg" class="text-emerald-700">{{ number_format($this->grandTotal(), 2) }}</flux:heading>
                    <flux:text class="text-sm uppercase tracking-wide text-zinc-500">VAT 18%</flux:text>
                </div>
            </flux:card>
        </div>

        <flux:card class="p-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
                <div class="w-full lg:max-w-xl">
                    <flux:select wire:model.live="selectedTenderId" label="Tender">
                        <option value="">Select a tender</option>
                        @foreach ($tenders as $tender)
                            <option value="{{ $tender->id }}">{{ $tender->reference_number ?: $tender->reference_no }} · {{ $tender->title }}</option>
                        @endforeach
                    </flux:select>
                </div>

                @if ($selectedTender)
                    <div class="flex flex-wrap gap-3">
                        <flux:button href="{{ route('quotes.pdf', ['tender' => $selectedTender->id]) }}" variant="primary" icon="document-arrow-down">View PDF</flux:button>
                        <flux:button href="{{ route('quotes.excel', ['tender' => $selectedTender->id]) }}" variant="ghost" icon="table-cells">Excel</flux:button>
                    </div>
                @endif
            </div>
        </flux:card>

        @if (auth()->user()->isAdmin())
            <div class="grid gap-6 xl:grid-cols-[1.75fr_0.95fr]">
                <flux:card class="overflow-hidden p-0">
                    <div class="flex items-center justify-between border-b border-zinc-200 p-5 dark:border-zinc-800">
                        <div>
                            <flux:heading size="lg">Quotation builder</flux:heading>
                            <flux:text class="mt-1">Add products or services line by line.</flux:text>
                        </div>
                        <flux:button wire:click="addItem" variant="subtle" size="sm" icon="plus">Add line</flux:button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[760px] text-left text-sm">
                            <thead class="bg-[#edf7ef] text-xs uppercase tracking-wider text-zinc-500 dark:bg-zinc-800">
                                <tr>
                                    <th class="p-4">Description</th>
                                    <th class="p-4">Unit</th>
                                    <th class="p-4">Qty</th>
                                    <th class="p-4">Unit price</th>
                                    <th class="p-4 text-right">Total</th>
                                    <th class="w-12 p-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                @foreach ($lineItems as $index => $item)
                                    <tr>
                                        <td class="p-3">
                                            <flux:input wire:model.live="lineItems.{{ $index }}.description" placeholder="Item or service" />
                                        </td>
                                        <td class="p-3">
                                            <flux:input wire:model.live="lineItems.{{ $index }}.unit" placeholder="item" />
                                        </td>
                                        <td class="p-3">
                                            <flux:input wire:model.live="lineItems.{{ $index }}.quantity" type="number" min="0.01" step="0.01" />
                                        </td>
                                        <td class="p-3">
                                            <flux:input wire:model.live="lineItems.{{ $index }}.unit_price" type="number" min="0" step="0.01" />
                                        </td>
                                        <td class="p-3 text-right font-semibold">
                                            {{ number_format((float) $item['quantity'] * (float) $item['unit_price'], 2) }}
                                        </td>
                                        <td class="p-3">
                                            <flux:button wire:click="removeItem({{ $index }})" variant="ghost" size="xs" icon="trash" />
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="flex flex-wrap justify-end gap-3 border-t border-zinc-200 p-5 dark:border-zinc-800">
                        <flux:button wire:click="saveQuote" variant="primary" icon="check">Save quotation version</flux:button>
                        @if ($selectedTender && $selectedTender->quoteItems->isNotEmpty())
                            <flux:button href="{{ route('quotes.pdf', ['tender' => $selectedTender->id]) }}" variant="ghost" icon="document-arrow-down">Download PDF</flux:button>
                            <flux:button href="{{ route('quotes.excel', ['tender' => $selectedTender->id]) }}" variant="ghost" icon="table-cells">Download Excel</flux:button>
                        @endif
                    </div>
                </flux:card>

                <div class="space-y-6">
                    <flux:card class="p-5">
                        <flux:heading size="lg">Calculation summary</flux:heading>
                        <div class="mt-5 space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-zinc-500">Subtotal</span>
                                <strong>{{ number_format($this->subtotal(), 2) }} {{ $selectedTender?->currency ?? 'TZS' }}</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-500">VAT (18%)</span>
                                <strong>{{ number_format($this->vat(), 2) }} {{ $selectedTender?->currency ?? 'TZS' }}</strong>
                            </div>
                            <div class="flex justify-between border-t border-zinc-200 pt-3 text-base dark:border-zinc-800">
                                <span>Grand total</span>
                                <strong class="text-emerald-700">{{ number_format($this->grandTotal(), 2) }} {{ $selectedTender?->currency ?? 'TZS' }}</strong>
                            </div>
                        </div>
                    </flux:card>

                    <flux:card class="p-5">
                        <flux:heading size="lg">Revision history</flux:heading>
                        <div class="mt-4 space-y-3">
                            @forelse ($versions as $version => $items)
                                <div class="flex items-center justify-between gap-4 rounded-xl border border-zinc-200 p-3 text-sm dark:border-zinc-800">
                                    <span class="font-medium">Version {{ $version }}</span>
                                    <strong>{{ number_format($items->sum(fn ($item) => (float) $item->quantity * (float) $item->unit_price * 1.18), 2) }} {{ $selectedTender?->currency ?? 'TZS' }}</strong>
                                </div>
                            @empty
                                <div class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50 p-4 text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900/30">
                                    Saved versions will appear here.
                                </div>
                            @endforelse
                        </div>
                    </flux:card>
                </div>
            </div>
        @elseif ($selectedTender)
            <flux:card class="p-6">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <flux:heading size="lg">Quotation for {{ $selectedTender->title }}</flux:heading>
                        <flux:text class="mt-1">Reference {{ $selectedTender->reference_no }} · {{ $selectedTender->client_name }}</flux:text>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <flux:button href="{{ route('quotes.pdf', ['tender' => $selectedTender->id]) }}" icon="document-arrow-down" variant="primary">View PDF</flux:button>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl bg-[#edf7ef] p-5 dark:bg-emerald-950/20">
                    <flux:text class="text-sm text-zinc-500">Total including VAT</flux:text>
                    <flux:heading size="xl" class="mt-2 text-emerald-700">{{ number_format((float) $selectedTender->quoted_amount, 2) }} {{ $selectedTender->currency }}</flux:heading>
                </div>

                <div class="mt-6 space-y-5">
                    @if ($selectedTender->quote_status === 'sent')
                        <div class="flex flex-wrap gap-3">
                            <flux:button wire:click="acceptQuote" variant="primary" icon="check">Accept quotation</flux:button>
                            <flux:button wire:click="rejectQuote" variant="danger" icon="x-mark">Reject quotation</flux:button>
                        </div>

                        <flux:textarea wire:model="quoteFeedback" label="Feedback (optional)" rows="3" placeholder="Tell the admin why you are rejecting this quotation." />
                    @else
                        <div class="flex items-center gap-3">
                            <flux:badge color="{{ $selectedTender->quote_status === 'accepted' ? 'green' : 'red' }}">Quotation {{ $selectedTender->quote_status }}</flux:badge>
                        </div>

                        @if ($selectedTender->quote_feedback)
                            <flux:text class="rounded-xl bg-zinc-100 p-4 text-sm dark:bg-zinc-900/40">Feedback: {{ $selectedTender->quote_feedback }}</flux:text>
                        @endif
                    @endif
                </div>
            </flux:card>
        @else
            <flux:card class="p-8 text-center">
                <flux:heading size="lg">No quotation available</flux:heading>
                <flux:text class="mt-2">Your quotation will appear here after the admin sends it.</flux:text>
            </flux:card>
        @endif
    </div>
</div>
