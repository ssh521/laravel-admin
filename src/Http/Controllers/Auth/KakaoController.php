<?php

namespace Ssh521\LaravelAdmin\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Http\Controllers\Controller;
use Ssh521\LaravelAdmin\Models\User;

class KakaoController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('kakao')->redirect();
    }

    public function callback()
    {
        try {
            $providerUser = Socialite::driver('kakao')->user();
        } catch (\Throwable $e) {
            Log::error('Kakao OAuth error', ['error' => $e->getMessage()]);

            return Redirect::route('login')->withErrors(['kakao' => '카카오 로그인에 실패했습니다.']);
        }

        // 먼저 카카오 provider_id로 기존 사용자 찾기
        $user = User::where('provider', 'kakao')
            ->where('provider_id', (string) $providerUser->getId())
            ->first();

        // 카카오 계정이 없으면 이메일로 기존 사용자 찾기 (이메일이 있는 경우에만)
        if (! $user && $providerUser->getEmail()) {
            $user = User::where('email', $providerUser->getEmail())->first();

            // 기존 사용자가 있다면 카카오 정보 연결
            if ($user) {
                $user->forceFill([
                    'provider' => 'kakao',
                    'provider_id' => (string) $providerUser->getId(),
                ])->save();
            }
        }

        // 새 사용자 생성
        if (! $user) {
            $email = $providerUser->getEmail();

            // 이메일이 없거나 이미 사용 중인 경우 고유한 이메일 생성
            if (! $email || User::where('email', $email)->exists()) {
                $email = 'kakao_'.$providerUser->getId().'@kakao.local';
            }

            $user = User::create([
                'name' => $providerUser->getName() ?: ($providerUser->getNickname() ?? 'KakaoUser_'.Str::random(6)),
                'email' => $email,
                'password' => bcrypt(Str::random(32)),
                'provider' => 'kakao',
                'provider_id' => (string) $providerUser->getId(),
                'email_verified_at' => now(),
            ]);

            // 새 사용자에게 기본 역할 할당
            if (class_exists(Role::class)) {
                $defaultRole = Role::where('name', 'User')->first();
                if ($defaultRole) {
                    $user->assignRole($defaultRole);
                }
            }
        }

        Auth::login($user, true);

        return Redirect::intended(route('dashboard'));
    }
}
