<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_reports', function (Blueprint $table) {
            $table->text('report_header')->nullable()->after('row_height');
            $table->text('report_footer')->nullable()->after('report_header');
        });
    }

    public function down(): void
    {
        Schema::table('custom_reports', function (Blueprint $table) {
            $table->dropColumn(['report_header', 'report_footer']);
        });
    }
};
