<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tender_quote_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->string('description');
            $table->string('unit', 50)->default('item');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('vat_rate', 5, 2)->default(18);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_quote_items');
    }
};
