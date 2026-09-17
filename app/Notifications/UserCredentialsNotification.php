<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCredentialsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $temporaryPassword) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Link-Tech workspace access')
            ->greeting('Welcome to Link-Tech')
            ->line('An administrator created your workspace account.')
            ->line('Email: '.$notifiable->email)
            ->line('Temporary password: '.$this->temporaryPassword)
            ->line('You will be required to change this password after your first login.')
            ->action('Open workspace', url('/dashboard'));
    }
}
