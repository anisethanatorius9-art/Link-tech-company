<?php

namespace App\Livewire;

use App\Models\ProcurementRequest;
use App\Models\InventoryItem;
use App\Models\Shift;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class RequestForm extends Component
{
    public string $type;

    public string $title = '';

    public string $category = '';

    public ?int $quantity = null;

    public string $notes = '';

    public string $supplierName = '';

    public ?float $unitCost = null;

    public ?float $unitPrice = null;

    public ?float $actualCash = null;

    public ?float $actualMobile = null;

    public ?float $actualCard = null;

    public function mount(string $type): void
    {
        abort_unless(in_array($type, ['supplier_order', 'staff_shift', 'inventory_restock'], true), 404);

        $this->type = $type;
    }

    public function save(): void
    {
        $validated = $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(['networking', 'computers', 'electrical', 'staffing', 'other'])],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'supplierName' => ['nullable', 'string', 'max:255'],
            'unitCost' => ['nullable', 'numeric', 'min:0'],
            'unitPrice' => ['nullable', 'numeric', 'min:0'],
            'actualCash' => ['nullable', 'numeric', 'min:0'],
            'actualMobile' => ['nullable', 'numeric', 'min:0'],
            'actualCard' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($this->type === 'supplier_order') {
            validator($validated, ['supplierName' => ['required'], 'unitCost' => ['required'], 'unitPrice' => ['required']])->validate();
        }

        if ($this->type === 'inventory_restock') {
            validator($validated, ['quantity' => ['required'], 'unitCost' => ['required'], 'unitPrice' => ['required']])->validate();
        }

        if ($this->type === 'staff_shift') {
            validator($validated, ['actualCash' => ['required'], 'actualMobile' => ['required'], 'actualCard' => ['required']])->validate();
        }

        DB::transaction(function () use ($validated): void {
            if ($this->type === 'supplier_order') {
                ProcurementRequest::create([
                    'title' => $validated['title'], 'category' => $validated['category'], 'quantity' => $validated['quantity'],
                    'unit_cost' => $validated['unitCost'], 'unit_price' => $validated['unitPrice'], 'supplier_name' => $validated['supplierName'],
                    'notes' => $validated['notes'], 'user_id' => Auth::id(), 'type' => $this->type, 'status' => 'ordered',
                ]);
            } elseif ($this->type === 'inventory_restock') {
                $quantity = (int) ($validated['quantity'] ?: 0);
                abort_unless($quantity > 0, 422, 'Received quantity is required.');
                InventoryItem::query()->updateOrCreate(
                    ['name' => $validated['title']],
                    ['category' => $validated['category'], 'stock' => DB::raw('stock + '.$quantity), 'unit_cost' => $validated['unitCost'] ?: 0, 'unit_price' => $validated['unitPrice'] ?: 0, 'active' => true],
                );
                ProcurementRequest::create([
                    'title' => $validated['title'], 'category' => $validated['category'], 'quantity' => $quantity, 'received_quantity' => $quantity,
                    'unit_cost' => $validated['unitCost'], 'unit_price' => $validated['unitPrice'], 'received_at' => now(), 'notes' => $validated['notes'],
                    'user_id' => Auth::id(), 'type' => $this->type, 'status' => 'completed',
                ]);
            } else {
                $shift = Shift::query()->where('user_id', Auth::id())->where('status', 'open')->latest()->first();
                $shift ??= Shift::create(['user_id' => Auth::id(), 'opened_at' => now(), 'status' => 'open']);
                $expected = (float) $shift->expected_cash + (float) $shift->expected_mobile + (float) $shift->expected_card;
                $actual = (float) ($validated['actualCash'] ?: 0) + (float) ($validated['actualMobile'] ?: 0) + (float) ($validated['actualCard'] ?: 0);
                $shift->update(['actual_cash' => $validated['actualCash'] ?: 0, 'actual_mobile' => $validated['actualMobile'] ?: 0, 'actual_card' => $validated['actualCard'] ?: 0, 'variance' => $actual - $expected, 'status' => 'closed', 'closed_at' => now(), 'notes' => $validated['notes']]);
            }
        });

        $this->reset('title', 'category', 'quantity', 'notes', 'supplierName', 'unitCost', 'unitPrice', 'actualCash', 'actualMobile', 'actualCard');
        Flux::toast(variant: 'success', text: __($this->type === 'staff_shift' ? 'Shift closed successfully.' : ($this->type === 'inventory_restock' ? 'Goods received and stock updated.' : 'Purchase order created.')));
    }

    public function render()
    {
        return view('livewire.request-form', [
            'heading' => match ($this->type) {
                'supplier_order' => 'New supplier order',
                'staff_shift' => 'Staff shift review',
                'inventory_restock' => 'Inventory restock',
            },
            'description' => match ($this->type) {
                'supplier_order' => 'Create a request for networking, computer or electrical equipment from a supplier.',
                'staff_shift' => 'Record a staff coverage request for receiving and processing incoming orders.',
                'inventory_restock' => 'Request replenishment for equipment needed by the company.',
            },
        ]);
    }
}
