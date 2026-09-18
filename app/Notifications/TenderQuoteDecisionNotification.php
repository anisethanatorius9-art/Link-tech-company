<?php

namespace App\Notifications;

use App\Models\Tender;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenderQuoteDecisionNotification extends Notification
{
    public function __construct(public Tender $tender) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Customer responded to quotation',
            'message' => $this->tender->client_name.' '.$this->tender->quote_status.' the quotation for '.$this->tender->title.'.',
            'url' => route('admin.quote-history'),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Quotation '.$this->tender->quote_status.': '.$this->tender->title)
            ->line($this->tender->client_name.' has '.$this->tender->quote_status.' the quotation.')
            ->when($this->tender->quote_feedback, fn (MailMessage $mail) => $mail->line('Feedback: '.$this->tender->quote_feedback))
            ->action('View quote history', route('admin.quote-history'));
    }
}
