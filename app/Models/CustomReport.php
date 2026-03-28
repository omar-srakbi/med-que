<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'description',
        'report_type',
        'data_source',
        'columns',
        'column_labels',
        'filters',
        'joins',
        'calculations',
        'group_by',
        'order_by',
        'column_width',
        'row_height',
        'report_header',
        'report_footer',
        'cache_enabled',
        'cache_duration_minutes',
        'is_active',
        'is_public',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'columns' => 'array',
        'filters' => 'array',
        'joins' => 'array',
        'calculations' => 'array',
        'group_by' => 'array',
        'order_by' => 'array',
        'cache_enabled' => 'boolean',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(ReportPermission::class, 'report_id');
    }

    public function cache(): HasOne
    {
        return $this->hasOne(ReportCache::class, 'report_id');
    }

    public function scopes(): array
    {
        return [
            'active' => function ($query) {
                return $query->where('is_active', true);
            },
            'public' => function ($query) {
                return $query->where('is_public', true);
            },
            'ownedBy' => function ($query, $userId) {
                return $query->where('created_by', $userId);
            },
        ];
    }

    public function canView($user): bool
    {
        // Owner can always view
        if ($this->created_by === $user->id) {
            return true;
        }

        // Check if public
        if ($this->is_public) {
            return true;
        }

        // Check permissions
        $permission = $this->permissions()
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('role_id', $user->role_id);
            })
            ->where('can_view', true)
            ->first();

        return $permission !== null;
    }

    public function canEdit($user): bool
    {
        // Owner can always edit
        if ($this->created_by === $user->id) {
            return true;
        }

        // Check permissions
        $permission = $this->permissions()
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('role_id', $user->role_id);
            })
            ->where('can_edit', true)
            ->first();

        return $permission !== null;
    }

    public function canDelete($user): bool
    {
        // Owner can always delete
        if ($this->created_by === $user->id) {
            return true;
        }

        // Check permissions
        $permission = $this->permissions()
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('role_id', $user->role_id);
            })
            ->where('can_delete', true)
            ->first();

        return $permission !== null;
    }

    public function canExport($user): bool
    {
        // Owner can always export
        if ($this->created_by === $user->id) {
            return true;
        }

        // Check permissions
        $permission = $this->permissions()
            ->where(function($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('role_id', $user->role_id);
            })
            ->where('can_export', true)
            ->first();

        return $permission !== null;
    }

    public function getCache(): ?array
    {
        $cache = $this->cache()->where('expires_at', '>', now())->first();

        if ($cache) {
            $cacheData = $cache->cache_data;
            // Handle both array and JSON string
            if (is_string($cacheData)) {
                $cacheData = json_decode($cacheData, true);
            }
            
            return [
                'data' => $cacheData,
                'cached' => true,
                'cached_at' => $cache->created_at,
                'expires_at' => $cache->expires_at,
            ];
        }

        return null;
    }

    public function setCache(array $data): void
    {
        if (!$this->cache_enabled) {
            return;
        }

        $cache = $this->cache()->first();
        
        if ($cache) {
            $cache->update([
                'cache_key' => $this->generateCacheKey(),
                'cache_data' => json_encode($data),
                'expires_at' => now()->addMinutes($this->cache_duration_minutes),
            ]);
        } else {
            ReportCache::create([
                'report_id' => $this->id,
                'cache_key' => $this->generateCacheKey(),
                'cache_data' => json_encode($data),
                'expires_at' => now()->addMinutes($this->cache_duration_minutes),
            ]);
        }
    }

    public function clearCache(): void
    {
        $this->cache()->delete();
    }

    private function generateCacheKey(): string
    {
        return 'report_' . $this->id . '_' . md5(json_encode($this->filters) . now()->format('Y-m-d-H'));
    }
}
