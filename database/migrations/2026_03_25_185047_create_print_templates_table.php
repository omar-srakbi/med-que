<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->json('template_data'); // Stores all template settings
            $table->boolean('is_default')->default(false);
            $table->string('template_type')->default('receipt'); // receipt, ticket, medical_record
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('template_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_templates');
    }
};
