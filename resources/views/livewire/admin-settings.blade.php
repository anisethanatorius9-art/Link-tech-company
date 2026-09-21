<div class="min-h-full w-full flex-1 bg-[#f6f7f2] p-4 text-[#17221b] lg:p-8 dark:bg-zinc-950 dark:text-zinc-100">
    <div class="mx-auto max-w-7xl space-y-8">
        <div class="border-b border-[#dce3d8] pb-6 dark:border-zinc-800">
            <flux:text class="mb-2 text-xs font-bold uppercase tracking-[.2em] text-[#2d7a57]">{{ __('Administration') }}</flux:text>
            <flux:heading size="xl">{{ __('Admin system settings') }}</flux:heading>
            <flux:subheading class="mt-2">{{ __('Configure company details, access, workflow rules, alerts, and system activity.') }}</flux:subheading>
        </div>

        <div class="grid gap-8 lg:grid-cols-[220px_1fr]">
            <nav class="space-y-1" aria-label="{{ __('Admin settings') }}">
                @foreach ([
                    'company' => ['building-office', __('Company profile')],
                    'users' => ['users', __('Users & roles')],
                    'rules' => ['adjustments-horizontal', __('Tender rules')],
                    'alerts' => ['bell-alert', __('System alerts')],
                    'audit' => ['shield-check', __('Audit & health')],
                ] as $key => [$icon, $label])
                    <flux:button wire:click="selectTab('{{ $key }}')" icon="{{ $icon }}" variant="{{ $tab === $key ? 'primary' : 'ghost' }}" class="w-full justify-start">
                        {{ $label }}
                    </flux:button>
                @endforeach
            </nav>

            <main class="min-w-0">
                @if ($tab === 'company')
                    <flux:card class="p-6">
                        <div class="mb-6"><flux:heading>{{ __('Company profile & branding') }}</flux:heading><flux:subheading>{{ __('These details appear on quotations, tender letters, and reports.') }}</flux:subheading></div>
                        <form wire:submit="saveCompany" class="space-y-6">
                            <div class="grid gap-4 md:grid-cols-2">
                                <flux:input wire:model="companyName" :label="__('Company name')" required />
                                <flux:input wire:model="companyPhone" :label="__('Phone number')" />
                                <flux:input wire:model="companyEmail" type="email" :label="__('Company email')" />
                                <flux:input wire:model="website" type="url" :label="__('Website')" />
                                <flux:textarea wire:model="address" :label="__('Physical address')" rows="3" class="md:col-span-2" />
                            </div>
                            <div class="border-t border-zinc-200 pt-6 dark:border-zinc-700"><flux:heading size="sm">{{ __('Legal identifiers') }}</flux:heading><div class="mt-4 grid gap-4 md:grid-cols-3"><flux:input wire:model="tin" :label="__('TIN')" /><flux:input wire:model="vrn" :label="__('VRN / VAT number')" /><flux:input wire:model="brelaNumber" :label="__('BRELA registration')" /><flux:input wire:model="licenseNumber" :label="__('CRB / ERB license')" /></div></div>
                            <div class="border-t border-zinc-200 pt-6 dark:border-zinc-700"><flux:heading size="sm">{{ __('Branding assets') }}</flux:heading><div class="mt-4 grid gap-4 md:grid-cols-2"><flux:input wire:model="logo" type="file" accept="image/*" :label="__('Company logo')" /><flux:input wire:model="stamp" type="file" accept="image/*" :label="__('Official stamp or signature')" /></div></div>
                            <div class="flex justify-end"><flux:button type="submit" variant="primary" icon="check">{{ __('Save company profile') }}</flux:button></div>
                        </form>
                    </flux:card>
                @elseif ($tab === 'users')
                    <livewire:admin-users />
                @elseif ($tab === 'rules')
                    <flux:card class="p-6"><div class="mb-6"><flux:heading>{{ __('Tender & workflow rules') }}</flux:heading><flux:subheading>{{ __('Set the defaults used when officers prepare quotations and submit tenders.') }}</flux:subheading></div><form wire:submit="saveRules" class="space-y-6"><div class="grid gap-4 md:grid-cols-3"><flux:select wire:model="currency" :label="__('Default currency')"><option value="TZS">TZS</option><option value="USD">USD</option></flux:select><flux:input wire:model="taxPercentage" type="number" step="0.01" min="0" max="100" :label="__('Tax / VAT percentage')" /><flux:input wire:model="minimumMargin" type="number" step="0.01" min="0" max="100" :label="__('Minimum margin warning (%)')" /></div><flux:switch wire:model="approvalRequired" :label="__('Require admin approval before tender submission')" :description="__('Officers must receive approval before a quotation can be submitted to NeST.')" /><flux:button type="submit" variant="primary">{{ __('Save tender rules') }}</flux:button></form></flux:card>
                @elseif ($tab === 'alerts')
                    <flux:card class="p-6"><div class="mb-6"><flux:heading>{{ __('Global notifications & reminders') }}</flux:heading><flux:subheading>{{ __('Set deadline escalation levels and the sender address used by the system.') }}</flux:subheading></div><form wire:submit="saveAlerts" class="space-y-6"><div class="grid gap-4 md:grid-cols-3"><flux:input wire:model="normalAlertDays" type="number" min="1" max="30" :label="__('Normal alert days')" /><flux:input wire:model="warningAlertDays" type="number" min="1" max="30" :label="__('Warning alert days')" /><flux:input wire:model="urgentAlertDays" type="number" min="1" max="30" :label="__('Urgent alert days')" /></div><flux:input wire:model="notificationEmail" type="email" :label="__('System notification email')" /><flux:button type="submit" variant="primary">{{ __('Save system alerts') }}</flux:button></form></flux:card>
                @else
                    <div class="space-y-6">
                        <flux:card class="p-6">
                            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                                <div>
                                    <flux:heading>{{ __('Audit logs') }}</flux:heading>
                                    <flux:subheading>{{ __('Recent tender activity and system actions.') }}</flux:subheading>
                                </div>
                                <div class="flex gap-2">
                                    @foreach ($exports as $export)
                                        <flux:button :href="route($export['route'])" icon="arrow-down-tray" variant="ghost">{{ $export['label'] }}</flux:button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-6 grid gap-4 md:grid-cols-3">
                                @foreach ($healthMetrics as $metric)
                                    <div class="rounded-2xl border p-4 {{ $metric['status'] === 'warning' ? 'border-amber-200 bg-amber-50 dark:border-amber-900/60 dark:bg-amber-950/20' : 'border-emerald-200 bg-emerald-50 dark:border-emerald-900/50 dark:bg-emerald-950/20' }}">
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-sm text-zinc-600 dark:text-zinc-300">{{ $metric['label'] }}</span>
                                            <flux:badge color="{{ $metric['status'] === 'warning' ? 'amber' : 'green' }}">
                                                {{ $metric['status'] === 'warning' ? __('Watch') : __('Healthy') }}
                                            </flux:badge>
                                        </div>
                                        <p class="mt-3 text-sm font-medium text-zinc-800 dark:text-zinc-100">{{ $metric['detail'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </flux:card>

                        <flux:card class="p-6">
                            <div class="flex items-center justify-between gap-3">
                                <flux:heading size="sm">{{ __('Potential login anomalies') }}</flux:heading>
                                <flux:badge color="{{ empty($auditSignals) ? 'green' : 'amber' }}">{{ empty($auditSignals) ? __('No issues') : __('Review required') }}</flux:badge>
                            </div>

                            <div class="mt-5 space-y-3">
                                @forelse ($auditSignals as $signal)
                                    <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-900/40">
                                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="flex items-center gap-3">
                                                <flux:badge color="{{ $signal['severity'] === 'warning' ? 'amber' : 'red' }}">{{ $signal['title'] }}</flux:badge>
                                                <span class="text-xs uppercase tracking-wide text-zinc-500">{{ $signal['source'] }}</span>
                                            </div>
                                            <span class="text-xs text-zinc-500">{{ $signal['time'] }}</span>
                                        </div>
                                        <p class="mt-3 text-sm text-zinc-700 dark:text-zinc-300">{{ $signal['message'] }}</p>
                                    </div>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-5 text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900/30">
                                        {{ __('No suspicious auth activity detected in the recent log file.') }}
                                    </div>
                                @endforelse
                            </div>
                        </flux:card>

                        <flux:card class="p-6">
                            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                                <div>
                                    <flux:heading>{{ __('Audit logs') }}</flux:heading>
                                    <flux:subheading>{{ __('Recent tender activity and system actions.') }}</flux:subheading>
                                </div>
                            </div>

                            <div class="mt-6 overflow-x-auto">
                                <flux:table>
                                    <flux:table.columns>
                                        <flux:table.column>{{ __('Time') }}</flux:table.column>
                                        <flux:table.column>{{ __('User') }}</flux:table.column>
                                        <flux:table.column>{{ __('Action') }}</flux:table.column>
                                        <flux:table.column>{{ __('Tender') }}</flux:table.column>
                                    </flux:table.columns>
                                    <flux:table.rows>
                                        @forelse ($activities as $activity)
                                            <flux:table.row>
                                                <flux:table.cell class="whitespace-nowrap">{{ $activity->created_at?->format('M j, Y H:i') }}</flux:table.cell>
                                                <flux:table.cell>{{ $activity->user?->name ?? __('System') }}</flux:table.cell>
                                                <flux:table.cell>{{ $activity->description ?: str_replace('_', ' ', ucfirst($activity->type)) }}</flux:table.cell>
                                                <flux:table.cell>#{{ $activity->tender_id }}</flux:table.cell>
                                            </flux:table.row>
                                        @empty
                                            <flux:table.row>
                                                <flux:table.cell colspan="4" class="py-10 text-center text-zinc-500">{{ __('No audit activity recorded yet.') }}</flux:table.cell>
                                            </flux:table.row>
                                        @endforelse
                                    </flux:table.rows>
                                </flux:table>
                            </div>
                        </flux:card>
                    </div>
                @endif
            </main>
        </div>
    </div>
</div>
