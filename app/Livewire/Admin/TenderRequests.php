<?php

namespace App\Livewire\Admin;

use App\Models\Tender;
use Flux\Flux;
use Livewire\Component;

class TenderRequests extends Component
{
    public function approve(int $tenderId): void
    {
        $tender = Tender::query()->where('status', 'pending')->findOrFail($tenderId);
        $tender->update([
            'status' => 'active',
            'published_at' => now(),
        ]);

        Flux::toast(variant: 'success', text: 'Tender approved and published.');
    }

    public function reject(int $tenderId): void
    {
        $tender = Tender::query()->where('status', 'pending')->findOrFail($tenderId);
        $tender->update(['status' => 'rejected']);

        Flux::toast(variant: 'success', text: 'Tender request rejected.');
    }

    public function render()
    {
        return view('livewire.admin.tender-requests', [
            'tenders' => Tender::query()->where('status', 'pending')->latest()->get(),
        ]);
    }
}
