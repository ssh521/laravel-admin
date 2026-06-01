{{--
    ============================================================
    공용 Footer 컴포넌트
    파일: resources/views/components/footer.blade.php

    사용법:
        <x-laravel-admin::footer />
        <x-laravel-admin::footer max-width="max-w-screen-2xl" />

    config/site.php 에서 값 관리 권장
    ============================================================
--}}

@props([
    'maxWidth'           => 'max-w-7xl',
    'companyName'        => config('site.company_name', 'OOO 주식회사'),
    'companyNameEn'      => config('site.company_name_en', 'OOO Inc.'),
    'ceo'                => config('site.ceo', 'OOO'),
    'privacyOfficer'     => config('site.privacy_officer', 'OOO'),
    'businessNumber'     => config('site.business_number', '000-00-00000'),
    'mailOrderNumber'    => config('site.mail_order_number', '제0000-서울OO-0000호'),
    'address'            => config('site.address', '서울특별시 OO구 OO로 000'),
    'tel'                => config('site.tel', '02-0000-0000'),
    'fax'                => config('site.fax', ''),
    'email'              => config('site.contact_email', 'contact@example.com'),
    'privacyEmail'       => config('site.privacy_email', 'privacy@example.com'),
    'siteUrl'            => config('app.url', 'https://example.com'),
    'kakaoChannelUrl'    => config('site.kakao_channel_url', ''),
    'copyrightYear'      => config('site.copyright_year', date('Y')),
])

@php
    $termsUrl = Route::has('terms') ? route('terms') : url('/terms');
    $privacyUrl = Route::has('privacy') ? route('privacy') : url('/privacy');
@endphp

<footer class="bg-white text-zinc-500 dark:bg-zinc-900 dark:text-zinc-400">

    {{-- 상단 구분선 --}}
    <div class="h-px bg-gradient-to-r from-transparent via-zinc-300 to-transparent dark:via-zinc-700"></div>

    <div class="mx-auto {{ $maxWidth }} px-2 py-6 lg:px-6">

        {{-- 사업자 정보 --}}
        <div class="flex flex-col gap-6 text-xs leading-relaxed lg:flex-row lg:items-end lg:justify-between">

            <div class="space-y-1 text-center text-zinc-600 lg:text-left dark:text-zinc-400">
                <p class="flex flex-wrap justify-center gap-x-4 gap-y-1 lg:justify-start">
                    <span><span class="text-zinc-500">상호 :</span> {{ $companyName }}</span>
                    <span><span class="text-zinc-500">대표 :</span> {{ $ceo }}</span>
                    <span>
                        <span class="text-zinc-500">사업자등록번호 :</span> {{ $businessNumber }}
                        <a
                            href="https://www.ftc.go.kr/bizCommPop.do?wrkr_no={{ str_replace('-', '', $businessNumber) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="ml-1 inline-flex items-center gap-0.5 rounded bg-zinc-200 px-1.5 py-0.5 text-[10px] text-zinc-600 transition hover:bg-zinc-300 hover:text-zinc-900 dark:bg-zinc-800 dark:text-zinc-400 dark:hover:bg-zinc-700 dark:hover:text-white"
                        >
                            사업자 확인
                            <svg class="size-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                        </a>
                    </span>
                </p>

                @if($mailOrderNumber)
                <p>
                    <span class="text-zinc-500">통신판매업 신고번호 :</span> {{ $mailOrderNumber }}
                </p>
                @endif

                <p class="flex flex-wrap justify-center gap-x-4 gap-y-1 lg:justify-start">
                    <span><span class="text-zinc-500">주소 :</span> {{ $address }}</span>
                </p>

                <p class="flex flex-wrap justify-center gap-x-4 gap-y-1 lg:justify-start">
                    <span>
                        <span class="text-zinc-500">대표전화 :</span>
                        <a href="tel:{{ preg_replace('/[^0-9]/', '', $tel) }}" class="hover:text-zinc-900 dark:hover:text-white">{{ $tel }}</a>
                    </span>
                    @if($fax)
                    <span><span class="text-zinc-500">팩스 :</span> {{ $fax }}</span>
                    @endif
                    <span>
                        <span class="text-zinc-500">이메일 :</span>
                        <a href="mailto:{{ $email }}" class="hover:text-zinc-900 dark:hover:text-white">{{ $email }}</a>
                    </span>
                </p>

                {{-- 구글·카카오 심사 필수: 개인정보보호책임자 명시 --}}
                <p class="flex flex-wrap justify-center gap-x-4 gap-y-1 lg:justify-start">
                    <span><span class="text-zinc-500">개인정보 보호책임자 :</span> {{ $privacyOfficer }}</span>
                    <span>
                        <span class="text-zinc-500">개인정보 문의 :</span>
                        <a href="mailto:{{ $privacyEmail }}" class="hover:text-zinc-900 dark:hover:text-white">{{ $privacyEmail }}</a>
                    </span>
                </p>
            </div>

            {{-- 저작권 --}}
            <div class="flex shrink-0 flex-col gap-2 rounded-lg border border-zinc-200 px-4 py-3 dark:border-zinc-800 bg-zinc-100 dark:bg-zinc-800">
                <span class="flex gap-2 justify-center">
                    <a href="{{ $termsUrl }}" class="font-semibold hover:text-zinc-900 dark:hover:text-white">이용약관</a>
                    <span>|</span>
                    <a href="{{ $privacyUrl }}" class="font-semibold hover:text-zinc-900 dark:hover:text-white">개인정보처리방침</a>
                </span>
                <span class="text-zinc-600 dark:text-zinc-500 text-center lg:text-right">
                    &copy; {{ $copyrightYear }} {{ $companyNameEn }}. All rights reserved.
                </span>
            </div>
        </div>

    </div>
</footer>
