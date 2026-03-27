<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Auditable;

class Patient extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'first_name',
        'last_name',
        'father_name',
        'mother_name',
        'birth_date',
        'birth_place',
        'national_id',
        'phone',
        'created_by',
        'is_profile_complete',
        'completed_at',
    ];

    protected $appends = [
        'full_name',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_profile_complete' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Scopes
    public function scopeIncomplete(Builder $query): Builder
    {
        return $query->where('is_profile_complete', false);
    }

    public function scopeComplete(Builder $query): Builder
    {
        return $query->where('is_profile_complete', true);
    }

    // Methods
    public function isComplete(): bool
    {
        return $this->is_profile_complete;
    }

    public function markComplete(): void
    {
        $this->update([
            'is_profile_complete' => true,
            'completed_at' => now(),
        ]);
    }
}
