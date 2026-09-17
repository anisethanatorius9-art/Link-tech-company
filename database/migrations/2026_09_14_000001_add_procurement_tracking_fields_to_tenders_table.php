<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenders', function (Blueprint $table): void {
            $table->string('reference_number')->nullable()->unique()->after('reference_no');
            $table->string('client_name')->nullable()->after('title');
            $table->date('published_date')->nullable()->after('client_name');
            $table->timestamp('submission_deadline')->nullable()->after('published_date');
            $table->timestamp('submitted_at')->nullable()->after('submission_deadline');
            $table->decimal('estimated_value', 15, 2)->nullable()->after('submitted_at');
            $table->decimal('quoted_amount', 15, 2)->nullable()->after('estimated_value');
            $table->string('currency', 3)->default('TZS')->after('quoted_amount');
            $table->string('submission_channel')->nullable()->after('currency');
            $table->string('confirmation_reference')->nullable()->after('submission_channel');
            $table->foreignId('assigned_officer_id')->nullable()->constrained('users')->nullOnDelete()->after('confirmation_reference');
            $table->decimal('contract_value', 15, 2)->nullable()->after('assigned_officer_id');
            $table->text('outcome_notes')->nullable()->after('contract_value');
        });

        Schema::table('tenders', function (Blueprint $table): void {
            $table->index(['status', 'submission_deadline']);
        });
    }

    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table): void {
            $table->dropForeign(['assigned_officer_id']);
            $table->dropIndex(['status', 'submission_deadline']);
            $table->dropUnique(['reference_number']);
            $table->dropColumn([
                'reference_number', 'client_name', 'published_date', 'submission_deadline', 'submitted_at',
                'estimated_value', 'quoted_amount', 'currency', 'submission_channel', 'confirmation_reference',
                'assigned_officer_id', 'contract_value', 'outcome_notes',
            ]);
        });
    }
};
