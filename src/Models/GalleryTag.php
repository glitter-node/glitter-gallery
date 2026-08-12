<?php

namespace Modules\Glitter\Gallery\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphedByMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GalleryTag extends Model
{
    use SoftDeletes;

    protected $table = 'gallery_tags';

    protected $fillable = [
        'slug',
        'name',
        'description',
        'taxonomy_group',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'sort_order' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    public function exhibitions(): MorphedByMany
    {
        return $this->morphedByMany(GalleryExhibition::class, 'taggable', 'gallery_taggables', 'tag_id', 'taggable_id');
    }

    public function items(): MorphedByMany
    {
        return $this->morphedByMany(GalleryItem::class, 'taggable', 'gallery_taggables', 'tag_id', 'taggable_id');
    }

    public function records(): MorphedByMany
    {
        return $this->morphedByMany(GalleryRecord::class, 'taggable', 'gallery_taggables', 'tag_id', 'taggable_id');
    }

    public function archiveMaterials(): MorphedByMany
    {
        return $this->morphedByMany(GalleryArchiveMaterial::class, 'taggable', 'gallery_taggables', 'tag_id', 'taggable_id');
    }

    public function collections(): MorphedByMany
    {
        return $this->morphedByMany(GalleryCollection::class, 'taggable', 'gallery_taggables', 'tag_id', 'taggable_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeByGroup(Builder $query, string $group): Builder
    {
        return $query->where('taxonomy_group', $group);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('slug');
    }
}
