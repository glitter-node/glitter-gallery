<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_tags', function (Blueprint $table) {
            $table->id()->comment('갤러리 태그 ID');
            $table->string('slug', 120)->unique()->comment('태그 식별 슬러그');
            $table->text('name')->comment('태그명 다국어 JSON');
            $table->mediumText('description')->nullable()->comment('태그 설명 다국어 JSON');
            $table->string('taxonomy_group', 80)->nullable()->comment('분류 그룹');
            $table->unsignedInteger('sort_order')->default(0)->comment('정렬 순서');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->comment('생성자 사용자 ID');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->comment('수정자 사용자 ID');
            $table->timestamps();
            $table->softDeletes();

            $table->index('taxonomy_group', 'gallery_tags_taxonomy_group_index');
            $table->index('sort_order', 'gallery_tags_sort_order_index');
            $table->index('deleted_at', 'gallery_tags_deleted_at_index');
        });

        if (DB::getDriverName() === 'mysql') {
            Schema::table('gallery_tags', function (Blueprint $table) {
                $table->comment('갤러리 태그');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_tags');
    }
};
