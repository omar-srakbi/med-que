<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();
        
        // Check if user has a role
        if (!$user->role) {
            abort(403, 'Your account does not have a role assigned. Please contact the administrator.');
        }
        
        $userPermissions = $user->role->permissions ?? [];

        if (in_array('*', $userPermissions) || in_array($permission, $userPermissions)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
