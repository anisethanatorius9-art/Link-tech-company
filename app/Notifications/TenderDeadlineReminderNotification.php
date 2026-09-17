<?php

namespace App\Notifications;

use App\Models\Tender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenderDeadlineReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Tender $tender) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tender deadline reminder: '.$this->tender->title)
            ->greeting('Procurement deadline reminder')
            ->line('The tender below is due in approximately three days and is still open.')
            ->line('Reference: '.($this->tender->reference_number ?: $this->tender->reference_no))
            ->line('Client: '.$this->tender->client_name)
            ->line('Deadline: '.$this->tender->submission_deadline?->format('d M Y, H:i'))
            ->action('Open Procurement', url('/admin/procurement'));
    }
}
