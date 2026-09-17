<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procurement_requests', function (Blueprint $table): void {
            $table->string('supplier_name')->nullable()->after('unit_price');
            $table->unsignedInteger('received_quantity')->default(0)->after('supplier_name');
            $table->timestamp('received_at')->nullable()->after('received_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('procurement_requests', function (Blueprint $table): void {
            $table->dropColumn(['supplier_name', 'received_quantity', 'received_at']);
        });
    }
};
