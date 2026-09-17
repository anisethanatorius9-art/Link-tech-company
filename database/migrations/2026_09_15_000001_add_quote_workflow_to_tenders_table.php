<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenders', function (Blueprint $table): void {
            $table->string('quote_status')->default('draft')->after('quoted_amount');
            $table->timestamp('quote_sent_at')->nullable()->after('quote_status');
            $table->timestamp('quote_decision_at')->nullable()->after('quote_sent_at');
            $table->text('quote_feedback')->nullable()->after('quote_decision_at');
        });

        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');

        Schema::table('tenders', function (Blueprint $table): void {
            $table->dropColumn(['quote_status', 'quote_sent_at', 'quote_decision_at', 'quote_feedback']);
        });
    }
};
