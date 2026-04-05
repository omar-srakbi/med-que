<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('currency_code')->default('SYP')->after('value');
            $table->string('currency_symbol')->default('ل.س')->after('currency_code');
            $table->integer('currency_decimals')->default(2)->after('currency_symbol');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['currency_code', 'currency_symbol', 'currency_decimals']);
        });
    }
};
