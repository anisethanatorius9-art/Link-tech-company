<div>
    <flux:modal wire:model="showModal" class="space-y-6 md:w-[600px]">
        @if ($tender)
        <div>
            <flux:heading size="lg">Submit tender proposal</flux:heading>
            <flux:subheading class="mt-1">Tender: <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $tender->title }}</span> ({{ $tender->reference_no }})</flux:subheading>
        </div>
        <form wire:submit="submitBid" class="space-y-4">
            <flux:input label="Company / vendor name" wire:model="company_name" required />
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <flux:input type="email" label="Email address" wire:model="email" required />
                <flux:input label="Phone number" wire:model="phone_number" required />
            </div>
            <flux:input type="number" step="0.01" label="Bid amount (TZS / USD)" wire:model="proposed_amount" icon="currency-dollar" required />
            <flux:separator text="Tender documents (PDF)" />
            <div>
                <flux:label>Technical proposal (PDF, max 10MB)</flux:label>
                <input type="file" wire:model="technical_proposal" accept="application/pdf" class="mt-1 block w-full text-sm text-zinc-500 file:mr-4 file:rounded-md file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:font-semibold file:text-emerald-700" />
                <flux:error name="technical_proposal" />
                <div wire:loading wire:target="technical_proposal" class="mt-1 text-xs text-emerald-600">Uploading file...</div>
            </div>
            <div>
                <flux:label>Financial proposal (PDF, max 10MB)</flux:label>
                <input type="file" wire:model="financial_proposal" accept="application/pdf" class="mt-1 block w-full text-sm text-zinc-500 file:mr-4 file:rounded-md file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:font-semibold file:text-emerald-700" />
                <flux:error name="financial_proposal" />
                <div wire:loading wire:target="financial_proposal" class="mt-1 text-xs text-emerald-600">Uploading file...</div>
            </div>
            <div class="flex justify-end gap-3 border-t border-zinc-200 pt-4 dark:border-zinc-800">
                <flux:button variant="ghost" wire:click="$set('showModal', false)">Cancel</flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submitBid">Submit proposal</span>
                    <span wire:loading wire:target="submitBid">Submitting...</span>
                </flux:button>
            </div>
        </form>
        @endif
    </flux:modal>
</div>
