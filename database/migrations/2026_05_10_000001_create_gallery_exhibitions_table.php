<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_exhibitions', function (Blueprint $table) {
            $table->id()->comment('갤러리 전시 ID');
            $table->string('slug', 160)->unique()->comment('공개 식별 슬러그');
            $table->text('title')->comment('전시 제목 다국어 JSON');
            $table->text('subtitle')->nullable()->comment('전시 부제 다국어 JSON');
            $table->mediumText('summary')->nullable()->comment('전시 요약 다국어 JSON');
            $table->text('period_label')->nullable()->comment('전시 기간 표시 다국어 JSON');
            $table->timestamp('starts_at')->nullable()->comment('전시 시작 일시');
            $table->timestamp('ends_at')->nullable()->comment('전시 종료 일시');
            $table->text('location')->nullable()->comment('전시 장소 다국어 JSON');
            $table->longText('curator_note')->nullable()->comment('큐레이터 노트 다국어 JSON');
            $table->text('featured_image')->nullable()->comment('대표 이미지 슬롯 JSON');
            $table->boolean('is_featured')->default(false)->comment('대표 전시 여부');
            $table->string('status', 30)->default('draft')->comment('발행 상태');
            $table->string('visibility_state', 30)->default('private')->comment('공개 범위 상태');
            $table->timestamp('published_at')->nullable()->comment('발행 일시');
            $table->unsignedInteger('sort_order')->default(0)->comment('정렬 순서');
            $table->mediumText('meta')->nullable()->comment('확장 메타데이터 JSON');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('생성자 사용자 ID');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->comment('수정자 사용자 ID');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status', 'gallery_exhibitions_status_index');
            $table->index('visibility_state', 'gallery_exhibitions_visibility_index');
            $table->index('published_at', 'gallery_exhibitions_published_at_index');
            $table->index('is_featured', 'gallery_exhibitions_featured_index');
            $table->index('sort_order', 'gallery_exhibitions_sort_order_index');
            $table->index(['starts_at', 'ends_at'], 'gallery_exhibitions_period_index');
            $table->index('deleted_at', 'gallery_exhibitions_deleted_at_index');
        });

        if (DB::getDriverName() === 'mysql') {
            Schema::table('gallery_exhibitions', function (Blueprint $table) {
                $table->comment('갤러리 전시 기본 정보');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_exhibitions');
    }
};
