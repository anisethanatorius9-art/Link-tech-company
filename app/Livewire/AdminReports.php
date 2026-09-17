<?php

namespace App\Livewire;

use App\Models\ProcurementRequest;
use Livewire\Component;

class AdminReports extends Component
{
    public function render()
    {
        return view('livewire.admin.reports', [
            'total' => ProcurementRequest::query()->count(),
            'pending' => ProcurementRequest::query()->where('status', 'pending')->count(),
            'completed' => ProcurementRequest::query()->where('status', 'completed')->count(),
            'quantity' => ProcurementRequest::query()->sum('quantity'),
        ]);
    }
}
