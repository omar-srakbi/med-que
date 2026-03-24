<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->change();
            $table->string('birth_place')->nullable()->change();
            $table->string('father_name')->nullable()->change();
            $table->string('mother_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->date('birth_date')->nullable(false)->change();
            $table->string('birth_place')->nullable(false)->change();
            $table->string('father_name')->nullable(false)->change();
            $table->string('mother_name')->nullable(false)->change();
        });
    }
};
