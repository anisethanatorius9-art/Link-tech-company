<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenders', function (Blueprint $table): void {
            $table->id();
            $table->string('reference_no')->unique();
            $table->string('title');
            $table->string('category')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });

        Schema::create('tender_submissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tender_id')->constrained()->cascadeOnDelete();
            $table->string('company_name');
            $table->string('email');
            $table->string('phone_number', 30);
            $table->decimal('proposed_amount', 15, 2);
            $table->string('technical_proposal_path');
            $table->string('financial_proposal_path');
            $table->string('status')->default('submitted');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tender_submissions');
        Schema::dropIfExists('tenders');
    }
};
