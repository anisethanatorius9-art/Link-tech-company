<?php

namespace App\Livewire\Admin;

use App\Models\Tender;
use App\Models\TenderSubmission;
use App\Notifications\TenderStatusUpdatedNotification;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class TenderSubmissions extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public string $selectedTenderId = 'all';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedTenderId(): void
    {
        $this->resetPage();
    }

    public function updateStatus(int $submissionId, string $newStatus): void
    {
        validator(['status' => $newStatus], [
            'status' => ['required', Rule::in(['under_review', 'approved', 'rejected'])],
        ])->validate();

        $submission = TenderSubmission::query()->with('tender')->findOrFail($submissionId);
        $submission->update(['status' => $newStatus]);
        $submission->notify(new TenderStatusUpdatedNotification($submission));

        Flux::toast(variant: 'success', text: "Status updated to {$newStatus} and notification queued.");
    }

    public function acceptBid(int $submissionId): void
    {
        $submission = DB::transaction(function () use ($submissionId): TenderSubmission {
            $submission = TenderSubmission::query()->with('tender')->lockForUpdate()->findOrFail($submissionId);

            TenderSubmission::query()
                ->where('tender_id', $submission->tender_id)
                ->where('id', '!=', $submission->id)
                ->update(['status' => 'rejected']);

            $submission->update(['status' => 'approved']);

            return $submission->fresh(['tender']);
        });

        $submission->notify(new TenderStatusUpdatedNotification($submission));
        Flux::toast(variant: 'success', text: "Bid from {$submission->company_name} accepted. Other bids were rejected.");
    }

    public function render(): View
    {
        $submissions = TenderSubmission::query()
            ->with('tender')
            ->when($this->search !== '', function ($query): void {
                $query->where(function ($query): void {
                    $query->where('company_name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter !== 'all', fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->selectedTenderId !== 'all', fn ($query) => $query->where('tender_id', $this->selectedTenderId))
            ->latest()
            ->paginate(10);

        return view('livewire.admin.tender-submissions', [
            'submissions' => $submissions,
            'tenders' => Tender::query()->select('id', 'title', 'reference_no')->orderBy('title')->get(),
            'total' => TenderSubmission::query()->count(),
            'pending' => TenderSubmission::query()->where('status', 'submitted')->count(),
            'approved' => TenderSubmission::query()->where('status', 'approved')->count(),
        ]);
    }
}
