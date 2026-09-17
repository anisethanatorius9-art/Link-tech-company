<?php

namespace App\Livewire;

use App\Models\Tender;
use App\Models\User;
use App\Notifications\TenderSubmittedNotification;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TendersPage extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = 'all';

    public string $dateFilter = 'all';

    public bool $showForm = false;

    public string $referenceNumber = '';

    public string $title = '';

    public string $clientName = '';

    public string $submissionDeadline = '';

    public string $requirements = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedDateFilter(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->reset(['referenceNumber', 'title', 'clientName', 'submissionDeadline', 'requirements']);
        $this->showForm = true;
    }

    public function createTender(): void
    {
        $this->authorize('create', Tender::class);
        $data = $this->validate([
            'referenceNumber' => ['required', 'string', 'max:100'], 'title' => ['required', 'string', 'max:255'],
            'clientName' => ['required', 'string', 'max:255'], 'submissionDeadline' => ['required', 'date'],
            'requirements' => ['nullable', 'string', 'max:5000'],
        ]);
        $tender = Tender::query()->create([
            'reference_no' => $data['referenceNumber'], 'reference_number' => $data['referenceNumber'],
            'title' => $data['title'], 'client_name' => $data['clientName'],
            'description' => $data['requirements'] ?: null, 'scope_of_work' => $data['requirements'] ?: null,
            'submission_deadline' => $data['submissionDeadline'], 'deadline' => $data['submissionDeadline'],
            'status' => 'submitted', 'submitted_at' => now(), 'created_by_id' => Auth::id(),
        ]);
        $currentUser = Auth::user();
        if ($currentUser instanceof User && ! $currentUser->isAdmin()) {
            User::query()->where('role', 'admin')->get()->each(fn (User $admin) => $admin->notify(new TenderSubmittedNotification($tender)));
        }
        $this->showForm = false;
        Flux::toast(variant: 'success', text: 'Tender saved as draft.');
    }

    public function render()
    {
        $user = Auth::user();
        $isAdmin = $user instanceof User && $user->isAdmin();
        $query = Tender::query()->with('assignedOfficer')
            ->when(! $isAdmin, fn ($query) => $query->where(fn ($query) => $query->where('assigned_officer_id', Auth::id())->orWhere('created_by_id', Auth::id())))
            ->when($this->search !== '', fn ($query) => $query->where(fn ($query) => $query->where('reference_number', 'like', '%'.$this->search.'%')->orWhere('reference_no', 'like', '%'.$this->search.'%')->orWhere('title', 'like', '%'.$this->search.'%')->orWhere('client_name', 'like', '%'.$this->search.'%')))
            ->when($this->status !== 'all', fn ($query) => $query->where('status', $this->status))
            ->when($this->dateFilter === 'next_7_days', fn ($query) => $query->whereBetween('submission_deadline', [now(), now()->addDays(7)]))
            ->latest('submission_deadline');
        $all = (clone $query)->get();

        return view('livewire.tenders-page', [
            'tenders' => $query->paginate(10), 'active' => $all->whereIn('status', ['draft', 'under_evaluation'])->count(),
            'pendingReview' => $all->where('status', 'submitted')->count(), 'won' => $all->where('status', 'awarded')->count(),
            'due' => $all->filter(fn (Tender $tender): bool => $tender->submission_deadline?->between(now(), now()->addDays(7)) ?? false)->count(),
        ]);
    }
}
