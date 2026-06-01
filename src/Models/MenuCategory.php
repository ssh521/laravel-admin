<?php

namespace Ssh521\LaravelAdmin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'menu_count',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'menu_count' => 'integer',
        'sort_order' => 'integer',
    ];

    // 메뉴와의 관계 (1:N)
    public function menus()
    {
        return $this->hasMany(Menu::class, 'category_id');
    }

    // 활성화된 메뉴만 조회
    public function activeMenus()
    {
        return $this->hasMany(Menu::class, 'category_id')->where('is_active', true);
    }

    // 활성화된 카테고리만 조회하는 스코프
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // 정렬 순서로 정렬하는 스코프
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * roles와의 N:M 관계
     */
    public function roles()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'menu_category_role', 'menu_category_id', 'role_id');
    }

    /**
     * 해당 Role이 이 카테고리에 접근 가능한지(허용 여부만 체크)
     *
     * @param  int|Role  $role
     * @return bool
     */
    public function canAccessByRole($role)
    {
        $roleId = $role instanceof \Spatie\Permission\Models\Role ? $role->id : $role;

        return $this->roles()->where('role_id', $roleId)->exists();
    }
}
