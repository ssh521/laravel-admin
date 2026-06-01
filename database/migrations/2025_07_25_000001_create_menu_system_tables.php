<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. 메뉴 카테고리 테이블 생성
        Schema::create('menu_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->nullable()->comment('카테고리명');
            $table->integer('menu_count')->nullable()->comment('메뉴 개수');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->integer('sort_order')->default(0)->comment('정렬 순서');
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. 메뉴 테이블 생성 (라우트 기반)
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->nullable()->comment('메뉴명');
            $table->string('route_name', 100)->nullable()->comment('라우트 이름 (예: users.index)');
            $table->string('route_parameters', 500)->nullable()->comment('라우트 파라미터 (JSON)');
            $table->string('url', 200)->nullable()->comment('직접 URL (route_name이 없을 때 사용)');
            $table->string('target', 20)->default('_self')->comment('링크 타겟 (_self, _blank, _parent, _top)');
            $table->unsignedBigInteger('category_id')->nullable()->comment('카테고리 ID');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('상위 메뉴 ID');
            $table->boolean('is_active')->default(true)->comment('활성화 여부');
            $table->integer('sort_order')->default(0)->comment('정렬 순서');
            $table->string('icon', 100)->nullable()->comment('아이콘');
            $table->text('description')->nullable()->comment('설명');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('menu_categories')->onDelete('set null');
            $table->foreign('parent_id')->references('id')->on('menus')->onDelete('set null');
        });

        // MenuCategory <-> Role N:M pivot 테이블 생성
        Schema::create('menu_category_role', function (Blueprint $table) {
            $table->unsignedBigInteger('menu_category_id');
            $table->unsignedBigInteger('role_id');
            $table->foreign('menu_category_id')->references('id')->on('menu_categories')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->unique(['menu_category_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_category_role');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('menu_categories');
    }
};
