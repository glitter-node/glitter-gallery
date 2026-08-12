<?php

namespace Modules\Glitter\Gallery\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Glitter\Gallery\Enums\GalleryPublicationStatus;
use Modules\Glitter\Gallery\Enums\GalleryVisibilityState;

class GalleryExhibitionSection extends Model
{
    use SoftDeletes;

    protected $table = 'gallery_exhibition_sections';

    protected $fillable = [
        'exhibition_id',
        'slug',
        'title',
        'summary',
        'body',
        'insertion_point',
        'status',
        'visibility_state',
        'published_at',
        'sort_order',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'summary' => 'array',
            'body' => 'array',
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

    public function items(): HasMany
    {
        return $this->hasMany(GalleryItem::class, 'section_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', GalleryPublicationStatus::Published->value);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
