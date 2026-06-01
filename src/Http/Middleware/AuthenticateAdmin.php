<?php

namespace Ssh521\LaravelAdmin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAdmin
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guard = $this->guard();

        if (Auth::guard($guard)->check()) {
            Auth::shouldUse($guard);
            $request->setUserResolver(fn () => Auth::guard($guard)->user());

            return $next($request);
        }

        if ($request->expectsJson()) {
            abort(401);
        }

        $adminLoginRoute = config('laravel-admin.route_name_prefix', 'admin.').'login';

        if (config('laravel-admin.auth.admin_login.enabled', false) && Route::has($adminLoginRoute)) {
            return redirect()->guest(route($adminLoginRoute));
        }

        return redirect()->guest(Route::has('login') ? route('login') : url('/login'));
    }

    private function guard(): string
    {
        return config('laravel-admin.auth.guard', 'laravel_admin');
    }
}
