<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name',
        'address',
        'tin',
        'vrn',
        'logo_path',
        'deadline_reminder_days',
        'contact_details',
        'legal_identifiers',
        'branding_assets',
        'workflow_rules',
        'notification_settings',
    ];

    protected function casts(): array
    {
        return [
            'contact_details' => 'array',
            'legal_identifiers' => 'array',
            'branding_assets' => 'array',
            'workflow_rules' => 'array',
            'notification_settings' => 'array',
        ];
    }
}
