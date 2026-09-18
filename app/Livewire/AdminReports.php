<?php

namespace App\Livewire;

use App\Models\ProcurementRequest;
use Illuminate\View\View;
use Livewire\Component;

class AdminReports extends Component
{
    public function render(): View
    {
        return view('livewire.admin.reports', [
            'total' => ProcurementRequest::query()->count(),
            'pending' => ProcurementRequest::query()->where('status', 'pending')->count(),
            'completed' => ProcurementRequest::query()->where('status', 'completed')->count(),
            'quantity' => ProcurementRequest::query()->sum('quantity'),
        ]);
    }
}
