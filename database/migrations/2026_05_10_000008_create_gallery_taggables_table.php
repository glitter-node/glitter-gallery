<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_taggables', function (Blueprint $table) {
            $table->id()->comment('갤러리 태그 연결 ID');
            $table->foreignId('tag_id')->constrained('gallery_tags')->restrictOnDelete()->comment('갤러리 태그 ID');
            $table->string('taggable_type', 120)->comment('태그 대상 모델 유형');
            $table->unsignedBigInteger('taggable_id')->comment('태그 대상 ID');
            $table->timestamp('created_at')->nullable()->comment('태그 연결 일시');

            $table->unique(['tag_id', 'taggable_type', 'taggable_id'], 'gallery_taggables_unique');
            $table->index(['taggable_type', 'taggable_id'], 'gallery_taggables_target_index');
        });

        if (DB::getDriverName() === 'mysql') {
            Schema::table('gallery_taggables', function (Blueprint $table) {
                $table->comment('갤러리 다형 태그 연결');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_taggables');
    }
};
