<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'setting_key',
        'setting_value',
        'setting_type',
        'category',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'setting_value' => match($this->setting_type) {
                'boolean' => 'boolean',
                'json' => 'array',
                'integer' => 'integer',
                default => 'string',
            },
        ];
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function get($key, $default = null)
    {
        $setting = self::where('setting_key', $key)->first();
        return $setting ? $setting->setting_value : $default;
    }

    public static function set($key, $value, $type = 'string', $category = 'general')
    {
        $setting = self::where('setting_key', $key)->first();
        
        if ($setting) {
            $setting->update([
                'setting_value' => is_array($value) ? json_encode($value) : $value,
                'setting_type' => $type,
                'updated_by' => auth()->id(),
            ]);
        } else {
            self::create([
                'setting_key' => $key,
                'setting_value' => is_array($value) ? json_encode($value) : $value,
                'setting_type' => $type,
                'category' => $category,
                'updated_by' => auth()->id(),
            ]);
        }
        
        return true;
    }
}
