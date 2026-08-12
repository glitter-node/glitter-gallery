<?php

namespace Modules\Glitter\Gallery\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Glitter\Gallery\Models\GalleryRecord;
use Modules\Glitter\Gallery\Repositories\Concerns\AppliesPublicGalleryFilters;
use Modules\Glitter\Gallery\Repositories\Contracts\RecordRepositoryInterface;

class RecordRepository implements RecordRepositoryInterface
{
    use AppliesPublicGalleryFilters;

    public function getPublicRecent(int $limit = 3): Collection
    {
        return $this->publicQuery(
            GalleryRecord::query()->with(['primaryExhibition', 'tags'])
        )
            ->orderByDesc('recorded_at')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function getPublicPaginated(array $filters = []): LengthAwarePaginator
    {
        $query = $this->publicQuery(
            GalleryRecord::query()->with(['primaryExhibition', 'tags'])
        );

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query
            ->orderByDesc('recorded_at')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($this->perPage($filters));
    }

    public function findPublicBySlug(string $slug): ?GalleryRecord
    {
        return $this->publicQuery(
            GalleryRecord::query()->with(['primaryExhibition', 'tags'])
        )
            ->where('slug', $slug)
            ->first();
    }
}
