<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCache extends Model
{
    protected $fillable = [
        'report_id',
        'cache_key',
        'cache_data',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(CustomReport::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function getData(): array
    {
        return $this->cache_data ?? [];
    }

    public function updateData(array $newData): void
    {
        $this->update([
            'cache_data' => $newData,
            'expires_at' => now()->addMinutes($this->report->cache_duration_minutes),
        ]);
    }
}
