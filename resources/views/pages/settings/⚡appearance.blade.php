<?php

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Preference settings')] class extends Component {
    public string $theme = 'system';
    public string $landing_page = 'dashboard';
    public int $items_per_page = 25;

    public function mount(): void
    {
        $preferences = Auth::user()->preferences ?? [];
        $this->theme = $preferences['theme'] ?? 'system';
        $this->landing_page = $preferences['landing_page'] ?? 'dashboard';
        $this->items_per_page = $preferences['items_per_page'] ?? 25;
    }

    public function savePreferences(): void
    {
        $validated = $this->validate([
            'theme' => ['required', 'in:light,dark,system'],
            'landing_page' => ['required', 'in:dashboard,user.tenders'],
            'items_per_page' => ['required', 'integer', 'in:10,25,50,100'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->update(['preferences' => $validated]);
        Flux::toast(variant: 'success', text: __('Preferences saved.'));
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Preference settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Preferences')" :subheading="__('Personalize the appearance and default views of your workspace')">
        <form wire:submit="savePreferences" x-data x-init="$flux.appearance = @js($theme)" class="my-6 w-full space-y-6">
            <flux:radio.group x-data wire:model="theme" x-on:change="$flux.appearance = $event.target.value" :label="__('Theme preference')" variant="segmented">
                <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
                <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
                <flux:radio value="system" icon="computer-desktop">{{ __('System default') }}</flux:radio>
            </flux:radio.group>

            <flux:select wire:model="landing_page" :label="__('Default dashboard view')">
                <option value="dashboard">{{ __('Tender overview') }}</option>
                <option value="user.tenders">{{ __('My assigned tenders') }}</option>
            </flux:select>

            <flux:select wire:model="items_per_page" :label="__('Items per page')">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </flux:select>

            <flux:button variant="primary" type="submit">{{ __('Save changes') }}</flux:button>
        </form>
    </x-pages::settings.layout>
</section>
