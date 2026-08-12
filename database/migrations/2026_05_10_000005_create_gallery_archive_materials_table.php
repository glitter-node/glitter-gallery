<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_archive_materials', function (Blueprint $table) {
            $table->id()->comment('갤러리 아카이브 자료 ID');
            $table->string('slug', 160)->comment('분류 내 식별 슬러그');
            $table->string('category', 60)->comment('아카이브 분류');
            $table->string('type', 60)->comment('아카이브 자료 유형');
            $table->text('title')->comment('자료 제목 다국어 JSON');
            $table->text('subtitle')->nullable()->comment('자료 부제 다국어 JSON');
            $table->string('date_label', 160)->nullable()->comment('자료 날짜 표시');
            $table->text('source')->nullable()->comment('출처 다국어 JSON');
            $table->text('rights')->nullable()->comment('권리 정보 다국어 JSON');
            $table->text('format')->nullable()->comment('형식 정보 다국어 JSON');
            $table->string('identifier', 160)->nullable()->comment('기관 식별자');
            $table->text('media')->nullable()->comment('미디어 또는 문서 슬롯 JSON');
            $table->mediumText('summary')->nullable()->comment('자료 요약 다국어 JSON');
            $table->longText('description')->nullable()->comment('자료 설명 다국어 JSON');
            $table->foreignId('primary_exhibition_id')->nullable()->constrained('gallery_exhibitions')->nullOnDelete()->comment('주 전시 ID');
            $table->string('status', 30)->default('draft')->comment('발행 상태');
            $table->string('visibility_state', 30)->default('private')->comment('공개 범위 상태');
            $table->timestamp('published_at')->nullable()->comment('발행 일시');
            $table->string('relationship_state', 30)->default('incomplete')->comment('관계 연결 검토 상태');
            $table->mediumText('meta')->nullable()->comment('확장 메타데이터 JSON');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('생성자 사용자 ID');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->comment('수정자 사용자 ID');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['category', 'slug'], 'gallery_archive_materials_category_slug_unique');
            $table->index('identifier', 'gallery_archive_materials_identifier_index');
            $table->index('category', 'gallery_archive_materials_category_index');
            $table->index('type', 'gallery_archive_materials_type_index');
            $table->index('primary_exhibition_id', 'gallery_archive_materials_primary_exhibition_index');
            $table->index('status', 'gallery_archive_materials_status_index');
            $table->index('visibility_state', 'gallery_archive_materials_visibility_index');
            $table->index('published_at', 'gallery_archive_materials_published_at_index');
            $table->index('relationship_state', 'gallery_archive_materials_relationship_state_index');
            $table->index('deleted_at', 'gallery_archive_materials_deleted_at_index');
        });

        if (DB::getDriverName() === 'mysql') {
            Schema::table('gallery_archive_materials', function (Blueprint $table) {
                $table->comment('갤러리 아카이브 자료');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_archive_materials');
    }
};
