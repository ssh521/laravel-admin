<?php

namespace Ssh521\LaravelAdmin\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Ssh521\LaravelAdmin\Models\AdminUser;
use Ssh521\LaravelAdmin\Tests\TestCase;

class UserSortingTest extends TestCase
{
    public function test_users_index_uses_sort_query_parameters(): void
    {
        config()->set('laravel-admin.user_model', SortableUser::class);

        Schema::create('sortable_users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
        });

        $guard = config('laravel-admin.auth.guard', 'laravel_admin');
        $superAdminRole = Role::query()
            ->where('name', config('laravel-admin.super_admin_role', 'Super Admin'))
            ->where('guard_name', $guard)
            ->firstOrFail();

        $admin = AdminUser::query()->forceCreate([
            'name' => 'User Sorting Super Admin',
            'email' => 'user-sorting-super-admin@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);
        $admin->assignRole($superAdminRole);

        SortableUser::query()->create([
            'name' => 'Alpha User',
            'email' => 'alpha-user@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);

        SortableUser::query()->create([
            'name' => 'Zulu User',
            'email' => 'zulu-user@example.com',
            'email_verified_at' => now(),
            'password' => 'password',
        ]);

        $this->actingAs($admin, $guard)
            ->get(route('admin.users.index', [
                'sortField' => 'name',
                'sortDirection' => 'asc',
            ]))
            ->assertOk()
            ->assertSeeInOrder(['Alpha User', 'Zulu User']);

        $this->actingAs($admin, $guard)
            ->get(route('admin.users.index', [
                'sortField' => 'name',
                'sortDirection' => 'desc',
            ]))
            ->assertOk()
            ->assertSeeInOrder(['Zulu User', 'Alpha User']);
    }
}

class SortableUser extends Model
{
    protected $table = 'sortable_users';

    protected $guarded = [];
}
