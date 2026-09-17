<?php

namespace App\Livewire;

use App\Models\Tender;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReportsPage extends Component
{
    public string $period = 'year';

    public function render()
    {
        $user = Auth::user();
        $isAdmin = $user instanceof User && $user->isAdmin();
        $query = Tender::query()->when(! $isAdmin, fn ($query) => $query->where(fn ($query) => $query->where('assigned_officer_id', Auth::id())->orWhere('created_by_id', Auth::id())));
        $tenders = (clone $query)->get();
        $won = $tenders->where('status', 'awarded')->count();
        $lost = $tenders->where('status', 'lost')->count();
        $decided = $won + $lost;
        $months = collect(range(0, 5))->map(function (int $offset) use ($tenders): array {
            $date = now()->subMonths(5 - $offset);

            return ['label' => $date->format('M'), 'value' => (float) $tenders->filter(fn (Tender $tender): bool => $tender->submitted_at?->isSameMonth($date) ?? false)->sum('quoted_amount')];
        });
        $max = max(1, $months->max('value'));
        $officers = $tenders->groupBy('assigned_officer_id')->map(fn ($items) => ['name' => $items->first()->assignedOfficer?->name ?: 'Unassigned', 'count' => $items->count(), 'won' => $items->where('status', 'awarded')->count()])->values();

        return view('livewire.reports-page', ['total' => $tenders->count(), 'won' => $won, 'lost' => $lost, 'winRate' => $decided ? round($won / $decided * 100) : 0, 'averageWin' => $won ? $tenders->where('status', 'awarded')->avg('contract_value') : 0, 'months' => $months->map(fn (array $month): array => [...$month, 'height' => max(4, $month['value'] / $max * 100)]), 'officers' => $officers]);
    }
}
