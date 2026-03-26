<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PrintLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'print_type',
        'record_type',
        'record_id',
        'printer_name',
        'copies',
        'status',
        'error_message',
        'printed_at',
    ];

    protected function casts(): array
    {
        return [
            'copies' => 'integer',
            'printed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function record(): MorphTo
    {
        return $this->morphTo();
    }

    public static function logPrint($type, $record, $printerName = null, $copies = 1, $status = 'success', $errorMessage = null)
    {
        return self::create([
            'user_id' => auth()->id(),
            'print_type' => $type,
            'record_type' => get_class($record),
            'record_id' => $record->id,
            'printer_name' => $printerName,
            'copies' => $copies,
            'status' => $status,
            'error_message' => $errorMessage,
            'printed_at' => now(),
        ]);
    }
}
