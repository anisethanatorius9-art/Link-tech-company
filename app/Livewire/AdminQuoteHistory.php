<?php

namespace App\Livewire;

use App\Models\Tender;
use Illuminate\View\View;
use Livewire\Component;

class AdminQuoteHistory extends Component
{
    public function render(): View
    {
        $quotes = Tender::query()
            ->with('creator')
            ->where('quote_status', '!=', 'draft')
            ->latest('quote_sent_at')
            ->get();

        return view('livewire.admin-quote-history', [
            'quotes' => $quotes,
            'total' => $quotes->count(),
            'pending' => $quotes->where('quote_status', 'sent')->count(),
            'accepted' => $quotes->where('quote_status', 'accepted')->count(),
            'rejected' => $quotes->where('quote_status', 'rejected')->count(),
        ]);
    }
}
