<?php

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Notification settings')] class extends Component {
    public array $reminder_days = [7, 3, 1];
    public bool $email_notifications = true;
    public bool $in_app_notifications = true;
    public bool $status_change_alerts = true;

    public function mount(): void
    {
        $preferences = Auth::user()->notification_preferences ?? [];
        $this->reminder_days = $preferences['reminder_days'] ?? $this->reminder_days;
        $this->email_notifications = $preferences['email_notifications'] ?? true;
        $this->in_app_notifications = $preferences['in_app_notifications'] ?? true;
        $this->status_change_alerts = $preferences['status_change_alerts'] ?? true;
    }

    public function saveNotifications(): void
    {
        $this->validate([
            'reminder_days' => ['array'],
            'reminder_days.*' => ['integer', 'in:7,3,1'],
            'email_notifications' => ['boolean'],
            'in_app_notifications' => ['boolean'],
            'status_change_alerts' => ['boolean'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->update([
            'notification_preferences' => [
                'reminder_days' => array_values(array_unique($this->reminder_days)),
                'email_notifications' => $this->email_notifications,
                'in_app_notifications' => $this->in_app_notifications,
                'status_change_alerts' => $this->status_change_alerts,
            ],
        ]);

        Flux::toast(variant: 'success', text: __('Notification preferences saved.'));
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Notification settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Notifications')" :subheading="__('Choose how you receive tender reminders and status updates')">
        <form wire:submit="saveNotifications" class="my-6 w-full space-y-8">
            <div class="space-y-3">
                <flux:heading size="sm">{{ __('NeST submission reminders') }}</flux:heading>
                <flux:checkbox.group wire:model="reminder_days" :label="__('Remind me before a tender deadline')">
                    <flux:checkbox value="7" :label="__('7 days before')" />
                    <flux:checkbox value="3" :label="__('3 days before')" />
                    <flux:checkbox value="1" :label="__('1 day before')" />
                </flux:checkbox.group>
            </div>

            <div class="space-y-4">
                <flux:heading size="sm">{{ __('Notification channels') }}</flux:heading>
                <flux:switch wire:model="email_notifications" :label="__('Email notifications')" :description="__('Receive new tender and assignment emails.')" />
                <flux:switch wire:model="in_app_notifications" :label="__('In-app notifications')" :description="__('Show alerts in the dashboard notification bell.')" />
                <flux:switch wire:model="status_change_alerts" :label="__('Status change alerts')" :description="__('Be notified when quotations or tenders change status.')" />
            </div>

            <flux:button variant="primary" type="submit">{{ __('Save changes') }}</flux:button>
        </form>
    </x-pages::settings.layout>
</section>
