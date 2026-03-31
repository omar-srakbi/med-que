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
        Schema::table('departments', function (Blueprint $table) {
            // Add sequence_prefix column (keep only prefix, counter/year go to ticket_sequences table)
            $table->string('sequence_prefix', 2)->default('TK')->after('is_active');

            // Drop old sequence columns
            $table->dropColumn([
                'ticket_prefix',
                'ticket_number_format',
                'ticket_seq_padding',
                'ticket_current_seq',
                'ticket_seq_reset_date',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Restore old sequence columns
            $table->string('ticket_prefix')->default('TKT')->after('name_ar');
            $table->string('ticket_number_format')->default('{prefix}-{date}-{seq}')->after('ticket_prefix');
            $table->integer('ticket_seq_padding')->default(4)->after('ticket_number_format');
            $table->integer('ticket_current_seq')->default(0)->after('ticket_seq_padding');
            $table->date('ticket_seq_reset_date')->nullable()->after('ticket_current_seq');

            // Drop new sequence column
            $table->dropColumn(['sequence_prefix']);
        });
    }
};
