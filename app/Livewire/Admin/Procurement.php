<?php

namespace App\Livewire\Admin;

use App\Models\Tender;
use App\Models\TenderActivity;
use App\Models\TenderDocument;
use App\Models\User;
use App\Notifications\TenderQuoteSentNotification;
use Flux\Flux;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Procurement extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public string $statusFilter = 'all';

    public string $dateFilter = 'all';

    public bool $showForm = false;

    public ?int $editingTenderId = null;

    public string $referenceNumber = '';

    public string $title = '';

    public string $clientName = '';

    public string $publishedDate = '';

    public string $submissionDeadline = '';

    public string $estimatedValue = '';

    public string $quotedAmount = '';

    public string $currency = 'TZS';

    public string $submissionChannel = '';

    public string $confirmationReference = '';

    public string $status = 'draft';

    public string $contractValue = '';

    public string $outcomeNotes = '';

    public ?int $assignedOfficerId = null;

    public string $documentType = 'quotation';

    public ?UploadedFile $document = null;

    public function sendQuoteToCustomer(int $tenderId): void
    {
        $tender = Tender::query()->with('creator')->findOrFail($tenderId);
        abort_unless($tender->creator && $tender->quoteItems()->exists(), 422);
        abort_unless($tender->documents()->where('document_type', 'quotation')->exists(), 422);
        $tender->update(['quote_status' => 'sent', 'quote_sent_at' => now(), 'quote_decision_at' => null, 'quote_feedback' => null]);
        $tender->creator->notify(new TenderQuoteSentNotification($tender->fresh(['creator'])));
        Flux::toast(variant: 'success', text: 'Quotation uploaded and sent to the customer.');
    }

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        return [
            'referenceNumber' => ['required', 'string', 'max:100', Rule::unique('tenders', 'reference_number')->ignore($this->editingTenderId)],
            'title' => ['required', 'string', 'max:255'],
            'clientName' => ['required', 'string', 'max:255'],
            'publishedDate' => ['nullable', 'date'],
            'submissionDeadline' => ['required', 'date'],
            'estimatedValue' => ['nullable', 'numeric', 'min:0'],
            'quotedAmount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'submissionChannel' => ['nullable', Rule::in(['NeST', 'Email', 'Physical'])],
            'confirmationReference' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'submitted', 'under_evaluation', 'awarded', 'lost', 'cancelled'])],
            'contractValue' => ['nullable', 'numeric', 'min:0'],
            'outcomeNotes' => ['nullable', 'string', 'max:5000'],
            'assignedOfficerId' => ['nullable', 'exists:users,id'],
            'documentType' => ['required', Rule::in(['quotation', 'financial_proposal', 'technical_proposal', 'nest_receipt', 'submission_evidence', 'other'])],
            'document' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedDateFilter(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $tenderId): void
    {
        $tender = Tender::query()->findOrFail($tenderId);
        $this->editingTenderId = $tender->id;
        $this->referenceNumber = $tender->reference_number ?: $tender->reference_no;
        $this->title = $tender->title;
        $this->clientName = (string) $tender->client_name;
        $this->publishedDate = $tender->published_date?->format('Y-m-d') ?: '';
        $this->submissionDeadline = $tender->submission_deadline?->format('Y-m-d\TH:i') ?: '';
        $this->estimatedValue = (string) ($tender->estimated_value ?? '');
        $this->quotedAmount = (string) ($tender->quoted_amount ?? '');
        $this->currency = $tender->currency ?: 'TZS';
        $this->submissionChannel = (string) $tender->submission_channel;
        $this->confirmationReference = (string) $tender->confirmation_reference;
        $this->status = in_array($tender->status, ['draft', 'submitted', 'under_evaluation', 'awarded', 'lost', 'cancelled'], true)
            ? $tender->status
            : 'draft';
        $this->contractValue = (string) ($tender->contract_value ?? '');
        $this->outcomeNotes = (string) $tender->outcome_notes;
        $this->assignedOfficerId = $tender->assigned_officer_id;
        $this->document = null;
        $this->showForm = true;
    }

    public function save(): void
    {
        $validated = $this->validate();
        $tender = $this->editingTenderId ? Tender::query()->findOrFail($this->editingTenderId) : new Tender;
        $oldStatus = $tender->exists ? $tender->status : null;
        $validated['reference_no'] = $validated['referenceNumber'];
        $validated['reference_number'] = $validated['referenceNumber'];
        $validated['deadline'] = $validated['submissionDeadline'];
        $validated['published_at'] = $validated['publishedDate'] ?: null;
        $validated['description'] = $validated['outcomeNotes'] ?: null;
        if ($validated['status'] === 'submitted' && ! $tender->submitted_at) {
            $validated['submitted_at'] = now();
        }
        $tender->fill([
            'reference_no' => $validated['reference_no'], 'reference_number' => $validated['reference_number'],
            'title' => $validated['title'], 'client_name' => $validated['clientName'],
            'published_date' => $validated['publishedDate'] ?: null, 'published_at' => $validated['published_at'],
            'submission_deadline' => $validated['submissionDeadline'], 'deadline' => $validated['deadline'],
            'submitted_at' => $validated['submitted_at'] ?? $tender->submitted_at,
            'estimated_value' => $validated['estimatedValue'] ?: null, 'quoted_amount' => $validated['quotedAmount'] ?: null,
            'currency' => strtoupper($validated['currency']), 'submission_channel' => $validated['submissionChannel'] ?: null,
            'confirmation_reference' => $validated['confirmationReference'] ?: null, 'assigned_officer_id' => $validated['assignedOfficerId'],
            'status' => $validated['status'], 'contract_value' => $validated['contractValue'] ?: null,
            'outcome_notes' => $validated['outcomeNotes'] ?: null,
        ]);
        $tender->save();

        if ($oldStatus !== $tender->status) {
            TenderActivity::query()->create([
                'tender_id' => $tender->id, 'user_id' => Auth::id(), 'type' => 'status_changed',
                'from_status' => $oldStatus, 'to_status' => $tender->status,
                'description' => $oldStatus ? "Status changed from {$oldStatus} to {$tender->status}." : 'Tender record created.',
            ]);
        } elseif (! $oldStatus) {
            TenderActivity::query()->create(['tender_id' => $tender->id, 'user_id' => Auth::id(), 'type' => 'created', 'description' => 'Tender record created.']);
        }

        if ($this->document) {
            $path = $this->document->store('tenders/'.$tender->id, 'public');
            $tender->documents()->create(['document_type' => $validated['documentType'], 'file_path' => $path, 'uploaded_by' => Auth::id()]);
            TenderActivity::query()->create(['tender_id' => $tender->id, 'user_id' => Auth::id(), 'type' => 'document_uploaded', 'description' => 'Uploaded '.str_replace('_', ' ', $validated['documentType']).'.']);
        }

        Flux::toast(variant: 'success', text: $this->editingTenderId ? 'Procurement record updated.' : 'Procurement record created.');
        $this->closeForm();
    }

    public function deleteDocument(int $documentId): void
    {
        $document = TenderDocument::query()->findOrFail($documentId);
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        Flux::toast(variant: 'success', text: 'Document removed.');
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['editingTenderId', 'referenceNumber', 'title', 'clientName', 'publishedDate', 'submissionDeadline', 'estimatedValue', 'quotedAmount', 'submissionChannel', 'confirmationReference', 'contractValue', 'outcomeNotes', 'assignedOfficerId', 'document']);
        $this->status = 'draft';
        $this->currency = 'TZS';
        $this->documentType = 'quotation';
        $this->resetValidation();
    }

    public function render(): View
    {
        $tenders = Tender::query()
            ->with(['assignedOfficer', 'creator', 'documents'])
            ->when($this->search !== '', fn ($query) => $query->where(function ($query): void {
                $search = '%'.$this->search.'%';
                $query->where('reference_number', 'like', $search)
                    ->orWhere('reference_no', 'like', $search)
                    ->orWhere('title', 'like', $search)
                    ->orWhere('client_name', 'like', $search)
                    ->orWhereHas('creator', fn ($creator) => $creator->where('name', 'like', $search)->orWhere('email', 'like', $search));
            }))
            ->when($this->statusFilter !== 'all', fn ($query) => $query->where('status', $this->statusFilter))
            ->when($this->dateFilter === 'this_month', fn ($query) => $query->whereBetween('submission_deadline', [now()->startOfMonth(), now()->endOfMonth()]))
            ->when($this->dateFilter === 'overdue', fn ($query) => $query->where('submission_deadline', '<', now())->whereNotIn('status', ['awarded', 'lost', 'cancelled']))
            ->latest()
            ->paginate(10);

        $decided = Tender::query()->whereIn('status', ['awarded', 'lost'])->count();

        return view('livewire.admin.procurement', [
            'tenders' => $tenders,
            'editingTender' => $this->editingTenderId ? Tender::query()->with(['documents', 'activities.user'])->find($this->editingTenderId) : null,
            'officers' => User::query()->where('is_active', true)->whereIn('role', ['user', 'officer'])->orderBy('name')->get(['id', 'name']),
            'total' => Tender::query()->count(),
            'submittedThisMonth' => Tender::query()->where('status', 'submitted')->where('submitted_at', '>=', now()->startOfMonth())->count(),
            'quotedThisMonth' => Tender::query()->where('status', 'submitted')->where('submitted_at', '>=', now()->startOfMonth())->sum('quoted_amount'),
            'winRate' => $decided ? round((Tender::query()->where('status', 'awarded')->count() / $decided) * 100) : 0,
        ]);
    }
}
