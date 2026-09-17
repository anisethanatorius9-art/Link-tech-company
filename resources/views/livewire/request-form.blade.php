<div class="flex min-h-full w-full flex-1 flex-col gap-8 p-4 lg:p-8">
    <div class="border-b border-zinc-200 pb-8 dark:border-zinc-700">
        <flux:heading size="xl" level="1">{{ $heading }}</flux:heading>
        <flux:text class="mt-3 max-w-2xl">{{ $description }}</flux:text>
    </div>

    <flux:card class="max-w-3xl border-zinc-200 p-6 dark:border-zinc-700">
        <form wire:submit="save" class="space-y-6">
            <flux:input wire:model="title" label="Request title" placeholder="e.g. New network switches" required />

            <flux:select wire:model="category" label="Category" placeholder="Choose a category" required>
                <flux:select.option value="networking">Networking equipment</flux:select.option>
                <flux:select.option value="computers">Computers and accessories</flux:select.option>
                <flux:select.option value="electrical">Electrical equipment</flux:select.option>
                <flux:select.option value="staffing">Staff coverage</flux:select.option>
                <flux:select.option value="other">Other</flux:select.option>
            </flux:select>

            <flux:input wire:model="quantity" type="number" min="1" label="Quantity" placeholder="Optional" />
            @if ($type === 'supplier_order')
                <flux:input wire:model="supplierName" label="Supplier name" placeholder="e.g. Link-Tech Company Limited" required />
                <div class="grid gap-4 sm:grid-cols-2"><flux:input wire:model="unitCost" type="number" min="0" step="0.01" label="Agreed cost price (TSH)" required /><flux:input wire:model="unitPrice" type="number" min="0" step="0.01" label="Selling price (TSH)" /></div>
            @elseif ($type === 'inventory_restock')
                <div class="grid gap-4 sm:grid-cols-2"><flux:input wire:model="unitCost" type="number" min="0" step="0.01" label="Purchase price (TSH)" required /><flux:input wire:model="unitPrice" type="number" min="0" step="0.01" label="Selling price (TSH)" required /></div>
            @elseif ($type === 'staff_shift')
                <div class="grid gap-4 sm:grid-cols-3"><flux:input wire:model="actualCash" type="number" min="0" step="0.01" label="Actual cash (TSH)" required /><flux:input wire:model="actualMobile" type="number" min="0" step="0.01" label="Actual mobile (TSH)" required /><flux:input wire:model="actualCard" type="number" min="0" step="0.01" label="Actual card (TSH)" required /></div>
            @endif
            <flux:textarea wire:model="notes" label="Notes" placeholder="Add only the details needed for this request." rows="5" />

            <flux:button type="submit" variant="primary" icon="paper-airplane">
                {{ $type === 'supplier_order' ? 'Create purchase order' : ($type === 'inventory_restock' ? 'Confirm goods received' : 'Close shift') }}
            </flux:button>
        </form>
    </flux:card>
</div>
