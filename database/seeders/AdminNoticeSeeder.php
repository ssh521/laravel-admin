<?php

namespace Ssh521\LaravelAdmin\Database\Seeders;

use Illuminate\Database\Seeder;
use Ssh521\LaravelAdmin\Models\AdminNotice;
use Ssh521\LaravelAdmin\Models\User;

class AdminNoticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * 실행 방법: php artisan db:seed --class=AdminNoticeSeeder
     */
    public function run(): void
    {
        // 기존 공지사항 데이터 삭제 (선택사항)
        // AdminNotice::truncate();

        // 사용자 조회 (작성자로 사용)
        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->warn('사용자가 없습니다. 먼저 사용자를 생성해주세요.');

            return;
        }

        // 샘플 공지사항 데이터
        $notices = [
            [
                'title' => '시스템 점검 안내',
                'content' => '안녕하세요. 시스템 안정성 향상을 위한 정기 점검이 예정되어 있습니다.

점검 일시: 2024년 12월 25일 02:00 ~ 04:00 (2시간)
점검 내용: 서버 업그레이드 및 데이터베이스 최적화

점검 시간 동안 서비스 이용이 일시 중단될 수 있으니 양해 부탁드립니다.',
                'is_published' => true,
                'published_at' => now()->subDays(5),
            ],
            [
                'title' => '새로운 기능 업데이트 안내',
                'content' => '관리자 페이지에 새로운 기능이 추가되었습니다.

주요 변경사항:
- 대시보드 통계 기능 개선
- 사용자 관리 UI 개선
- 공지사항 관리 기능 추가

더 나은 서비스 제공을 위해 지속적으로 개선하겠습니다.',
                'is_published' => true,
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => '비밀번호 변경 안내',
                'content' => '보안 강화를 위해 정기적인 비밀번호 변경을 권장드립니다.

비밀번호 변경 주기: 90일
비밀번호 규칙:
- 최소 8자 이상
- 영문, 숫자, 특수문자 조합

보안을 위해 강력한 비밀번호를 사용해주시기 바랍니다.',
                'is_published' => true,
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => '공지사항 작성 가이드',
                'content' => '공지사항 작성 시 다음 사항을 참고해주세요.

1. 제목은 명확하고 간결하게 작성
2. 내용은 읽기 쉽게 문단을 나누어 작성
3. 중요한 내용은 강조 표시
4. 첨부파일이 필요한 경우 첨부

문의사항이 있으시면 관리자에게 연락주시기 바랍니다.',
                'is_published' => false,
                'published_at' => null,
            ],
            [
                'title' => '연말 연휴 운영 안내',
                'content' => '2024년 연말 연휴 기간 중 운영 안내입니다.

운영 일정:
- 12월 30일 ~ 1월 1일: 휴무
- 1월 2일부터 정상 운영

긴급한 문의사항은 이메일로 접수해주시기 바랍니다.

새해 복 많이 받으세요!',
                'is_published' => true,
                'published_at' => now()->subWeeks(2),
            ],
        ];

        // 공지사항 생성
        foreach ($notices as $noticeData) {
            AdminNotice::create([
                'title' => $noticeData['title'],
                'content' => $noticeData['content'],
                'author_id' => $users->random()->id,
                'is_published' => $noticeData['is_published'],
                'published_at' => $noticeData['published_at'],
            ]);
        }

        // 추가 테스트 데이터 생성 (Factory 사용)
        AdminNotice::factory()
            ->count(100)
            ->create([
                'author_id' => $users->random()->id,
            ]);

        $this->command->info('공지사항 시드 데이터가 생성되었습니다.');
    }
}
