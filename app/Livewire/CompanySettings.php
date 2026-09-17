<?php

namespace App\Livewire;

use App\Models\CompanySetting;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class CompanySettings extends Component
{
    use WithFileUploads;

    public string $companyName = '';

    public string $address = '';

    public string $tin = '';

    public string $vrn = '';

    public int $deadlineReminderDays = 3;

    public $logo = null;

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User && $user->isAdmin(), 403);
        $settings = CompanySetting::query()->first();
        $this->companyName = $settings?->company_name ?: 'Link-Tech Company';
        $this->address = (string) $settings?->address;
        $this->tin = (string) $settings?->tin;
        $this->vrn = (string) $settings?->vrn;
        $this->deadlineReminderDays = $settings?->deadline_reminder_days ?: 3;
    }

    public function save(): void
    {
        $data = $this->validate(['companyName' => ['required', 'string', 'max:255'], 'address' => ['nullable', 'string', 'max:500'], 'tin' => ['nullable', 'string', 'max:100'], 'vrn' => ['nullable', 'string', 'max:100'], 'deadlineReminderDays' => ['required', 'integer', 'in:1,3,5,7'], 'logo' => ['nullable', 'image', 'max:5120']]);
        $settings = CompanySetting::query()->firstOrNew();
        $settings->fill(['company_name' => $data['companyName'], 'address' => $data['address'], 'tin' => $data['tin'], 'vrn' => $data['vrn'], 'deadline_reminder_days' => $data['deadlineReminderDays']]);
        if ($this->logo) {
            $settings->logo_path = $this->logo->store('company', 'public');
        }
        $settings->save();
        Flux::toast(variant: 'success', text: 'Company settings saved.');
    }

    public function render()
    {
        return view('livewire.company-settings');
    }
}
