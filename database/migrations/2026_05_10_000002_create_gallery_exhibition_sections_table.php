<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_exhibition_sections', function (Blueprint $table) {
            $table->id()->comment('전시 섹션 ID');
            $table->foreignId('exhibition_id')->constrained('gallery_exhibitions')->restrictOnDelete()->comment('갤러리 전시 ID');
            $table->string('slug', 160)->comment('전시 내 섹션 슬러그');
            $table->text('title')->comment('섹션 제목 다국어 JSON');
            $table->mediumText('summary')->nullable()->comment('섹션 요약 다국어 JSON');
            $table->longText('body')->nullable()->comment('섹션 본문 다국어 JSON');
            $table->string('insertion_point', 80)->nullable()->comment('콘텐츠 삽입 위치');
            $table->string('status', 30)->default('draft')->comment('발행 상태');
            $table->string('visibility_state', 30)->default('private')->comment('공개 범위 상태');
            $table->timestamp('published_at')->nullable()->comment('발행 일시');
            $table->unsignedInteger('sort_order')->default(0)->comment('정렬 순서');
            $table->mediumText('meta')->nullable()->comment('확장 메타데이터 JSON');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['exhibition_id', 'slug'], 'gallery_sections_exhibition_slug_unique');
            $table->index(['exhibition_id', 'sort_order'], 'gallery_sections_exhibition_sort_index');
            $table->index('status', 'gallery_sections_status_index');
            $table->index('visibility_state', 'gallery_sections_visibility_index');
            $table->index('published_at', 'gallery_sections_published_at_index');
            $table->index('deleted_at', 'gallery_sections_deleted_at_index');
        });

        if (DB::getDriverName() === 'mysql') {
            Schema::table('gallery_exhibition_sections', function (Blueprint $table) {
                $table->comment('갤러리 전시 섹션');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_exhibition_sections');
    }
};
