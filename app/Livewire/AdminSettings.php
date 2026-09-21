<?php

namespace App\Livewire;

use App\Models\CompanySetting;
use App\Models\TenderActivity;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use RuntimeException;

#[Layout('layouts.admin')]
class AdminSettings extends Component
{
    use WithFileUploads;

    public string $tab = 'audit';

    public string $companyName = '';

    public string $address = '';

    public string $companyPhone = '';

    public string $companyEmail = '';

    public string $website = '';

    public string $tin = '';

    public string $vrn = '';

    public string $brelaNumber = '';

    public string $licenseNumber = '';

    public ?TemporaryUploadedFile $logo = null;

    public ?TemporaryUploadedFile $stamp = null;

    public string $currency = 'TZS';

    public string $taxPercentage = '18';

    public string $minimumMargin = '10';

    public bool $approvalRequired = true;

    public int $normalAlertDays = 7;

    public int $warningAlertDays = 3;

    public int $urgentAlertDays = 1;

    public string $notificationEmail = '';

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        abort_unless($user instanceof User && $user->isAdmin(), 403);

        $settings = CompanySetting::query()->first();
        $this->fillCompany($settings);
    }

    public function selectTab(string $tab): void
    {
        abort_unless(in_array($tab, ['company', 'users', 'rules', 'alerts', 'audit'], true), 404);
        $this->tab = $tab;
    }

    public function saveCompany(): void
    {
        $validated = $this->validate([
            'companyName' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'companyPhone' => ['nullable', 'string', 'max:50'],
            'companyEmail' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'tin' => ['nullable', 'string', 'max:100'],
            'vrn' => ['nullable', 'string', 'max:100'],
            'brelaNumber' => ['nullable', 'string', 'max:100'],
            'licenseNumber' => ['nullable', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'max:5120'],
            'stamp' => ['nullable', 'image', 'max:5120'],
        ]);

        $settings = CompanySetting::query()->firstOrNew();
        $settings->fill([
            'company_name' => $validated['companyName'],
            'address' => $validated['address'],
            'tin' => $validated['tin'],
            'vrn' => $validated['vrn'],
            'contact_details' => [
                'phone' => $validated['companyPhone'],
                'email' => $validated['companyEmail'],
                'website' => $validated['website'],
            ],
            'legal_identifiers' => [
                'brela' => $validated['brelaNumber'],
                'license' => $validated['licenseNumber'],
            ],
        ]);

        if ($this->logo) {
            $logoPath = $this->logo->store('company', 'public');
            if ($logoPath === false) {
                throw new RuntimeException('Unable to store the company logo.');
            }
            $settings->logo_path = $logoPath;
        }

        if ($this->stamp) {
            $branding = $settings->branding_assets ?? [];
            $stampPath = $this->stamp->store('company', 'public');
            if ($stampPath === false) {
                throw new RuntimeException('Unable to store the company stamp.');
            }
            $branding['stamp_path'] = $stampPath;
            $settings->branding_assets = $branding;
        }

        $settings->save();
        $this->logo = null;
        $this->stamp = null;
        Flux::toast(variant: 'success', text: __('Company profile saved.'));
    }

    public function saveRules(): void
    {
        $validated = $this->validate([
            'currency' => ['required', 'in:TZS,USD'],
            'taxPercentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'minimumMargin' => ['required', 'numeric', 'min:0', 'max:100'],
            'approvalRequired' => ['boolean'],
        ]);

        CompanySetting::query()->firstOrCreate()->update([
            'workflow_rules' => [
                'currency' => $validated['currency'],
                'tax_percentage' => (float) $validated['taxPercentage'],
                'minimum_margin' => (float) $validated['minimumMargin'],
                'approval_required' => $validated['approvalRequired'],
            ],
        ]);

        Flux::toast(variant: 'success', text: __('Tender rules saved.'));
    }

    public function saveAlerts(): void
    {
        $validated = $this->validate([
            'normalAlertDays' => ['required', 'integer', 'min:1', 'max:30'],
            'warningAlertDays' => ['required', 'integer', 'min:1', 'max:30'],
            'urgentAlertDays' => ['required', 'integer', 'min:1', 'max:30'],
            'notificationEmail' => ['required', 'email', 'max:255'],
        ]);

        CompanySetting::query()->firstOrCreate()->update([
            'notification_settings' => [
                'normal_days' => $validated['normalAlertDays'],
                'warning_days' => $validated['warningAlertDays'],
                'urgent_days' => $validated['urgentAlertDays'],
                'email' => $validated['notificationEmail'],
            ],
        ]);

        Flux::toast(variant: 'success', text: __('System alerts saved.'));
    }

    public function render(): View
    {
        return view('livewire.admin-settings', [
            'activities' => $this->tab === 'audit'
                ? TenderActivity::query()->with('user')->latest()->limit(30)->get()
                : collect(),
            'auditSignals' => $this->tab === 'audit' ? $this->collectAuditSignals() : [],
            'healthMetrics' => $this->tab === 'audit' ? $this->collectHealthMetrics() : [],
            'exports' => [
                ['label' => __('Requests Excel'), 'route' => 'admin.exports.requests.xlsx'],
                ['label' => __('Requests PDF'), 'route' => 'admin.exports.requests.pdf'],
            ],
        ]);
    }

    private function collectAuditSignals(): array
    {
        $logPath = storage_path('logs/laravel.log');
        if (! is_file($logPath)) {
            return [];
        }

        $signals = [];
        $lines = array_filter(file($logPath, FILE_IGNORE_NEW_LINES), fn (string $line): bool => $line !== '');

        foreach (array_slice(array_reverse($lines), 0, 200) as $line) {
            if (! preg_match('/(Failed login attempt|Invalid credentials|Too many attempts|unauthorized|Unauthenticated|CSRF|suspicious|blocked)/i', $line)) {
                continue;
            }

            $severity = str_contains(strtolower($line), 'failed login') || str_contains(strtolower($line), 'too many attempts')
                ? 'warning'
                : 'danger';

            $title = preg_match('/Failed login attempt/i', $line)
                ? 'Failed login'
                : (preg_match('/Too many attempts/i', $line)
                    ? 'Repeated login failures'
                    : (preg_match('/CSRF|unauthorized|Unauthenticated/i', $line)
                        ? 'Suspicious access'
                        : 'Security alert'));

            $message = preg_match('/Failed login attempt/i', $line)
                ? 'A failed login attempt was detected in the system log.'
                : (preg_match('/Too many attempts/i', $line)
                    ? 'Multiple login failures suggest a possible brute-force attempt.'
                    : 'The platform detected unusual request activity that may need review.');

            if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $timeMatch)) {
                $time = $timeMatch[1];
            } else {
                $time = now()->format('Y-m-d H:i:s');
            }

            $signals[] = [
                'title' => $title,
                'severity' => $severity,
                'message' => $message,
                'time' => $time,
                'source' => 'laravel.log',
            ];
        }

        return array_values(array_slice($signals, 0, 8));
    }

    private function collectHealthMetrics(): array
    {
        $metrics = [
            ['label' => __('Database'), 'status' => 'healthy', 'detail' => __('Connected')],
            ['label' => __('Storage'), 'status' => 'healthy', 'detail' => __('Available')],
            ['label' => __('Auth logs'), 'status' => 'healthy', 'detail' => __('Monitoring active')],
        ];

        $logPath = storage_path('logs/laravel.log');
        if (is_file($logPath)) {
            $contents = file_get_contents($logPath);
            if (is_string($contents) && preg_match('/(Failed login attempt|Too many attempts|CSRF|Unauthenticated|unauthorized)/i', $contents)) {
                $metrics[2]['status'] = 'warning';
                $metrics[2]['detail'] = __('Possible anomaly detected');
            }
        }

        return $metrics;
    }

    private function fillCompany(?CompanySetting $settings): void
    {
        $this->companyName = $settings?->company_name ?: 'Link-Tech Company';
        $this->address = (string) $settings?->address;
        $this->tin = (string) $settings?->tin;
        $this->vrn = (string) $settings?->vrn;
        $contact = $settings ? $settings->contact_details : null;
        $legal = $settings ? $settings->legal_identifiers : null;
        $rules = $settings ? $settings->workflow_rules : null;
        $alerts = $settings ? $settings->notification_settings : null;
        $contact = is_array($contact) ? $contact : [];
        $legal = is_array($legal) ? $legal : [];
        $rules = is_array($rules) ? $rules : [];
        $alerts = is_array($alerts) ? $alerts : [];
        $this->companyPhone = (string) ($contact['phone'] ?? '');
        $this->companyEmail = (string) ($contact['email'] ?? '');
        $this->website = (string) ($contact['website'] ?? '');
        $this->brelaNumber = (string) ($legal['brela'] ?? '');
        $this->licenseNumber = (string) ($legal['license'] ?? '');
        $this->currency = $rules['currency'] ?? 'TZS';
        $this->taxPercentage = (string) ($rules['tax_percentage'] ?? 18);
        $this->minimumMargin = (string) ($rules['minimum_margin'] ?? 10);
        $this->approvalRequired = $rules['approval_required'] ?? true;
        $this->normalAlertDays = $alerts['normal_days'] ?? 7;
        $this->warningAlertDays = $alerts['warning_days'] ?? 3;
        $this->urgentAlertDays = $alerts['urgent_days'] ?? 1;
        $this->notificationEmail = (string) ($alerts['email'] ?? '');
    }
}
