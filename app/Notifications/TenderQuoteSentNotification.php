<?php

namespace App\Notifications;

use App\Models\Tender;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenderQuoteSentNotification extends Notification
{
    public function __construct(public Tender $tender) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New quotation available',
            'message' => 'A quotation for '.$this->tender->title.' is ready for your review.',
            'url' => route('quotes.index', ['tender' => $this->tender->id]),
            'pdf_url' => route('quotes.pdf', ['tender' => $this->tender->id]),
            'customer_name' => $this->tender->creator?->name,
            'customer_email' => $this->tender->creator?->email,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Quotation ready: '.$this->tender->title)
            ->line('Your quotation is ready for review.')
            ->line('Customer: '.$this->tender->creator?->name.' ('.$this->tender->creator?->email.')')
            ->line('Total: '.$this->tender->currency.' '.number_format((float) $this->tender->quoted_amount, 2))
            ->action('View quotation PDF', route('quotes.pdf', ['tender' => $this->tender->id]))
            ->line('You can accept or reject it from your tender page.');
    }
}
