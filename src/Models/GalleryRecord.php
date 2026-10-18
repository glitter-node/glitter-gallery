<?php

namespace Modules\Glitter\Gallery\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Glitter\Gallery\Enums\GalleryPublicationStatus;
use Modules\Glitter\Gallery\Enums\GalleryRecordType;
use Modules\Glitter\Gallery\Enums\GalleryVisibilityState;

class GalleryRecord extends Model
{
    use SoftDeletes;

    protected $table = 'gallery_records';

    protected $fillable = [
        'slug',
        'type',
        'title',
        'subtitle',
        'author_name',
        'recorded_at',
        'summary',
        'body_sections',
        'primary_exhibition_id',
        'status',
        'visibility_state',
        'published_at',
        'relationship_state',
        'meta',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => GalleryRecordType::class,
            'title' => 'array',
            'subtitle' => 'array',
            'author_name' => 'array',
            'recorded_at' => 'datetime',
            'summary' => 'array',
            'body_sections' => 'array',
            'status' => GalleryPublicationStatus::class,
            'visibility_state' => GalleryVisibilityState::class,
            'published_at' => 'datetime',
            'meta' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function primaryExhibition(): BelongsTo
    {
        return $this->belongsTo(GalleryExhibition::class, 'primary_exhibition_id');
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(GalleryTag::class, 'taggable', 'gallery_taggables', 'taggable_id', 'tag_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', GalleryPublicationStatus::Published->value);
    }

    public function scopeByType(Builder $query, GalleryRecordType|string $type): Builder
    {
        return $query->where('type', $type instanceof GalleryRecordType ? $type->value : $type);
    }
}
