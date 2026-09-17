<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('company_name')->default('Link-Tech Company');
            $table->string('address')->nullable();
            $table->string('tin')->nullable();
            $table->string('vrn')->nullable();
            $table->string('logo_path')->nullable();
            $table->unsignedTinyInteger('deadline_reminder_days')->default(3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
