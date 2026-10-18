<?php

namespace Modules\Glitter\Gallery\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Modules\Glitter\Gallery\Models\GalleryArchiveMaterial;
use Modules\Glitter\Gallery\Repositories\Concerns\AppliesPublicGalleryFilters;
use Modules\Glitter\Gallery\Repositories\Contracts\ArchiveMaterialRepositoryInterface;

class ArchiveMaterialRepository implements ArchiveMaterialRepositoryInterface
{
    use AppliesPublicGalleryFilters;

    public function getPublicRecent(int $limit = 3): Collection
    {
        return $this->publicQuery(
            GalleryArchiveMaterial::query()->with(['primaryExhibition', 'tags'])
        )
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function getPublicPaginated(array $filters = []): LengthAwarePaginator
    {
        return $this->publicQuery(
            GalleryArchiveMaterial::query()->with(['primaryExhibition', 'tags'])
        )
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($this->perPage($filters));
    }

    public function getPublicByCategory(string $category, array $filters = []): LengthAwarePaginator
    {
        $query = $this->publicQuery(
            GalleryArchiveMaterial::query()->with(['primaryExhibition', 'tags'])
        )
            ->where('category', $category);

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        return $query
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($this->perPage($filters));
    }

    public function findPublicByCategoryAndSlug(string $category, string $slug): ?GalleryArchiveMaterial
    {
        return $this->publicQuery(
            GalleryArchiveMaterial::query()->with(['primaryExhibition', 'tags'])
        )
            ->where('category', $category)
            ->where('slug', $slug)
            ->first();
    }

    public function publicCategoryCounts(): array
    {
        return $this->publicQuery(GalleryArchiveMaterial::query())
            ->selectRaw('category, count(*) as aggregate')
            ->groupBy('category')
            ->pluck('aggregate', 'category')
            ->map(fn ($count): int => (int) $count)
            ->all();
    }
}
