<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle($request, Closure $next, $permissionName)
    {
        $user = Auth::user();

        if (!$user || !$user->hasPermission($permissionName)) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}

