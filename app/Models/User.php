<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'hire_date',
        'salary',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'salary' => 'decimal:2',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function createdPatients(): HasMany
    {
        return $this->hasMany(Patient::class, 'created_by');
    }

    public function cashierTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'cashier_id');
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'doctor_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'cashier_id');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function canBookAdvanceTickets(): bool
    {
        // Check if user has a role
        if (!$this->role) {
            return false;
        }
        
        // Check if user has the advance booking permission
        $permissions = $this->role->permissions ?? [];
        
        // Admin has all permissions
        if ($this->role->name === 'Admin') {
            return true;
        }
        
        // Check for specific permission or all permissions
        return in_array('*', $permissions) || in_array('create_advance_tickets', $permissions);
    }
    
    public function hasPermission($permission): bool
    {
        // Check if user has a role
        if (!$this->role) {
            return false;
        }
        
        $permissions = $this->role->permissions ?? [];
        return in_array('*', $permissions) || in_array($permission, $permissions);
    }
}
