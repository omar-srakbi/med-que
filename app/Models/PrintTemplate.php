<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'template_data',
        'is_default',
        'template_type',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'template_data' => 'array',
            'is_default' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getDefault($type = 'receipt')
    {
        return self::where('template_type', $type)
            ->where('is_default', true)
            ->first();
    }

    public function setAsDefault()
    {
        // Remove default from other templates of same type
        self::where('template_type', $this->template_type)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
        
        // Set this as default
        $this->update(['is_default' => true]);
        
        return true;
    }
}
