<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Auditable;

class Ticket extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'ticket_number',
        'patient_id',
        'department_id',
        'service_id',
        'cashier_id',
        'queue_number',
        'amount_paid',
        'visit_date',
        'created_at_time',
        'called_number',
        'completed_at',
        'is_advance_booking',
        'booking_date',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid' => 'decimal:2',
            'visit_date' => 'date',
            'booking_date' => 'date',
            'created_at_time' => 'datetime:H:i',
            'called_number' => 'integer',
            'completed_at' => 'datetime',
            'is_advance_booking' => 'boolean',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function medicalRecord(): HasOne
    {
        return $this->hasOne(MedicalRecord::class);
    }
}
