<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'group'];

    protected function casts(): array
    {
        return [
            'value' => match($this->type) {
                'boolean' => 'boolean',
                'json' => 'array',
                default => 'string',
            },
        ];
    }

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value)
    {
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            $setting->update(['value' => $value]);
        } else {
            self::create([
                'key' => $key,
                'value' => $value,
                'type' => is_bool($value) ? 'boolean' : (is_array($value) ? 'json' : 'text'),
            ]);
        }
        
        return true;
    }
    
    public static function getCurrencyCode()
    {
        return self::where('key', 'currency_code')->first()?->value ?? 'JOD';
    }
    
    public static function getCurrencySymbol()
    {
        return self::where('key', 'currency_symbol')->first()?->value ?? 'JD';
    }
    
    public static function getCurrencyDecimals()
    {
        return (int) (self::where('key', 'currency_decimals')->first()?->value ?? 2);
    }
    
    public static function formatCurrency($amount)
    {
        $decimals = self::getCurrencyDecimals();
        $symbol = self::getCurrencySymbol();
        
        return number_format($amount, $decimals) . ' ' . $symbol;
    }
    
    public static function getClinicName()
    {
        if (app()->getLocale() === 'ar') {
            return self::where('key', 'clinic_name_ar')->first()?->value ?? 'المركز الطبي';
        }
        return self::where('key', 'clinic_name')->first()?->value ?? 'Medical Center';
    }
}
