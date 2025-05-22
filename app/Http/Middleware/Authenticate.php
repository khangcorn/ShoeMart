<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }

    protected function authenticate($request, array $guards)
    {
        parent::authenticate($request, $guards);

        if (auth()->check() && auth()->user()->is_blocked) {
            auth()->logout();

            throw ValidationException::withMessages([
                'error' => ['Tài khoản của bạn đã bị khóa. Vui lòng liên hệ Admin.'],
            ]);
        }
    }
}
