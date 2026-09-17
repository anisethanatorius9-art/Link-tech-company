<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenders', function (Blueprint $table): void {
            $table->text('scope_of_work')->nullable()->after('description');
            $table->text('sla_expectations')->nullable()->after('scope_of_work');
            $table->text('eligibility_criteria')->nullable()->after('sla_expectations');
        });
    }

    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table): void {
            $table->dropColumn(['scope_of_work', 'sla_expectations', 'eligibility_criteria']);
        });
    }
};
