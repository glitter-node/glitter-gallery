<?php

namespace Modules\Glitter\Gallery\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Glitter\Gallery\Enums\GalleryArchiveCategory;
use Modules\Glitter\Gallery\Enums\GalleryItemType;
use Modules\Glitter\Gallery\Enums\GalleryPublicationStatus;
use Modules\Glitter\Gallery\Enums\GalleryVisibilityState;

class GalleryItem extends Model
{
    use SoftDeletes;

    protected $table = 'gallery_items';

    protected $fillable = [
        'exhibition_id',
        'section_id',
        'slug',
        'type',
        'title',
        'subtitle',
        'artist_name',
        'year_label',
        'medium',
        'dimensions',
        'collection_name',
        'credit_line',
        'archive_category',
        'thumbnail_media',
        'detail_media',
        'summary',
        'curator_note',
        'status',
        'visibility_state',
        'published_at',
        'sort_order',
        'relationship_state',
        'meta',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => GalleryItemType::class,
            'title' => 'array',
            'subtitle' => 'array',
            'artist_name' => 'array',
            'medium' => 'array',
            'collection_name' => 'array',
            'credit_line' => 'array',
            'archive_category' => GalleryArchiveCategory::class,
            'thumbnail_media' => 'array',
            'detail_media' => 'array',
            'summary' => 'array',
            'curator_note' => 'array',
            'status' => GalleryPublicationStatus::class,
            'visibility_state' => GalleryVisibilityState::class,
            'published_at' => 'datetime',
            'sort_order' => 'integer',
            'meta' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function exhibition(): BelongsTo
    {
        return $this->belongsTo(GalleryExhibition::class, 'exhibition_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(GalleryExhibitionSection::class, 'section_id');
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

    public function scopeByExhibition(Builder $query, int $exhibitionId): Builder
    {
        return $query->where('exhibition_id', $exhibitionId);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
