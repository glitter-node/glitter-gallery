<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_collections', function (Blueprint $table) {
            $table->id()->comment('갤러리 컬렉션 ID');
            $table->string('slug', 160)->unique()->comment('공개 식별 슬러그');
            $table->text('title')->comment('컬렉션 제목 다국어 JSON');
            $table->text('subtitle')->nullable()->comment('컬렉션 부제 다국어 JSON');
            $table->mediumText('summary')->nullable()->comment('컬렉션 요약 다국어 JSON');
            $table->longText('description')->nullable()->comment('컬렉션 설명 다국어 JSON');
            $table->text('cover_media')->nullable()->comment('표지 미디어 슬롯 JSON');
            $table->string('status', 30)->default('draft')->comment('발행 상태');
            $table->string('visibility_state', 30)->default('private')->comment('공개 범위 상태');
            $table->timestamp('published_at')->nullable()->comment('발행 일시');
            $table->unsignedInteger('sort_order')->default(0)->comment('정렬 순서');
            $table->mediumText('meta')->nullable()->comment('확장 메타데이터 JSON');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('생성자 사용자 ID');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->comment('수정자 사용자 ID');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status', 'gallery_collections_status_index');
            $table->index('visibility_state', 'gallery_collections_visibility_index');
            $table->index('published_at', 'gallery_collections_published_at_index');
            $table->index('sort_order', 'gallery_collections_sort_order_index');
            $table->index('deleted_at', 'gallery_collections_deleted_at_index');
        });

        if (DB::getDriverName() === 'mysql') {
            Schema::table('gallery_collections', function (Blueprint $table) {
                $table->comment('갤러리 컬렉션');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_collections');
    }
};
