<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->string('ticket_prefix')->default('TKT')->after('name_ar');
            $table->string('ticket_number_format')->default('{prefix}-{date}-{seq}')->after('ticket_prefix');
            $table->integer('ticket_seq_padding')->default(4)->after('ticket_number_format');
            $table->integer('ticket_current_seq')->default(0)->after('ticket_seq_padding');
            $table->date('ticket_seq_reset_date')->nullable()->after('ticket_current_seq');
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['ticket_prefix', 'ticket_number_format', 'ticket_seq_padding', 'ticket_current_seq', 'ticket_seq_reset_date']);
        });
    }
};
