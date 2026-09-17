<?php

namespace App\Notifications;

use App\Models\TenderSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenderStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public TenderSubmission $submission) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusMessage = match ($this->submission->status) {
            'approved' => 'Congratulations! Your tender proposal has been accepted. Our procurement team will reach out shortly regarding the next steps.',
            'rejected' => 'We regret to inform you that your tender proposal was not successful for this cycle.',
            'under_review' => 'Your tender proposal is currently undergoing official technical and financial review.',
            default => 'The status of your tender proposal has been updated to: ' . ucfirst($this->submission->status),
        };

        return (new MailMessage)
            ->subject('Tender Status Update: ' . $this->submission->tender->title)
            ->greeting('Dear ' . $this->submission->company_name . ',')
            ->line('We are writing to update you on your submission for tender Ref: ' . $this->submission->tender->reference_no . '.')
            ->line($statusMessage)
            ->action('View Tenders Page', url('/tenders'))
            ->line('Thank you for participating!');
    }
}
