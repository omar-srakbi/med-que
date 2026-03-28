<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_reports', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description')->nullable();
            $table->enum('report_type', ['simple', 'advanced'])->default('simple');
            $table->string('data_source');
            $table->json('columns');
            $table->json('filters')->nullable();
            $table->json('joins')->nullable();
            $table->json('calculations')->nullable();
            $table->json('group_by')->nullable();
            $table->json('order_by')->nullable();
            $table->boolean('cache_enabled')->default(true);
            $table->integer('cache_duration_minutes')->default(10);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false);
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_reports');
    }
};
