<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('company_settings', function (Blueprint $table): void {
            $table->json('contact_details')->nullable()->after('address');
            $table->json('legal_identifiers')->nullable()->after('vrn');
            $table->json('branding_assets')->nullable()->after('logo_path');
            $table->json('workflow_rules')->nullable()->after('deadline_reminder_days');
            $table->json('notification_settings')->nullable()->after('workflow_rules');
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'contact_details',
                'legal_identifiers',
                'branding_assets',
                'workflow_rules',
                'notification_settings',
            ]);
        });
    }
};
