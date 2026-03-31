<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketSequence extends Model
{
    protected $fillable = [
        'sequence_prefix',
        'sequence_counter',
        'sequence_year',
    ];

    protected $casts = [
        'sequence_counter' => 'integer',
        'sequence_year' => 'integer',
    ];

    /**
     * Get the departments using this sequence prefix
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'sequence_prefix', 'sequence_prefix');
    }

    /**
     * Get or create sequence for a prefix
     */
    public static function getOrCreate(string $prefix, int $year): self
    {
        return static::firstOrCreate(
            ['sequence_prefix' => $prefix, 'sequence_year' => $year],
            ['sequence_counter' => 0]
        );
    }

    /**
     * Get next sequence number
     */
    public function getNext(): int
    {
        $this->increment('sequence_counter');
        return $this->sequence_counter;
    }

    /**
     * Reset sequence for new year
     */
    public function resetForYear(int $year): void
    {
        if ($this->sequence_year !== $year) {
            $this->update([
                'sequence_counter' => 0,
                'sequence_year' => $year,
            ]);
        }
    }
}
