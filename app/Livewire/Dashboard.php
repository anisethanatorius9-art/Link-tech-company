<?php

namespace App\Livewire;

use App\Models\Tender;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public string $locale = 'en';

    public bool $showTenderForm = false;

    public string $referenceNumber = '';

    public string $title = '';

    public string $clientName = '';

    public string $submissionDeadline = '';

    public string $requirements = '';

    public function getGreetingProperty(): string
    {
        return match (true) {
            now()->hour >= 5 && now()->hour < 12 => __('Good morning'),
            now()->hour >= 12 && now()->hour < 17 => __('Good afternoon'),
            default => __('Good evening'),
        };
    }

    public function mount(): void
    {
        $this->locale = session('locale', 'en');
    }

    public function setLanguage(string $locale): void
    {
        abort_unless(in_array($locale, ['en', 'sw', 'zh', 'fr'], true), 422);

        $this->locale = $locale;
        session(['locale' => $this->locale]);
        app()->setLocale($this->locale);
    }

    public function updatedLocale(string $locale): void
    {
        $this->setLanguage($locale);
    }

    public function openTenderForm(): void
    {
        $this->reset(['referenceNumber', 'title', 'clientName', 'submissionDeadline', 'requirements']);
        $this->showTenderForm = true;
    }

    public function createTender(): void
    {
        $this->authorize('create', Tender::class);

        $validated = $this->validate([
            'referenceNumber' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'clientName' => ['required', 'string', 'max:255'],
            'submissionDeadline' => ['required', 'date'],
            'requirements' => ['nullable', 'string', 'max:5000'],
        ]);

        Tender::query()->create([
            'reference_no' => $validated['referenceNumber'],
            'reference_number' => $validated['referenceNumber'],
            'title' => $validated['title'],
            'client_name' => $validated['clientName'],
            'description' => $validated['requirements'] ?: null,
            'scope_of_work' => $validated['requirements'] ?: null,
            'submission_deadline' => $validated['submissionDeadline'],
            'deadline' => $validated['submissionDeadline'],
            'status' => 'draft',
            'created_by_id' => Auth::id(),
        ]);

        $this->showTenderForm = false;
        Flux::toast(variant: 'success', text: 'Tender assignment saved as draft.');
    }

    public function render()
    {
        $currentUser = Auth::user();
        $isAdmin = $currentUser instanceof User && $currentUser->isAdmin();
        $tenders = Tender::query()
            ->when(! $isAdmin, fn ($query) => $query->where(fn ($query) => $query->where('assigned_officer_id', Auth::id())->orWhere('created_by_id', Auth::id())))
            ->latest()
            ->get();
        $expiring = $tenders->filter(fn (Tender $tender): bool => $tender->submission_deadline?->between(now(), now()->addDays(7)) && ! in_array($tender->status, ['awarded', 'lost', 'cancelled'], true));
        $submittedThisMonth = $tenders->where('status', 'submitted')->filter(fn (Tender $tender): bool => $tender->submitted_at?->isCurrentMonth() ?? false)->count();
        $decided = $tenders->whereIn('status', ['awarded', 'lost'])->count();

        return view('livewire.dashboard', [
            'inProgress' => $tenders->whereIn('status', ['draft', 'under_evaluation'])->count(),
            'submitted' => $submittedThisMonth,
            'expiring' => $expiring->count(),
            'won' => $tenders->where('status', 'awarded')->count(),
            'winRate' => $decided ? round(($tenders->where('status', 'awarded')->count() / $decided) * 100) : 0,
            'urgentTenders' => $expiring->sortBy('submission_deadline')->take(5),
        ]);
    }
}
