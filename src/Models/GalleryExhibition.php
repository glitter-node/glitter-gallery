<?php

namespace Modules\Glitter\Gallery\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Glitter\Gallery\Enums\GalleryPublicationStatus;
use Modules\Glitter\Gallery\Enums\GalleryVisibilityState;

class GalleryExhibition extends Model
{
    use SoftDeletes;

    protected $table = 'gallery_exhibitions';

    protected $fillable = [
        'slug',
        'title',
        'subtitle',
        'summary',
        'period_label',
        'starts_at',
        'ends_at',
        'location',
        'curator_note',
        'featured_image',
        'is_featured',
        'status',
        'visibility_state',
        'published_at',
        'sort_order',
        'meta',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'subtitle' => 'array',
            'summary' => 'array',
            'period_label' => 'array',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'location' => 'array',
            'curator_note' => 'array',
            'featured_image' => 'array',
            'is_featured' => 'boolean',
            'status' => GalleryPublicationStatus::class,
            'visibility_state' => GalleryVisibilityState::class,
            'published_at' => 'datetime',
            'sort_order' => 'integer',
            'meta' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function sections(): HasMany
    {
        return $this->hasMany(GalleryExhibitionSection::class, 'exhibition_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GalleryItem::class, 'exhibition_id');
    }

    public function primaryRecords(): HasMany
    {
        return $this->hasMany(GalleryRecord::class, 'primary_exhibition_id');
    }

    public function primaryArchiveMaterials(): HasMany
    {
        return $this->hasMany(GalleryArchiveMaterial::class, 'primary_exhibition_id');
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

    public function scopeVisiblePublic(Builder $query): Builder
    {
        return $query->whereIn('visibility_state', [
            GalleryVisibilityState::Public->value,
            GalleryVisibilityState::Unlisted->value,
        ]);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('published_at')->orderByDesc('id');
    }
}
