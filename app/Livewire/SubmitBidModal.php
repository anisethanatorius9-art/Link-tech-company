<?php

namespace App\Livewire;

use App\Models\Tender;
use App\Models\TenderSubmission;
use Flux\Flux;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class SubmitBidModal extends Component
{
    use WithFileUploads;

    public ?Tender $tender = null;

    public string $company_name = '';

    public string $email = '';

    public string $phone_number = '';

    public string $proposed_amount = '';

    public ?UploadedFile $technical_proposal = null;

    public ?UploadedFile $financial_proposal = null;

    public bool $showModal = false;

    #[On('openSubmitModal')]
    public function openSubmitModal(int $tenderId): void
    {
        $this->tender = Tender::query()->findOrFail($tenderId);
        $this->resetValidation();
        $this->reset(['company_name', 'email', 'phone_number', 'proposed_amount', 'technical_proposal', 'financial_proposal']);
        $this->showModal = true;
    }

    /** @return array<string, array<int, mixed>> */
    protected function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone_number' => ['required', 'string', 'max:30'],
            'proposed_amount' => ['required', 'numeric', 'min:0'],
            'technical_proposal' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'financial_proposal' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    public function submitBid(): void
    {
        $this->validate();

        $tender = Tender::query()->findOrFail($this->tender?->id);
        abort_if(! in_array($tender->status, ['active', 'closing_soon'], true), 422, 'This tender is no longer accepting bids.');

        /** @var UploadedFile $technicalProposal */
        $technicalProposal = $this->technical_proposal;
        /** @var UploadedFile $financialProposal */
        $financialProposal = $this->financial_proposal;
        $technicalPath = $technicalProposal->store("submissions/{$tender->id}/technical", 'public');
        $financialPath = $financialProposal->store("submissions/{$tender->id}/financial", 'public');

        TenderSubmission::query()->create([
            'tender_id' => $tender->id,
            'company_name' => $this->company_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'proposed_amount' => $this->proposed_amount,
            'technical_proposal_path' => $technicalPath,
            'financial_proposal_path' => $financialPath,
            'status' => 'submitted',
        ]);

        $this->reset(['company_name', 'email', 'phone_number', 'proposed_amount', 'technical_proposal', 'financial_proposal']);
        $this->showModal = false;
        Flux::toast(variant: 'success', text: 'Your tender bid has been submitted successfully.');
    }

    public function render(): View
    {
        return view('livewire.submit-bid-modal');
    }
}
