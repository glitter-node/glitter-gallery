<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_records', function (Blueprint $table) {
            $table->id()->comment('갤러리 기록 ID');
            $table->string('slug', 160)->unique()->comment('공개 식별 슬러그');
            $table->string('type', 40)->comment('기록 유형');
            $table->text('title')->comment('기록 제목 다국어 JSON');
            $table->text('subtitle')->nullable()->comment('기록 부제 다국어 JSON');
            $table->text('author_name')->nullable()->comment('작성자명 다국어 JSON');
            $table->timestamp('recorded_at')->nullable()->comment('기록 일시');
            $table->mediumText('summary')->nullable()->comment('기록 요약 다국어 JSON');
            $table->longText('body_sections')->comment('본문 섹션 배열 JSON');
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

            $table->index('type', 'gallery_records_type_index');
            $table->index('recorded_at', 'gallery_records_recorded_at_index');
            $table->index('primary_exhibition_id', 'gallery_records_primary_exhibition_index');
            $table->index('status', 'gallery_records_status_index');
            $table->index('visibility_state', 'gallery_records_visibility_index');
            $table->index('published_at', 'gallery_records_published_at_index');
            $table->index('relationship_state', 'gallery_records_relationship_state_index');
            $table->index('deleted_at', 'gallery_records_deleted_at_index');
        });

        if (DB::getDriverName() === 'mysql') {
            Schema::table('gallery_records', function (Blueprint $table) {
                $table->comment('갤러리 큐레이터 및 현장 기록');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_records');
    }
};
