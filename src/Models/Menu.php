<?php

namespace Ssh521\LaravelAdmin\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * Menu 모델
 *
 * 관리자 패널의 메뉴 시스템을 관리하는 모델입니다.
 * 계층적 메뉴 구조를 지원하며, 라우트 기반 URL 생성과 외부 링크를 처리합니다.
 *
 * @property int $id
 * @property string $name 메뉴 이름
 * @property string|null $route_name 라우트 이름
 * @property array|null $route_parameters 라우트 파라미터
 * @property string|null $url 직접 URL
 * @property string|null $target 링크 타겟 (_blank, _self 등)
 * @property int|null $category_id 메뉴 카테고리 ID
 * @property int|null $parent_id 상위 메뉴 ID
 * @property bool $is_active 활성화 여부
 * @property int $sort_order 정렬 순서
 * @property string|null $icon 아이콘 클래스
 * @property string|null $description 설명
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class Menu extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * 대량 할당 가능한 속성들
     */
    protected $fillable = [
        'name',
        'route_name',
        'route_parameters',
        'url',
        'target',
        'category_id',
        'parent_id',
        'is_active',
        'sort_order',
        'icon',
        'description',
    ];

    /**
     * 속성 타입 캐스팅
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'category_id' => 'integer',
        'parent_id' => 'integer',
        'route_parameters' => 'array',
    ];

    // ============================================================================
    // 관계 메서드들
    // ============================================================================

    /**
     * 메뉴 카테고리와의 관계 (N:1)
     */
    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    /**
     * 상위 메뉴와의 관계 (N:1)
     */
    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    /**
     * 하위 메뉴들과의 관계 (1:N)
     */
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id');
    }

    // ============================================================================
    // 쿼리 스코프들
    // ============================================================================

    /**
     * 활성화된 메뉴만 조회하는 스코프
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * 정렬 순서로 정렬하는 스코프
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * 최상위 메뉴만 조회하는 스코프
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    // ============================================================================
    // URL 관련 메서드들
    // ============================================================================

    /**
     * 메뉴의 실제 URL 생성
     *
     * 라우트 이름이 있으면 라우트 URL을 생성하고,
     * 없으면 직접 입력된 URL을 반환합니다.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        // 라우트 이름이 있는 경우 라우트 URL 생성
        if ($this->route_name) {
            try {
                $parameters = $this->getRouteParameters();

                return route($this->route_name, $parameters);
            } catch (RouteNotFoundException $e) {
                // 라우트가 존재하지 않는 경우 fallback
                Log::warning("Route not found: {$this->route_name}", [
                    'menu_id' => $this->id,
                    'menu_name' => $this->name,
                ]);

                return $this->getRawUrlAttribute();
            } catch (\Exception $e) {
                // 기타 예외는 로그 기록 후 fallback
                Log::error('Error generating route URL', [
                    'menu_id' => $this->id,
                    'route_name' => $this->route_name,
                    'error' => $e->getMessage(),
                ]);

                return $this->getRawUrlAttribute();
            }
        }

        // 기본 URL 반환
        return $this->getRawUrlAttribute();
    }

    /**
     * 외부 링크 여부 확인
     *
     * @return bool
     */
    public function getIsExternalAttribute()
    {
        $url = $this->getRawUrlAttribute();

        return $this->isExternalUrl($url);
    }

    /**
     * 원본 URL 속성 (무한 재귀 방지)
     *
     * @return string
     */
    public function getRawUrlAttribute()
    {
        return $this->attributes['url'] ?? '#';
    }

    /**
     * URL이 외부 링크인지 확인
     *
     * @param  string  $url  확인할 URL
     * @return bool
     */
    protected function isExternalUrl($url)
    {
        if (empty($url) || $url === '#') {
            return false;
        }

        // http:// 또는 https://로 시작하는 경우 외부 링크
        return preg_match('/^https?:\/\//', $url);
    }

    /**
     * 라우트 파라미터 파싱
     *
     * @return array
     */
    protected function getRouteParameters()
    {
        if (empty($this->route_parameters)) {
            return [];
        }

        // JSON 문자열인 경우 파싱
        if (is_string($this->route_parameters)) {
            try {
                $decoded = json_decode($this->route_parameters, true);

                return is_array($decoded) ? $decoded : [];
            } catch (\JsonException $e) {
                Log::warning('Invalid JSON in route_parameters', [
                    'menu_id' => $this->id,
                    'route_parameters' => $this->route_parameters,
                ]);

                return [];
            }
        }

        // 이미 배열인 경우 그대로 반환
        return is_array($this->route_parameters) ? $this->route_parameters : [];
    }

    // ============================================================================
    // 유틸리티 메서드들
    // ============================================================================

    /**
     * 현재 페이지가 이 메뉴인지 확인
     *
     * @return bool
     */
    public function isCurrentPage()
    {
        // 라우트 이름이 있는 경우 라우트 비교
        if ($this->route_name) {
            return request()->routeIs($this->route_name);
        }

        // 직접 URL이 있는 경우 URL 비교
        if ($this->url && $this->url !== '#') {
            $currentPath = request()->path();
            $menuPath = ltrim($this->url, '/');

            // 정확한 경로 매칭 또는 하위 경로 매칭
            return $currentPath === $menuPath || str_starts_with($currentPath, $menuPath.'/');
        }

        return false;
    }
}
