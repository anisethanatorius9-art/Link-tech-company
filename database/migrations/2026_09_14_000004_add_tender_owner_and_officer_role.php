<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenders', function (Blueprint $table): void {
            $table->foreignId('created_by_id')->nullable()->constrained('users')->nullOnDelete()->after('assigned_officer_id');
        });
    }

    public function down(): void
    {
        Schema::table('tenders', function (Blueprint $table): void {
            $table->dropForeign(['created_by_id']);
            $table->dropColumn('created_by_id');
        });
    }
};
