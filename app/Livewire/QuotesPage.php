<?php

namespace App\Livewire;

use App\Models\Tender;
use App\Models\TenderQuoteItem;
use App\Models\User;
use App\Notifications\TenderQuoteDecisionNotification;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class QuotesPage extends Component
{
    public ?int $selectedTenderId = null;

    /** @var array<int, array{description: string, unit: string, quantity: int|float, unit_price: int|float}> */
    public array $lineItems = [['description' => '', 'unit' => 'item', 'quantity' => 1, 'unit_price' => 0]];

    public string $quoteFeedback = '';

    public function mount(?Tender $tender = null): void
    {
        $this->selectedTenderId = $tender?->id;
        if ($tender) {
            $items = $tender->quoteItems()->latest('version')->get()->unique('description')->values();
            if ($items->isNotEmpty()) {
                $this->lineItems = $items->map(fn (TenderQuoteItem $item): array => ['description' => $item->description, 'unit' => $item->unit, 'quantity' => (float) $item->quantity, 'unit_price' => (float) $item->unit_price])->all();
            }
        }
    }

    public function addItem(): void
    {
        $this->lineItems[] = ['description' => '', 'unit' => 'item', 'quantity' => 1, 'unit_price' => 0];
    }

    public function removeItem(int $index): void
    {
        unset($this->lineItems[$index]);
        $this->lineItems = array_values($this->lineItems);
    }

    public function saveQuote(): void
    {
        $this->validate(['selectedTenderId' => ['required', 'exists:tenders,id'], 'lineItems.*.description' => ['required', 'string', 'max:255'], 'lineItems.*.unit' => ['required', 'string', 'max:50'], 'lineItems.*.quantity' => ['required', 'numeric', 'min:0.01'], 'lineItems.*.unit_price' => ['required', 'numeric', 'min:0']]);
        $tender = Tender::query()->findOrFail($this->selectedTenderId);
        $user = Auth::user();
        abort_unless($user instanceof User && $user->isAdmin(), 403);
        $version = ((int) $tender->quoteItems()->max('version')) + 1;
        foreach ($this->lineItems as $item) {
            $tender->quoteItems()->create([...$item, 'version' => $version, 'vat_rate' => 18]);
        }
        $tender->update(['quoted_amount' => $this->grandTotal(), 'quote_status' => 'draft', 'quote_decision_at' => null, 'quote_feedback' => null]);
        Flux::toast(variant: 'success', text: "Quotation version {$version} saved. Upload it from Procurement to send it.");
    }

    public function acceptQuote(): void
    {
        $tender = $this->customerTender();
        $tender->update(['quote_status' => 'accepted', 'quote_decision_at' => now(), 'quote_feedback' => null]);
        $this->notifyAdmins($tender->fresh());
        Flux::toast(variant: 'success', text: 'Quotation accepted.');
    }

    public function rejectQuote(): void
    {
        $this->validate(['quoteFeedback' => ['nullable', 'string', 'max:2000']]);
        $tender = $this->customerTender();
        $tender->update(['quote_status' => 'rejected', 'quote_decision_at' => now(), 'quote_feedback' => $this->quoteFeedback ?: null]);
        $this->notifyAdmins($tender->fresh());
        Flux::toast(variant: 'success', text: 'Quotation rejected and feedback sent.');
    }

    private function notifyAdmins(Tender $tender): void
    {
        User::query()->where('role', 'admin')->get()->each(fn (User $admin) => $admin->notify(new TenderQuoteDecisionNotification($tender)));
    }

    private function customerTender(): Tender
    {
        $tender = Tender::query()->findOrFail($this->selectedTenderId);
        $user = Auth::user();
        abort_unless($user instanceof User && ! $user->isAdmin() && $tender->created_by_id === $user->id && $tender->quote_status === 'sent', 403);

        return $tender;
    }

    public function subtotal(): float
    {
        return collect($this->lineItems)->sum(fn (array $item): float => (float) $item['quantity'] * (float) $item['unit_price']);
    }

    public function vat(): float
    {
        return $this->subtotal() * 0.18;
    }

    public function grandTotal(): float
    {
        return $this->subtotal() + $this->vat();
    }

    public function render(): View
    {
        $user = Auth::user();
        $isAdmin = $user instanceof User && $user->isAdmin();
        $tenders = Tender::query()->when(! $isAdmin, fn ($query) => $query->where('created_by_id', Auth::id())->whereIn('quote_status', ['sent', 'accepted', 'rejected']))->orderBy('title')->get();
        $selected = $this->selectedTenderId ? Tender::query()->with('quoteItems')->find($this->selectedTenderId) : null;

        return view('livewire.quotes-page', ['tenders' => $tenders, 'selectedTender' => $selected, 'versions' => $selected?->quoteItems->groupBy('version') ?? collect()]);
    }
}
