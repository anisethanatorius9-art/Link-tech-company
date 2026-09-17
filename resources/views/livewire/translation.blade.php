<div class="flex min-h-full w-full flex-1 flex-col gap-8 p-4 lg:p-8">
    <div class="border-b border-zinc-200 pb-8 dark:border-zinc-700">
        <flux:heading size="xl" level="1">{{ __('Translation') }}</flux:heading>
        <flux:text class="mt-3 max-w-2xl">
            {{ __('Choose the language you want to use across the system interface.') }}
        </flux:text>
    </div>

    <flux:card class="max-w-xl border-zinc-200 p-6 dark:border-zinc-700">
        <flux:heading size="lg">{{ __('Interface language') }}</flux:heading>
        <flux:text class="mt-2">{{ __('Selected language:') }} {{ $locale === 'sw' ? __('Kiswahili') : __('English') }}</flux:text>
        <flux:select wire:model.live="locale" icon="language" class="mt-6 w-48">
            <option value="en">English</option>
            <option value="sw">Kiswahili</option>
            <option value="zh">中文</option>
            <option value="fr">Français</option>
        </flux:select>
    </flux:card>
</div>
