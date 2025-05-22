<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
{
    $user = auth()->user();

    if ($user && $user->hasRole('staff') && $user->must_change_password) {
        // Tránh redirect vòng lặp: bỏ qua route đổi mật khẩu và logout
        if (
            !$request->is('password/change') &&
            !$request->is('logout')
        ) {
            return redirect()->route('password.change.form');

        }
    }

    return $next($request);
}


}
