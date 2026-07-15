<?php

namespace Ssh521\LaravelAdmin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\SessionManager;
use Symfony\Component\HttpFoundation\Response;

class UseAdminSession
{
    private const REQUEST_AREA_ATTRIBUTE = 'laravel-admin.is-admin-request';

    public function __construct(private readonly SessionManager $sessions) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isAdminRequest = $this->isAdminRequest($request);

        $request->attributes->set(self::REQUEST_AREA_ATTRIBUTE, $isAdminRequest);

        if (! config('laravel-admin.auth.session.enabled', true) || ! $isAdminRequest) {
            return $next($request);
        }

        $defaultCookie = (string) config('session.cookie');
        $adminCookie = (string) config('laravel-admin.auth.session.cookie');
        $session = $this->sessions->driver();
        $defaultSessionName = $session->getName();

        config()->set('session.cookie', $adminCookie);
        $session->setName($adminCookie);

        try {
            return $next($request);
        } finally {
            config()->set('session.cookie', $defaultCookie);
            $session->setName($defaultSessionName);
        }
    }

    private function isAdminRequest(Request $request): bool
    {
        $routeNamePrefix = (string) config('laravel-admin.route_name_prefix', 'admin.');

        if ($routeNamePrefix !== '' && $request->routeIs($routeNamePrefix.'*')) {
            return true;
        }

        if ($this->isAdminPath('/'.$request->path())) {
            return true;
        }

        if (! $request->hasHeader('X-Livewire')) {
            return false;
        }

        $referer = $request->headers->get('referer');

        if (! is_string($referer) || parse_url($referer, PHP_URL_HOST) !== $request->getHost()) {
            return false;
        }

        $refererPath = parse_url($referer, PHP_URL_PATH);

        return is_string($refererPath) && $this->isAdminPath($refererPath);
    }

    private function isAdminPath(string $path): bool
    {
        $path = trim($path, '/');
        $adminPrefix = trim((string) config('laravel-admin.route_prefix', 'admin'), '/');

        return $adminPrefix !== ''
            && ($path === $adminPrefix || str_starts_with($path, $adminPrefix.'/'));
    }
}
