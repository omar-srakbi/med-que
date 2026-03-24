<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class PermissionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::if('can', function ($permission) {
            $user = auth()->user();
            if (!$user) {
                return false;
            }
            
            $userPermissions = $user->role->permissions ?? [];
            return in_array('*', $userPermissions) || in_array($permission, $userPermissions);
        });
    }
}
