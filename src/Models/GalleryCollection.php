<?php

namespace Modules\Glitter\Gallery\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Glitter\Gallery\Enums\GalleryPublicationStatus;
use Modules\Glitter\Gallery\Enums\GalleryVisibilityState;

class GalleryCollection extends Model
{
    use SoftDeletes;

    protected $table = 'gallery_collections';

    protected $fillable = [
        'slug',
        'title',
        'subtitle',
        'summary',
        'description',
        'cover_media',
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
            'description' => 'array',
            'cover_media' => 'array',
            'status' => GalleryPublicationStatus::class,
            'visibility_state' => GalleryVisibilityState::class,
            'published_at' => 'datetime',
            'sort_order' => 'integer',
            'meta' => 'array',
            'deleted_at' => 'datetime',
        ];
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

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('published_at')->orderByDesc('id');
    }
}
