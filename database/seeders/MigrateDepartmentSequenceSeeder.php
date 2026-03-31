<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\TicketSequence;

class MigrateDepartmentSequenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = (int) now()->year;
        $departments = Department::all();
        $globalSequence = 0;

        // Create global sequence record
        TicketSequence::updateOrCreate(
            ['sequence_prefix' => 'TK', 'sequence_year' => $currentYear],
            ['sequence_counter' => 0]
        );

        foreach ($departments as $department) {
            // Extract first 2 characters from old ticket_prefix (or use 'TK' as default)
            $oldPrefix = $department->ticket_prefix ?? 'TKT';
            $newPrefix = strtoupper(substr($oldPrefix, 0, 2));

            $department->update([
                'sequence_prefix' => $newPrefix,
            ]);

            // If using 'TK' prefix, they share the global sequence
            // Custom prefixes get their own sequence
            if ($newPrefix !== 'TK') {
                TicketSequence::firstOrCreate(
                    ['sequence_prefix' => $newPrefix, 'sequence_year' => $currentYear],
                    ['sequence_counter' => 0]
                );
            }
        }

        $this->command->info('Migrated ' . $departments->count() . ' departments to new sequence system.');
        $this->command->info('All departments with "TK" prefix share a global sequence counter.');
    }
}
