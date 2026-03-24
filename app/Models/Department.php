<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'is_active',
        'ticket_prefix',
        'ticket_number_format',
        'ticket_seq_padding',
        'ticket_current_seq',
        'ticket_seq_reset_date',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'ticket_seq_padding' => 'integer',
            'ticket_current_seq' => 'integer',
            'ticket_seq_reset_date' => 'date',
        ];
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class);
    }
}
