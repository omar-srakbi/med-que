<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->boolean('is_profile_complete')->default(false)->after('phone');
            $table->timestamp('completed_at')->nullable()->after('is_profile_complete');
        });
        
        // Mark existing patients as complete
        DB::table('patients')->update(['is_profile_complete' => true, 'completed_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['is_profile_complete', 'completed_at']);
        });
    }
};
