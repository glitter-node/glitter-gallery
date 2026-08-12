<?php

namespace Modules\Glitter\Gallery\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Glitter\Gallery\Enums\GalleryRelationshipType;

class GalleryRelationship extends Model
{
    use SoftDeletes;

    protected $table = 'gallery_relationships';

    protected $fillable = [
        'type',
        'source_type',
        'source_id',
        'target_type',
        'target_id',
        'role',
        'note',
        'confidence',
        'sort_order',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => GalleryRelationshipType::class,
            'source_id' => 'integer',
            'target_id' => 'integer',
            'note' => 'array',
            'sort_order' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeBySource(Builder $query, string $type, int $id): Builder
    {
        return $query->where('source_type', $type)->where('source_id', $id);
    }

    public function scopeByTarget(Builder $query, string $type, int $id): Builder
    {
        return $query->where('target_type', $type)->where('target_id', $id);
    }

    public function scopeByType(Builder $query, GalleryRelationshipType|string $type): Builder
    {
        return $query->where('type', $type instanceof GalleryRelationshipType ? $type->value : $type);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
