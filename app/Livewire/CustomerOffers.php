<?php

namespace App\Livewire;

use App\Models\ProcurementRequest;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class CustomerOffers extends Component
{
    public function accept(int $requestId): void
    {
        $request = $this->customerRequest($requestId);
        abort_unless($request->getAttribute('status') === 'quote_sent', 422);
        $request->update(['customer_decision' => 'accepted', 'responded_at' => now(), 'status' => 'accepted']);
        Flux::toast(variant: 'success', text: __('Deal accepted. Please complete payment using the control number.'));
    }

    public function reject(int $requestId): void
    {
        $request = $this->customerRequest($requestId);
        abort_unless($request->getAttribute('status') === 'quote_sent', 422);
        $request->update(['customer_decision' => 'rejected', 'responded_at' => now(), 'status' => 'rejected']);
        Flux::toast(variant: 'success', text: __('Deal rejected.'));
    }

    private function customerRequest(int $requestId): ProcurementRequest
    {
        return ProcurementRequest::query()
            ->where('user_id', Auth::id())
            ->findOrFail($requestId);
    }

    public function render(): View
    {
        return view('livewire.customer-offers', [
            'requests' => ProcurementRequest::query()
                ->where('user_id', Auth::id())
                ->where('type', 'tender')
                ->latest('responded_at')
                ->latest()
                ->get(),
        ]);
    }
}
