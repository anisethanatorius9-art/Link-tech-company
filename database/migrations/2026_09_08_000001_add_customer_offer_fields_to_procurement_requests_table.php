<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procurement_requests', function (Blueprint $table): void {
            $table->decimal('quoted_amount', 15, 2)->nullable()->after('unit_price');
            $table->string('control_number')->nullable()->after('quoted_amount');
            $table->string('quote_document_path')->nullable()->after('control_number');
            $table->string('customer_decision')->nullable()->after('quote_document_path');
            $table->string('payment_status')->default('unpaid')->after('customer_decision');
            $table->timestamp('responded_at')->nullable()->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('procurement_requests', function (Blueprint $table): void {
            $table->dropColumn(['quoted_amount', 'control_number', 'quote_document_path', 'customer_decision', 'payment_status', 'responded_at']);
        });
    }
};
