<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('sequence_prefix', 2)->unique(); // 2-char prefix or 'GLOBAL' for shared sequence
            $table->integer('sequence_counter')->default(0);
            $table->integer('sequence_year')->default(2026);
            $table->timestamps();

            $table->index(['sequence_prefix', 'sequence_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_sequences');
    }
};
