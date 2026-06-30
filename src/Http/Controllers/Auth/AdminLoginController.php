<?php

namespace Ssh521\LaravelAdmin\Http\Controllers\Auth;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Ssh521\LaravelAdmin\Http\Controllers\Controller;
use Ssh521\LaravelAdmin\Support\AdminBroadcastNotifier;

class AdminLoginController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::guard($this->guard())->check()) {
            return redirect()->intended($this->redirectPath());
        }

        return view('laravel-admin::admin.auth.login', [
            'loginRoute' => $this->routeName('login'),
            'showRegisterLink' => false,
            'showPasswordResetLink' => false,
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::guard($this->guard())->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        app(AdminBroadcastNotifier::class)->adminLoggedIn($request);

        return redirect()->intended($this->redirectPath());
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard($this->guard())->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($this->routeName('login'));
    }

    private function guard(): string
    {
        return config('laravel-admin.auth.guard', 'laravel_admin');
    }

    private function redirectPath(): string
    {
        return config('laravel-admin.auth.admin_login.redirect_to', '/admin');
    }

    private function routeName(string $name): string
    {
        return config('laravel-admin.route_name_prefix', 'admin.').$name;
    }
}
