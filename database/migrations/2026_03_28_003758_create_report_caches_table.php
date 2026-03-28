<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_caches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('custom_reports')->onDelete('cascade');
            $table->string('cache_key')->unique();
            $table->longText('cache_data');
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_caches');
    }
};
