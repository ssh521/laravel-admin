<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\TestResponse;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Tests\TestCase;

class AdminSessionIsolationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('session.cookie', 'application-session');
        config()->set('laravel-admin.auth.session.enabled', true);
        config()->set('laravel-admin.auth.session.cookie', 'administrator-session');
    }

    public function test_public_admin_and_admin_livewire_requests_use_the_correct_cookie(): void
    {
        Route::get('/_test/session', fn () => response('user-session'))
            ->middleware('web')
            ->name('test.session');
        Route::get('/admin/_test/session', fn () => response('admin-session'))
            ->middleware('web')
            ->name('admin.test.session');
        Route::get('/_test/livewire-session', fn () => response('admin-livewire-session'))
            ->middleware('web')
            ->name('test.livewire-session');

        $this->assertContains(
            'application-session',
            $this->responseCookieNames($this->get('/_test/session')),
        );
        $this->assertContains(
            'administrator-session',
            $this->responseCookieNames($this->get('/admin/_test/session')),
        );
        $this->assertContains(
            'administrator-session',
            $this->responseCookieNames($this->withHeaders([
                'X-Livewire' => 'true',
                'Referer' => url('/admin/users'),
            ])->get('/_test/livewire-session')),
        );
    }

    public function test_session_separation_can_be_disabled(): void
    {
        config()->set('laravel-admin.auth.session.enabled', false);

        $response = $this->get('/admin/login');

        $this->assertContains('application-session', $this->responseCookieNames($response));
        $this->assertNotContains('administrator-session', $this->responseCookieNames($response));
    }

    public function test_invalidating_either_session_keeps_the_other_session_available(): void
    {
        Route::post('/_test/user-session', function (Request $request) {
            $request->session()->put('user-session-marker', true);

            return response('stored');
        })->middleware('web')->name('test.user-session.store');
        Route::get('/_test/user-session', fn (Request $request) => response()->json([
            'available' => $request->session()->get('user-session-marker', false),
        ]))->middleware('web')->name('test.user-session.show');
        Route::post('/_test/user-session/logout', function (Request $request) {
            $request->session()->invalidate();

            return response('invalidated');
        })->middleware('web')->name('test.user-session.destroy');
        Route::get('/admin/_test/authenticated', fn () => response('admin-authenticated'))
            ->middleware(['web', 'auth:laravel_admin'])
            ->name('admin.test.authenticated');

        $admin = AdminUser::query()->create([
            'name' => 'Session Admin',
            'email' => 'session-admin@example.com',
            'password' => 'password',
        ]);

        $userCookie = $this->post('/_test/user-session')
            ->getCookie('application-session')?->getValue();

        $this->assertNotNull($userCookie);

        Auth::forgetGuards();

        $adminCookie = $this->withCookie('application-session', $userCookie)
            ->post('/admin/login', [
                'email' => $admin->email,
                'password' => 'password',
            ])
            ->getCookie('administrator-session')?->getValue();

        $this->assertNotNull($adminCookie);

        Auth::forgetGuards();

        $this->withCookies([
            'application-session' => $userCookie,
            'administrator-session' => $adminCookie,
        ])->post('/admin/logout')
            ->assertRedirect('/admin/login');

        Auth::forgetGuards();

        $this->withCookie('application-session', $userCookie)
            ->get('/_test/user-session')
            ->assertOk()
            ->assertJson(['available' => true]);

        Auth::forgetGuards();

        $freshAdminCookie = $this->withCookie('application-session', $userCookie)
            ->post('/admin/login', [
                'email' => $admin->email,
                'password' => 'password',
            ])
            ->getCookie('administrator-session')?->getValue();

        $this->assertNotNull($freshAdminCookie);

        Auth::forgetGuards();

        $this->withCookies([
            'application-session' => $userCookie,
            'administrator-session' => $freshAdminCookie,
        ])->post('/_test/user-session/logout')
            ->assertOk();

        Auth::forgetGuards();

        $this->withCookie('administrator-session', $freshAdminCookie)
            ->get('/admin/_test/authenticated')
            ->assertOk()
            ->assertSeeText('admin-authenticated');
    }

    /**
     * @return list<string>
     */
    private function responseCookieNames(TestResponse $response): array
    {
        return array_map(
            static fn ($cookie): string => $cookie->getName(),
            $response->headers->getCookies(),
        );
    }
}
