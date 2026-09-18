<?php

namespace App\Livewire;

use App\Mail\CompanyFeedbackMail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;
use RuntimeException;

class PosTerminal extends Component
{
    use WithFileUploads;

    public string $company = '';

    public string $companyEmail = '';

    public string $customerEmail = '';

    public string $subject = '';

    public string $message = '';

    public ?UploadedFile $reportFile = null;

    public function mount(): void
    {
        $this->message = "Dear  Team,\n\nI hope you are well.\n\nPlease find attached the report for your review. The report contains the relevant feedback, observations, and details that require your attention.\n\nWe kindly request you to review the attached document and share your response or any recommended action with us.\n\nThank you for your cooperation.\n\nKind regards,\nGilta Makundi\nSales";
    }

    public function sendFeedback(): void
    {
        $validated = $this->validate([
            'company' => ['required', 'string', 'max:255'],
            'companyEmail' => ['required', 'email', 'max:255'],
            'customerEmail' => ['required', 'email', 'max:255', 'different:companyEmail'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
            'reportFile' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ]);

        $attachmentData = $this->reportFile->get();
        if ($attachmentData === false) {
            throw new RuntimeException('Unable to read the feedback report.');
        }

        Mail::to($validated['companyEmail'])
            ->cc($validated['customerEmail'])
            ->send(new CompanyFeedbackMail(
                $validated['company'],
                $validated['subject'],
                $validated['message'],
                'Gilta Makundi',
                $attachmentData,
                $this->reportFile->getClientOriginalName(),
            ));

        $this->reset(['company', 'companyEmail', 'customerEmail', 'subject', 'message', 'reportFile']);
        session()->flash('feedback-sent', 'Feedback report sent successfully.');
    }

    public function render(): View
    {
        return view('livewire.pos-terminal');
    }
}
