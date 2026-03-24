<?php

namespace App;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            AuditLog::log('created', self::getActionDescription($model, 'created'), $model, null, $model->toArray());
        });

        static::updated(function ($model) {
            $oldValues = $model->getOriginal();
            $newValues = $model->toArray();
            $changes = $model->getChanges();
            
            // Only log if there are actual changes
            if (!empty($changes)) {
                AuditLog::log('updated', self::getActionDescription($model, 'updated'), $model, $oldValues, $newValues);
            }
        });

        static::deleted(function ($model) {
            AuditLog::log('deleted', self::getActionDescription($model, 'deleted'), $model, $model->toArray(), null);
        });
    }

    private static function getActionDescription($model, string $action): string
    {
        $modelName = class_basename($model);
        $user = auth()->user();
        $userName = $user ? $user->full_name : 'System';
        
        return "{$userName} {$action} {$modelName} #{$model->id}";
    }
}
