<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAdminAccess
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        // Kiểm tra đã đăng nhập và có role admin hoặc staff
        if ($user && $user->roles()->whereIn('name', ['admin', 'staff'])->exists()) {
            return $next($request);
        }

        // Nếu chưa có quyền thì chuyển tới trang yêu cầu truy cập
        return redirect()->route('admin.request-access');
    }
}
