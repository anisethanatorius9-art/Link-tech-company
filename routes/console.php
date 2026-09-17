<?php

use App\Models\CompanySetting;
use App\Models\Tender;
use App\Models\User;
use App\Notifications\TenderDeadlineReminderNotification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function (): void {
    $reminderDays = CompanySetting::query()->value('deadline_reminder_days') ?: 3;
    $deadlineStart = now()->addDays($reminderDays)->startOfDay();
    $deadlineEnd = $deadlineStart->copy()->endOfDay();
    $admins = User::query()->where('role', 'admin')->get();

    Tender::query()
        ->whereBetween('submission_deadline', [$deadlineStart, $deadlineEnd])
        ->whereIn('status', ['draft', 'submitted', 'under_evaluation'])
        ->each(fn (Tender $tender) => $admins->each->notify(new TenderDeadlineReminderNotification($tender)));
})->dailyAt('09:00')->name('tender-deadline-reminders')->withoutOverlapping();
