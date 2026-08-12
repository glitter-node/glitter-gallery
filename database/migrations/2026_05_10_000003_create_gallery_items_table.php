<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id()->comment('갤러리 아이템 ID');
            $table->foreignId('exhibition_id')->constrained('gallery_exhibitions')->restrictOnDelete()->comment('갤러리 전시 ID');
            $table->foreignId('section_id')->nullable()->constrained('gallery_exhibition_sections')->nullOnDelete()->comment('전시 섹션 ID');
            $table->string('slug', 160)->unique()->comment('공개 식별 슬러그');
            $table->string('type', 40)->comment('아이템 유형');
            $table->text('title')->comment('아이템 제목 다국어 JSON');
            $table->text('subtitle')->nullable()->comment('아이템 부제 다국어 JSON');
            $table->text('artist_name')->nullable()->comment('작가 또는 대상명 다국어 JSON');
            $table->string('year_label', 80)->nullable()->comment('제작 또는 관련 연도 표시');
            $table->text('medium')->nullable()->comment('매체 정보 다국어 JSON');
            $table->string('dimensions', 160)->nullable()->comment('작품 크기 표시');
            $table->text('collection_name')->nullable()->comment('소장처명 다국어 JSON');
            $table->text('credit_line')->nullable()->comment('크레딧 문구 다국어 JSON');
            $table->string('archive_category', 60)->nullable()->comment('아카이브 분류');
            $table->text('thumbnail_media')->nullable()->comment('썸네일 미디어 슬롯 JSON');
            $table->text('detail_media')->nullable()->comment('상세 미디어 슬롯 JSON');
            $table->mediumText('summary')->nullable()->comment('아이템 요약 다국어 JSON');
            $table->longText('curator_note')->nullable()->comment('큐레이터 노트 다국어 JSON');
            $table->string('status', 30)->default('draft')->comment('발행 상태');
            $table->string('visibility_state', 30)->default('private')->comment('공개 범위 상태');
            $table->timestamp('published_at')->nullable()->comment('발행 일시');
            $table->unsignedInteger('sort_order')->default(0)->comment('정렬 순서');
            $table->string('relationship_state', 30)->default('incomplete')->comment('관계 연결 검토 상태');
            $table->mediumText('meta')->nullable()->comment('확장 메타데이터 JSON');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('생성자 사용자 ID');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->comment('수정자 사용자 ID');
            $table->timestamps();
            $table->softDeletes();

            $table->index('exhibition_id', 'gallery_items_exhibition_index');
            $table->index('section_id', 'gallery_items_section_index');
            $table->index('type', 'gallery_items_type_index');
            $table->index('archive_category', 'gallery_items_archive_category_index');
            $table->index('status', 'gallery_items_status_index');
            $table->index('visibility_state', 'gallery_items_visibility_index');
            $table->index('published_at', 'gallery_items_published_at_index');
            $table->index('relationship_state', 'gallery_items_relationship_state_index');
            $table->index('sort_order', 'gallery_items_sort_order_index');
            $table->index('deleted_at', 'gallery_items_deleted_at_index');
        });

        if (DB::getDriverName() === 'mysql') {
            Schema::table('gallery_items', function (Blueprint $table) {
                $table->comment('갤러리 작품 및 오브젝트 카탈로그');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
    }
};
