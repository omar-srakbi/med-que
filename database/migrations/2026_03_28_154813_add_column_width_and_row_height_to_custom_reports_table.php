<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_reports', function (Blueprint $table) {
            $table->integer('column_width')->default(150)->after('order_by');
            $table->integer('row_height')->default(40)->after('column_width');
        });
    }

    public function down(): void
    {
        Schema::table('custom_reports', function (Blueprint $table) {
            $table->dropColumn(['column_width', 'row_height']);
        });
    }
};
