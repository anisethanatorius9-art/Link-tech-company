<?php

namespace App\Notifications;

use App\Models\Tender;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenderSubmittedNotification extends Notification
{
    public function __construct(public Tender $tender) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New tender submitted',
            'message' => $this->tender->title.' was submitted by '.$this->tender->client_name.'.',
            'url' => route('quotes.index', ['tender' => $this->tender->id]),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New tender submitted: '.$this->tender->title)
            ->line('A new tender has been submitted and is ready for quotation.')
            ->line('Reference: '.$this->tender->reference_no)
            ->action('Prepare quotation', route('quotes.index', ['tender' => $this->tender->id]));
    }
}
