<?php

namespace App\Livewire;

use Illuminate\View\View;
use Livewire\Component;

class Translation extends Component
{
    public string $locale = 'sw';

    public function mount(): void
    {
        $this->locale = session('locale', 'en');
    }

    public function setLanguage(string $locale): void
    {
        abort_unless(in_array($locale, ['en', 'sw', 'zh', 'fr'], true), 422);

        $this->locale = $locale;
        session(['locale' => $this->locale]);
        app()->setLocale($this->locale);
    }

    public function updatedLocale(string $locale): void
    {
        $this->setLanguage($locale);
    }

    public function render(): View
    {
        return view('livewire.translation');
    }
}
