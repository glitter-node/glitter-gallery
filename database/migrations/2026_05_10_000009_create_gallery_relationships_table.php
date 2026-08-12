<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_relationships', function (Blueprint $table) {
            $table->id()->comment('갤러리 관계 ID');
            $table->string('type', 60)->comment('관계 유형');
            $table->string('source_type', 60)->comment('출발 노드 유형');
            $table->unsignedBigInteger('source_id')->comment('출발 노드 ID');
            $table->string('target_type', 60)->comment('도착 노드 유형');
            $table->unsignedBigInteger('target_id')->comment('도착 노드 ID');
            $table->string('role', 80)->nullable()->comment('관계 역할');
            $table->mediumText('note')->nullable()->comment('관계 편집 노트 다국어 JSON');
            $table->string('confidence', 30)->default('needs_review')->comment('관계 신뢰 상태');
            $table->unsignedInteger('sort_order')->default(0)->comment('정렬 순서');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('생성자 사용자 ID');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->comment('수정자 사용자 ID');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['type', 'source_type', 'source_id', 'target_type', 'target_id'], 'gallery_relationships_edge_unique');
            $table->index(['source_type', 'source_id'], 'gallery_relationships_source_index');
            $table->index(['target_type', 'target_id'], 'gallery_relationships_target_index');
            $table->index(['type', 'confidence', 'sort_order'], 'gallery_relationships_type_confidence_sort_index');
            $table->index('deleted_at', 'gallery_relationships_deleted_at_index');
        });

        if (DB::getDriverName() === 'mysql') {
            Schema::table('gallery_relationships', function (Blueprint $table) {
                $table->comment('갤러리 타입 관계 그래프');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_relationships');
    }
};
