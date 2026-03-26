<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('print_type'); // ticket, receipt, medical_record, report
            $table->string('record_type')->nullable(); // App\Models\Ticket
            $table->unsignedBigInteger('record_id')->nullable();
            $table->string('printer_name')->nullable();
            $table->integer('copies')->default(1);
            $table->string('status')->default('success'); // success, failed
            $table->text('error_message')->nullable();
            $table->timestamp('printed_at');
            $table->timestamps();
            
            $table->index(['user_id', 'print_type']);
            $table->index('printed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_logs');
    }
};
